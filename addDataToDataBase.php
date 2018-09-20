<?php
require "database.php";

class Saver
{
    private $database;

    public function __construct()
    {
        $this->database = connect();

        echo "wywołano konstruktor";
    }
    public function addChannelToDataBase($channel)
    {}
    public function addJointsToDataBase($joints)
    {
        $fileID = $this->getFileID();
        foreach ($joints as $joint) {
            $this->addJointToDataBase($joint,$fileID);
        }}
    public function addFileToDataBase($fileName, $framesNumber, $frameTimeNumber)
    {
        try {
            $data = [
                'file_name' => $fileName,
                'frames' => $framesNumber,
                'frame_time' => $frameTimeNumber,
            ];
            $sql = "INSERT INTO files (file_name, frames, frame_time) VALUES (:file_name, :frames,:frame_time)";

            $stmt = $this->database->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Failed to get DB handle:s " . $e->getMessage() . "\n";
            exit;
        }

    }

    public function getFileID()
    {
        $sql = 'SELECT ID FROM files WHERE ID=( SELECT max(ID) FROM files )';
        $fileID = $this->database->query($sql)->fetch()["ID"];
        return $fileID;

    }
    public function addJointToDataBase($joint, $fileID)
    {
        try {
            $data = [
                'name' => $joint->name,
                'offset_x' => $joint->offsetX,
                'offset_y' => $joint->offsetY,
                'offset_z' => $joint->offsetZ,
                'number_of_channels' => $joint->numberOfChannels,
                "file_ID" => $fileID,
            ];
            $sql = "INSERT INTO joints (name, offset_x, offset_y,offset_z,number_of_channels,file_ID) VALUES (:name, :offset_x, :offset_y,:offset_z,:number_of_channels,:file_ID)";

            $stmt = $this->database->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Failed to get DB handle:s " . $e->getMessage() . "\n";
            exit;
        }
    }
}
