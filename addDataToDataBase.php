<?php
require "database.php";
global $jointError, $channelError, $filesError;
class Saver
{

    private $database;
    private $frames;

    public function __construct()
    {
        $this->database = connectToDataBase();

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
            echo $channelError;
            exit;
        }

    }
    public function addJointsToDataBase($joints)
    {
        $fileID = $this->getFileID();
        $ID= $this->getJointID();
        foreach ($joints as $joint) {
            $ID+=1;
            $this->addJointToDataBase($joint, $fileID, $ID);
            // for ($i = 0; $i < $this->frames; $i++) {
            //     $this->addChannelToDataBase(isset($joint->channels)?$joint->channels:null, $this->getJointID(), $i);
                
            // }
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
            echo $filesError;
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
    public function addJointToDataBase($joint, $fileID,$ID)
    {
        try { // TODO
            $data = [
                'ID' => $ID,
                'name' => $joint->name,
                'offset_x' => $joint->offsetX,
                'offset_y' => $joint->offsetY,
                'offset_z' => $joint->offsetZ,
                'number_of_channels' => isset($joint->numberOfChannels) ? $joint->numberOfChannels : null,
                "file_ID" => $fileID,
            ];
            $sql = "INSERT INTO joints (ID, name, offset_x, offset_y,offset_z,number_of_channels,file_ID) VALUES (:ID, :name, :offset_x, :offset_y,:offset_z,:number_of_channels,:file_ID)";

            $stmt = $this->database->prepare($sql);
            $stmt->execute($data);
            if (isset($joint->channels))
            {
                $this->nowa_funkcja($joint->channels, $ID);
            
            };
        } catch (PDOException $e) {
            echo $e;
            exit;
        }
    }
    function nowa_funkcja($channels, $ID)
    { $values = "";
        
        for ($i = 0; $i < $this->frames; $i++) {
            $data = [   
                'X_position' => isset($channels["Xposition"]) ? $channels["Xposition"][$i] : "NULL",
                'Z_position' => isset($channels["Zposition"]) ? $channels["Zposition"][$i] : "NULL",
                'Y_position' => isset($channels["Yposition"]) ? $channels["Yposition"][$i] : "NULL",
                'X_rotation' => isset($channels["Xrotation"]) ? $channels["Xrotation"][$i] : "NULL",
                'Y_rotation' => isset($channels["Yrotation"]) ? $channels["Yrotation"][$i] : "NULL",
                'Z_rotation' => isset($channels["Zrotation"]) ? $channels["Zrotation"][$i] : "NULL",
                "joint_ID" => $ID,
            ];
              $values .="(".implode(", ",$data).")";
              $values .= $i < $this->frames-1?",":"";
            }
            try {
                $sql = "INSERT INTO channels (X_position,Y_position,Z_position,X_rotation,Y_rotation,Z_rotation,joint_ID)
                VALUES ".$values;
                $stmt = $this->database->prepare($sql);
                $stmt->execute();
            } catch (PDOException $e) {
                echo $channelError;
                exit;
            }
    }
}

