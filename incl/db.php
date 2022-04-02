<?php
class sql extends SQLite3
{
    private $is_first_start = true;
    private $db_file = __DIR__ . '/mysqlitedb.db';
    private $table = 'links';

    /**
     * @inheritdoc
     */
    public function __construct()
    {
        $this->createDbFile();
        try {
            $this->open($this->db_file);
            /* create table and index if its first start */
            if ($this->is_first_start) {
                $this->createTable();
            }
        } catch (Exception $e) {
            die("Something going wrong. <br />{$e->getMessage()}");
        }
    }

    /**
     * Physically create DB file for sqlite engine
     */
    private function createDbFile()
    {
        /* if db-file already exist */
        if (file_exists($this->db_file)) {
            $this->is_first_start = false;
            return;
        }

        /* creating db-file */
        if (@touch($this->db_file)) {
            @chmod($this->db_file, 0666);
            $this->is_first_start = true;
        } else {
            die("Allow writing to the " . __DIR__ . " directory");
        }
    }

    /**
     * Create table in bd
     */
    private function createTable()
    {
        $this->exec("CREATE TABLE IF NOT EXISTS {$this->table} (original_link TEXT, hash TEXT NOT NULL)");
        $this->exec("CREATE UNIQUE INDEX IF NOT EXISTS {$this->table}_hash_idx ON {$this->table} (hash)");
    }

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
            $ins = $sql->execute();

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
