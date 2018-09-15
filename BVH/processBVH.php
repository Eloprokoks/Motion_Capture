<?php 
// 7 przerwarzanie pliku BVH
require "joint.php";
require "BVHconsts.php";
function readBVH($file,$fileSize){
    $joints = array(); //lista 
    $arrayBVH=getBVHArray($file,$fileSize);
    global $hierarchy,$root,$braceLeft,$offset,$channels,$numberOfChannels;
    $fileIsCorrect=checkHierarchy($arrayBVH) && areEqual($arrayBVH[0],$hierarchy) && areEqual($arrayBVH[1],$root);
    if($fileIsCorrect){
        echo "plik BVH jest poprawny";
        $fileIsCorrect=areEqual($arrayBVH[3],$braceLeft)&&areEqual($arrayBVH[4],$offset);
        if($fileIsCorrect){
            $newJoint=new Joint($arrayBVH[2],$arrayBVH[5],$arrayBVH[6],$arrayBVH[7]);
            $fileIsCorrect=areEqual($arrayBVH[8],$channels);
            if($fileIsCorrect){
                $numberOfChannels=(int)$arrayBVH[9];
                $newJoint->setNumberOfChannels($numberOfChannels);
                for ($i=0; $i <$numberOfChannels; $i++) { 
                   $newJoint->channels[$arrayBVH[10+$i]]=array();

                }
                print_r($newJoint);
            }
        }
    }
    else echo "plik BVH jest niepoprawny"; 
  }

    //8 przetworzenie pliku na listę 
    function getBVHArray($file,$fileSize){   //lista 
        $stringContent=fread($file,$fileSize);
        $stringNoWS = preg_replace('/\s+/', '#', $stringContent);   // tekst bez białych znaków (WS)
        return explode("#",$stringNoWS);
    }

    //9 sprawdzenie poprawności pliku
    function checkHierarchy($arrayBVH){
        foreach($arrayBVH as $arrayElement){
            $hierarchy=0;
            global $braceLeft,$braceRight;
            if(areEqual($arrayElement,$braceLeft))
                $hierarchy++;              
            elseif(areEqual($arrayElement,$braceRight)) 
                $hierarchy--;
        }
        return $hierarchy==0;
    }

    //10 funkcja pomocnicza porównująca elementy w liście
    function areEqual($arrayElement,$itemCompared){
        return strcmp($arrayElement,$itemCompared)==0;
    }




?>