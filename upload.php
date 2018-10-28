<?php
require "BVH/processBVH.php";
// 5 funkcja odpowiedzialna za odczytanie z formularza
function readFileFromForm()
{
    $bvh = $_FILES['uploaded_file']['tmp_name'];
    $myfile = fopen($bvh, "r") or die("Unable to open file!");
    // print_r(fread($myfile,filesize($bvh)));
    // fclose($myfile);
    $fileName = $_FILES['uploaded_file']['name'];
    return [$myfile, filesize($bvh), $fileName];
}
//6 wywołanie funkcji readFileFromForm, z pliku, zamknięcie
[$file, $fileSize, $fileName] = readFileFromForm();

$parser=new Parser($file, $fileSize);
[$joints,$framesNumber, $frameTimeNumber]=$parser->readBVH();

$saver = new Saver();
$saver->addFileToDataBase(
    $fileName,
    $framesNumber,
    $frameTimeNumber
);

$saver->addJointsToDataBase($joints);

fclose($file);

