<?php
    try {
        $dbh = new PDO("mysql:host=localhost;dbname=bvh_test", 'root', '');
        foreach($dbh->query('SELECT * from joints') as $row) {
            print_r($row);
        }
        $dbh = null;
    } catch (PDOException $e) {
        print "Error!: " . $e->getMessage() . "<br/>";
        die();
    }
?>