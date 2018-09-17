<?php
// 7 przerwarzanie pliku BVH
require "joint.php";
require "BVHconsts.php";
$joints = array(); //lista
$framesNumber;
$frameTimeNumber;
function readBVH($file, $fileSize)
{
    global $hierarchy, $root, $joints, $framesNumber, $frameTimeNumber;
    $arrayBVH = getBVHArray($file, $fileSize);
    $fileIsCorrect = checkHierarchy($arrayBVH) && areEqual($arrayBVH[0], $hierarchy) && areEqual($arrayBVH[1], $root);
    if ($fileIsCorrect) {
// echo '<pre>';
        //         print_r($arrayBVH);
        //         echo '</pre>';;
        //start recursion, 2 = "Hips"
        $i = checkFile($arrayBVH, 2);
        $i = setFramesAndFrameTime($arrayBVH, $i);
        // echo$framesNumber." ".$frameTimeNumber;
        fillJointsWithData($arrayBVH, $i);
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
    global $braceLeft, $braceRight, $channels, $offset, $joints, $joint, $end, $motion, $root;

    // $fileIsCorrect = areEqual($arrayBVH[$i + 1], $braceLeft) && areEqual($arrayBVH[$i + 2], $offset);

    //trafiono na MOTION, $i-1 to pozycja słowa przed nazwą jointa
    if (areEqual($arrayBVH[$i - 1], $motion)) {
        echo "KONIEC TEJ STRUKTURY";

    } else {
        // /$i += 2;
        // if ($fileIsCorrect) {
        $nextWord = $arrayBVH[$i - 1];
        if (areEqual($nextWord, $joint) || areEqual($nextWord, $root)) {
            $newJoint = new Joint($arrayBVH[$i], $arrayBVH[$i + 3], $arrayBVH[$i + 4], $arrayBVH[$i + 5]);
            $i += 5;
            //sprawdzamy czy kolejne słowo to channel lub czy jest "}"
            $nextWordIsChannel = areEqual($arrayBVH[$i + 1], $channels);
            //$nextWordIsBraceRight = areEqual($arrayBVH[$i + 1], $braceRight);
            if ($nextWordIsChannel) {
                $jointWithChannels = checkChannels($newJoint, $arrayBVH, $i);
                $i += $newJoint->numberOfChannels + 2;
            }
            //po channelach sprawdzamy czy jest Joint, End site lub "}"
            array_push($joints, $jointWithChannels);
            $i = setBraceRight($arrayBVH, $i);

        } else {
            $i = setBraceRight($arrayBVH, $i);
        }
        $i = checkFile($arrayBVH, $i + 2);
    }
    return $i;
}

function checkBraceRight($arrayBVH, $braceRight, $i)
{
    $jakaszmienna = $braceRight;
    while ($jakaszmienna == $braceRight) {
        $i++;
        $jakaszmienna = $arrayBVH[$i + 1];
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

function setBraceRight($arrayBVH, $i)
{
    global $braceRight, $end;
    if (areEqual($arrayBVH[$i + 1], $braceRight)) {
        $i = checkBraceRight($arrayBVH, $braceRight, $i);

    } elseif (areEqual($arrayBVH[$i + 1], $end)) {
        setEnd($arrayBVH, $i);
        $i = checkBraceRight($arrayBVH, $braceRight, $i);
    }
    return $i;
}

function setEnd($arrayBVH, $i)
{
    global $site, $joints;
    $fileIsCorrect = areEqual($arrayBVH[$i + 1], $site);
    $i += 1;
    $newJoint = new Joint("End Site " . $i, $arrayBVH[$i + 3], $arrayBVH[$i + 4], $arrayBVH[$i + 5]);
    $i += 5;
    array_push($joints, $newJoint);
    return $i;
}

function setFramesAndFrameTime($arrayBVH, $i)
{
    global $frames, $frame, $time, $framesNumber, $frameTimeNumber;
    $fileIsCorrect = areEqual($arrayBVH[$i], $frames);
    $framesNumber = (int) $arrayBVH[$i + 1];
    $fileIsCorrect = areEqual($arrayBVH[$i + 2], $frame) && areEqual($arrayBVH[$i + 3], $time);
    $frameTimeNumber = (float) $arrayBVH[$i + 4];
    $i += 4;
    return $i;
}

function fillJointsWithData($arrayBVH, $i)
{
    $i += 1;
    $arraySize = count($arrayBVH);
    global $joints;
    $k = 0; //pozycja w tablicy liczona od 0 tam gdzie zaczynają się numery
    $l = 0; // licznik po channelach
    for ($j = $i; $j < $arraySize; $j += $l) {
        $l = 0;
        foreach ($joints[$k % count($joints)]->channels as $key => $channel) {
            if (isset($arrayBVH[$j + $l]) && $arrayBVH[$j + $l]) {
                array_push($joints[$k % count($joints)]->channels[$key], $arrayBVH[$j + $l]);

            }
            $l++;
        }
        $k++;
    }
}
