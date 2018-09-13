<?php
    
    function connect() {
        require "config.php";  // include 
        $dbh=null;  //PDO object 
        try {
            $dbh = new PDO("mysql:host=".$host.";dbname=".$dbname,$user,$pass);
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "<br/>";
            die();
        } 
        return $dbh;
    } 
    
    $dbh2=connect();
?>
