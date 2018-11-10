<?php
require "database.php";
class Getter
{
    private $database;

    /**
     *Konstruktor przy tworzeniu obiektu łączy się z bazą
     */
    public function __construct()
    {
        $this->database = connectToDataBase();
    }

    /**
     * Funkcja wybiera z bazy plik identyfikowany przez ID.
     */
    public function getFileById($ID)
    {
        try {

            $sql = "SELECT * FROM files WHERE ID=" . $ID;
            $stmt = $this->database->prepare($sql);
            $stmt->execute();
            $result = $sth->fetchAll();
            return $result;
        } catch (PDOException $e) {
            echo $channelError;
            exit;
        }
    }
    /**
     * Funkcja wybiera z bazy, dane o plikach o nazwie fileName.
     */
    public function getFileByName($fileName)
    {
        try {

            $sql = "SELECT * FROM files WHERE file_name=" . $fileName;
            $stmt = $this->database->prepare($sql);
            $stmt->execute();
            $result = $sth->fetchAll();
            return $result;
        } catch (PDOException $e) {
            echo $channelError;
            exit;
        }
    }

    /**
     * Funkcja wybiera z bazy, dane o jednym stawie identyfikowane przez ID.
     */
    public function getJointByID($ID)
    {
        try {

            $sql = "SELECT * FROM joints WHERE ID=" . $ID;
            $stmt = $this->database->prepare($sql);
            $stmt->execute();
            $result = $sth->fetchAll();
            return $result;
        } catch (PDOException $e) {
            echo $channelError;
            exit;
        }
    }
}
