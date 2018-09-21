<?php
require "database.php";

class Saver
{
    private $database;
    private $frames;

    public function __construct()
    {
        $this->database = connect();

        echo "wywołano konstruktor";
    }
    
    public function addChannelToDataBase($channels, $jointID, $i)
    {
        try {

            $data = [
                'X_position' => isset($channels["Xposition"]) ? $channels["Xposition"][$i] : null,
                'Z_position' => isset($channels["Zposition"]) ? $channels["Zposition"][$i] : null,
                'Y_position' => isset($channels["Yposition"]) ? $channels["Yposition"][$i] : null,
                'X_rotation' => isset($channels["Xrotation"]) ? $channels["Xrotation"][$i] : null,
                'Y_rotation' => isset($channels["Yrotation"]) ? $channels["Yrotation"][$i] : null,
                'Z_rotation' => isset($channels["Zrotation"]) ? $channels["Zrotation"][$i] : null,
                "joint_ID" => $jointID,
            ];
            $sql = "INSERT INTO channels (X_position,Y_position,Z_position,X_rotation,Y_rotation,Z_rotation,joint_ID)
            VALUES (:X_position,:Y_position,:Z_position,:X_rotation,:Y_rotation,:Z_rotation,:joint_ID)";

            $stmt = $this->database->prepare($sql);
            $stmt->execute($data);
        } catch (PDOException $e) {
            echo "Failed to get DB handle:s " . $e->getMessage() . "\n";
            exit;
        }

    }
    public function addJointsToDataBase($joints)
    {
        $fileID = $this->getFileID();
        foreach ($joints as $joint) {
            $this->addJointToDataBase($joint, $fileID);
            for ($i = 0; $i < $this->frames; $i++) {
                $this->addChannelToDataBase($joint->channels, $this->getJointID(), $i);
                # code...
            }
        }

    }
    public function addFileToDataBase($fileName, $framesNumber, $frameTimeNumber)
    {$this->frames = $framesNumber;
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
    public function getJointID()
    {
        $sql = 'SELECT ID FROM joints WHERE ID=( SELECT max(ID) FROM joints )';
        $jointID = $this->database->query($sql)->fetch()["ID"];
        return $jointID;
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
