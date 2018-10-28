<?php
/**
 * Funkcja łącząca się z bazą danych za pomocą biblioteki PDO
 * @return PDO - baza danych
 */
function connectToDataBase()
{
    require "config.php";
    try {
        $database = new PDO(
            "mysql:host=" . $host . ";dbname=" . $dbname,
            $user,
            $pass
        );
        $database->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // wyswietla błędy jeśli się pojawią
        return $database;  
    } catch (PDOException $e) {  // wychwytuje błędy i drukuje 
        print($e);
        die();
    }
}
