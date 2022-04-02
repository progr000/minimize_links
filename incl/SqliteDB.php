<?php
class SqliteDB extends SQLite3
{
    private $is_first_start = true;
    private $db_file = __DIR__ . '/../db/mysqlitedb.db';
    protected $table = 'links';

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
            die("Allow writing to the " . dirname($this->db_file) . " directory");
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
}
