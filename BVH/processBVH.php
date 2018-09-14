<?php 
// 7 przerwarzanie pliku BVH
require "joint.php";
function readBVH($file,$fileSize){
    require "BVHconsts.php";
    $hierarchy = 0;
    $joints = array(); //lista 
    $fileIsCorrect = true;
    $arrayBVH=getBVHArray($file,$fileSize);
   //9 sprawdzenie poprawności pliku
    foreach($arrayBVH as $e){
        if($e==$braceLeft)
            {
                $hierarchy++;
                echo $hierarchy." ";                
            }
        elseif($e==$braceRight)
        {
            $hierarchy--;
            echo $hierarchy." ";
        }
    }
  }

    //8 przetworzenie pliku na listę 
  function getBVHArray($file,$fileSize){   //lista 
    $stringContent=fread($file,$fileSize);
    $stringNoWS = preg_replace('/\s+/', '#', $stringContent);   // tekst bez białych znaków (WS)
    return explode("#",$stringNoWS);
    }


?>