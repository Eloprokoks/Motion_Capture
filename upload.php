<?php 
require "BVH/processBVH.php";
// 5 funkcja odpowiedzialna za odczytanie z formularza 
  function readFileFromForm() {
    $bvh = $_FILES['uploaded_file']['tmp_name'];
    $myfile = fopen($bvh, "r") or die("Unable to open file!");
    // print_r(fread($myfile,filesize($bvh)));
    // fclose($myfile);
    return [$myfile,filesize($bvh)];
  } 
  //6 wywołanie funkcji readFileFromForm, z pliku, zamknięcie
    [$file,$fileSize]=readFileFromForm();
    readBVH($file,$fileSize);
    fclose($file);
?>