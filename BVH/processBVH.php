<?php
// 7 przerwarzanie pliku BVH
require "joint.php";
require "BVHconsts.php";
$joints = array(); //lista
function readBVH($file, $fileSize)
{
    global $hierarchy, $root, $joints;
    $arrayBVH = getBVHArray($file, $fileSize);
    $fileIsCorrect = checkHierarchy($arrayBVH) && areEqual($arrayBVH[0], $hierarchy) && areEqual($arrayBVH[1], $root);
    if ($fileIsCorrect) {
        checkFile($arrayBVH, 2);
        echo '<pre>';
        print_r($joints);
        echo '</pre>';
    } else {
        echo "plik BVH jest niepoprawny";
    }

}

//8 przetworzenie pliku na listę
function getBVHArray($file, $fileSize)
{ //lista
    $stringContent = fread($file, $fileSize);
    $stringNoWS = preg_replace('/\s+/', '#', $stringContent); // tekst bez białych znaków (WS)
    return explode("#", $stringNoWS);
}

//9 sprawdzenie poprawności pliku
function checkHierarchy($arrayBVH)
{
    foreach ($arrayBVH as $arrayElement) {
        $hierarchy = 0;
        global $braceLeft, $braceRight;
        if (areEqual($arrayElement, $braceLeft)) {
            $hierarchy++;
        } elseif (areEqual($arrayElement, $braceRight)) {
            $hierarchy--;
        }

    }
    return $hierarchy == 0;
}

//10 funkcja pomocnicza porównująca elementy w liście
function areEqual($arrayElement, $itemCompared)
{
    return strcmp($arrayElement, $itemCompared) == 0;
}

function checkFile($arrayBVH, $i)
{
    global $braceLeft, $braceRight, $channels, $offset, $joints, $joint, $endSite, $motion;
    $fileIsCorrect = areEqual($arrayBVH[$i + 1], $braceLeft) && areEqual($arrayBVH[$i + 2], $offset);
    $fileIsEnd = areEqual($arrayBVH[$i - 1], $motion);
    $nextWordIsBraceRight =areEqual($arrayBVH[$i+1],$braceRight);
    echo $nextWordIsBraceRight;
    $i += 2;
    if (!$fileIsEnd) {

        if ($fileIsCorrect) {
            $newJoint = new Joint($arrayBVH[$i - 2], $arrayBVH[$i + 1], $arrayBVH[$i + 2], $arrayBVH[$i + 3]);
            $i += 3;
            //sprawdzamy czy kolejne słowo to channel lub czy jest "}"
            $nextWordIsChannel = areEqual($arrayBVH[$i + 1], $channels);
            //$nextWordIsBraceRight = areEqual($arrayBVH[$i + 1], $braceRight);
            if ($nextWordIsChannel) {
                $jointWithChannels = checkChannels($newJoint, $arrayBVH, $i);
                $i += $newJoint->numberOfChannels + 2;
            }
            //po channelach sprawdzamy czy jest Joint, End site lub "}"
            $nextWord = $arrayBVH[$i + 1];

            if (areEqual($nextWord, $joint)) {
                array_push($joints, $jointWithChannels);
                $i += checkFile($arrayBVH, $i + 2);

            } elseif (areEqual($nextWord, $endSite)) {
                $i += 6;
                $jakaszmienna = $braceRight;
                while ($jakaszmienna == $braceRight) {
                    $i++;
                    $jakaszmienna = $arrayBVH[$i + 1];
                }
                $i += checkFile($arrayBVH, $i + 2);
            } elseif (areEqual($nextWord, $braceRight)) {
                $jakaszmienna = $braceRight;
                while ($jakaszmienna == $braceRight) {
                    $i++;
                    $jakaszmienna = $arrayBVH[$i + 1];
                }
                $i += checkFile($arrayBVH, $i + 2);
        
            } 

        }else {
            echo "nie ma { albo offsetu";
        }
    }
     else {
        echo "KONIEC TEJ STRUKTURY";
    }
    return $i;

}

function checkChannels($newJoint, $arrayBVH, $i)
{

    $numberOfChannels = (int) $arrayBVH[$i + 2];
    $newJoint->setNumberOfChannels($numberOfChannels);
    for ($j = 0; $j < $numberOfChannels; $j++) {
        $newJoint->channels[$arrayBVH[3 + $i + $j]] = array();

    }
    return $newJoint;

}
