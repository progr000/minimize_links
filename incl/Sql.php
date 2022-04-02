<?php
require_once 'SqliteDB.php';

class Sql extends SqliteDB
{
    /**
     * Create hash from any string
     * @param $str
     * @return string
     */
    private function getHash($str)
    {
        return hash('crc32b', $str);
    }

    /**
     * Find or create Short link for original link
     * @param $original_link
     * @return array
     */
    public function findOrCreateShortLink($original_link)
    {
        $original_link = SQLite3::escapeString($original_link);
        $hash = $this->getHash($original_link);
        $hash = SQLite3::escapeString($hash);
        $sql = $this->prepare("SELECT * FROM {$this->table} WHERE hash=:hash");
        $sql->bindValue(':hash', $hash, SQLITE3_TEXT);
        $result = $sql->execute()->fetchArray(SQLITE3_ASSOC);

        if (!is_array($result)) {
            $sql = $this->prepare("INSERT INTO {$this->table} (original_link, hash) VALUES (:original_link, :hash)");
            $sql->bindValue(':hash', $hash, SQLITE3_TEXT);
            $sql->bindValue(':original_link', $original_link, SQLITE3_TEXT);
            $sql->execute();

            $result = compact('original_link', 'hash');
        }

        return $result;
    }

    /**
     * Find real link by it hash
     * @param $hash
     * @return array|bool
     */
    public function getShortLink($hash)
    {
        $hash = SQLite3::escapeString($hash);
        $sql = $this->prepare("SELECT original_link FROM {$this->table} WHERE hash=:hash");
        $sql->bindValue(':hash', $hash, SQLITE3_TEXT);
        $result = $sql->execute()->fetchArray(SQLITE3_ASSOC);

        if (is_array($result))  {
            return $result;
        }

        return false;
    }
}
