<?php
// 3 ustanowienie połączenia z bazą używając klasy PDO
function connect()
{
    require "config.php";
    require "messageconst.php";
    global $noConnection;
    // include
     //PDO object
    try {
        $database = new PDO("mysql:host=" . $host . ";dbname=" . $dbname, $user, $pass);
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $database;
    } catch (PDOException $e) {
        print $noConnection;
        die();
    }
}
