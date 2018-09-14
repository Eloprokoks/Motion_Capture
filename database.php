<?php
    // 3 ustanowienie połączenia z bazą używając klasy PDO
    function connect() {
        require "config.php";  // include 
        $database=null;  //PDO object 
        try {
            $database = new PDO("mysql:host=".$host.";dbname=".$dbname,$user,$pass);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        } 
        return $database;
    } 

?>
