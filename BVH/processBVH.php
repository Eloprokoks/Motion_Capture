<?php

require "Joint.php";
require 'BVHconsts.php';
require "addDataToDataBase.php";

/**
 * Przekształca plik .bvh na obiekt
 */
class Parser
{
    private $joints = array();
    private $framesNumber;
    private $frameTimeNumber;
    private $parents = array();
    private $parentHierarchy = 0;
    private $file;
    private $fileSize;
    private $fileIsCorrect = true;
    private $arrayBVH = array();

    /**
     * Konstruktor
     */
    public function __construct($newFile, $newFileSize)
    {

        $this->parents[0] = null; // rodzic pierwszego stawu nie ma rodzica - ROOT nie ma rodzica
        $this->file = $newFile;
        $this->fileSize = $newFileSize;

    }

    /**
     * Funkcja główna, wywołuje resztę funkcji
     */
    public function readBVH()
    {
        global $HIERARCHY, $ROOT;

        $this->arrayBVH = $this->getBVHArray($this->file, $this->fileSize);

        $this->fileIsCorrect = $this->checkHierarchy($this->arrayBVH)
        && $this->areEqual($this->arrayBVH[0], $HIERARCHY)
        && $this->areEqual($this->arrayBVH[1], $ROOT);

        $i = $this->checkFile(2);
        $i = $this->setFramesAndFrameTime($i);
        $this->fillJointsWithData($i);

        $this->print($this->joints);

        return [$this->joints, $this->framesNumber, $this->frameTimeNumber];
    }

    /**
     * Funkcja sprawdzająca poprawność pliku na podstawie chierarchii stawów
     */
    private function checkHierarchy()
    {
        global $BRACE_LEFT, $BRACE_RIGHT;

        foreach ($this->arrayBVH as $arrayElement) {
            $hierarchy = 0;
            if ($this->areEqual($arrayElement, $BRACE_LEFT)) {
                $hierarchy++;
            } elseif ($this->areEqual($arrayElement, $BRACE_RIGHT)) {
                $hierarchy--;
            }
        }
        return $hierarchy == 0;
    }

    /**
     * Funkcja czyta plik i tworzy listę
     */
    private function getBVHArray()
    {
        $stringContent = fread($this->file, $this->fileSize);
        $stringNoWS = preg_replace('/\s+/', '#', $stringContent); // tekst bez białych znaków (WS)
        return explode("#", $stringNoWS);
    }

    /**
     * Funkcja porównująca dwa słowa
     */
    private static function areEqual($arrayElement, $itemCompared)
    {
        return strcmp($arrayElement, $itemCompared) == 0;
    }

    /**
     * Rekursywna funkcja chodząca po stworzonej liście i tworzy Jointy
     */
    private function checkFile($i)
    {
        global $ROOT, $MOTION, $JOINT, $CHANNELS;

        if (!$this->areEqual($this->arrayBVH[$i - 1], $MOTION)) {

            $nextWord = $this->arrayBVH[$i - 1];
            if ($this->areEqual($nextWord, $JOINT) // jesli następne słowo jest JOintem albo rootem
                 || $this->areEqual($nextWord, $ROOT)) {

                $this->parentHierarchy++; // zwiększ hierarchię
                $this->parents[$this->parentHierarchy] = $this->arrayBVH[$i]; //ustawia aktualny staw jako rodzica kolejnych dzieci

                //stworzenie nowego obiektu, sprawdz konstruktor
                $newJoint = new Joint(
                    $this->arrayBVH[$i],
                    $this->arrayBVH[$i + 3],
                    $this->arrayBVH[$i + 4],
                    $this->arrayBVH[$i + 5],
                    $this->parents[$this->parentHierarchy - 1]
                );

                $i += 5; // przesunięcie wskaźnika

                //sprawdzamy czy kolejne słowo to channel lub czy jest "}"
                if ($this->areEqual($this->arrayBVH[$i + 1], $CHANNELS)) {
                    $jointWithChannels = $this->checkChannels($newJoint, $i);
                    $i += $newJoint->numberOfChannels + 2;
                }
                //po channelach sprawdzamy czy jest Joint, End site lub "}"
                array_push($this->joints, $jointWithChannels);
                $i = $this->setBraceRight($i);

            } else {
                $i = $this->setBraceRight($i);
            }
            $i = $this->checkFile($i + 2);
        }
        return $i;
    }

    /**
     * przesunięcie wskaźnika o tyle ile jest "}"
     */
    private function checkBraceRight($braceRight, $i)
    {
        $currentWord = $braceRight;
        while ($currentWord == $braceRight) {
            $i++;
            $currentWord = $this->arrayBVH[$i + 1];
            $this->parentHierarchy--;
        }
        return $i;
    }

    /**
     * Funkcja sprawdza cyfrę przed channelami, inicjuje listę channeli obiektu Joint nowymi pustymi listami
     */
    private function checkChannels($newJoint, $i)
    {
        $numberOfChannels = (int) $this->arrayBVH[$i + 2]; // rzutowanie na inta ze stringa
        $newJoint->setNumberOfChannels($numberOfChannels);
        for ($j = 0; $j < $numberOfChannels; $j++) {
            $newJoint->channels[$this->arrayBVH[3 + $i + $j]] = array();

        }
        return $newJoint;

    }

    /**
     * Funkcja przesuwa wskaźnik o ilość "}"  i sprawdza czy jest End site
     */
    private function setBraceRight($i)
    {
        global $BRACE_RIGHT, $END;
        if ($this->areEqual($this->arrayBVH[$i + 1], $BRACE_RIGHT)) {
            $this->parentHierarchy++;
            $i = $this->checkBraceRight($BRACE_RIGHT, $i);

        } elseif ($this->areEqual($this->arrayBVH[$i + 1], $END)) {
            $this->setEnd($i);
            $i = $this->checkBraceRight($BRACE_RIGHT, $i);
        }
        return $i;
    }

    /**
     * Funkcja czyta end site i tworzy nowego BaseJointa
     */
    private function setEnd($i)
    {
        global $SITE;
        $this->parentHierarchy++;
        $this->fileIsCorrect = $this->areEqual($this->arrayBVH[$i + 1], $SITE);
        $i += 2;

        $newJoint = new BaseJoint(
            "EndSite" . $i,
            $this->arrayBVH[$i + 3],
            $this->arrayBVH[$i + 4],
            $this->arrayBVH[$i + 5],
            $this->parents[$this->parentHierarchy - 1]);
        $i += 4;
        array_push($this->joints, $newJoint);
        return $i;
    }

    /**
     * funkcja czyta ilość klatek i czas trwania jednej klatkis
     */
    private function setFramesAndFrameTime($i)
    {
        global $FRAMES, $FRAME, $TIME;
        $this->fileIsCorrect = $this->areEqual($this->arrayBVH[$i], $FRAMES);
        $this->framesNumber = (int) $this->arrayBVH[$i + 1];
        $this->fileIsCorrect =
        $this->areEqual($this->arrayBVH[$i + 2], $FRAME)
        && $this->areEqual($this->arrayBVH[$i + 3], $TIME);
        $this->frameTimeNumber = (float) $this->arrayBVH[$i + 4];
        $i += 4;
        return $i;
    }
    /**
    *Funkcja czyta numery i wkłada je do odpowiednich  tablic
     */
    private function fillJointsWithData($i)
    {
        $i += 1;
        $arraySize = count($this->arrayBVH);

        $k = 0; //pozycja w tablicy liczona od 0 tam gdzie zaczynają się numery
        $l = 0; // licznik po channelach
        for ($j = $i; $j < $arraySize; $j += $l) {
            $l = 0;
            if (isset($this->joints[$k % count($this->joints)]->channels)) {

                foreach ($this->joints[$k % count($this->joints)]->channels as $key => $channel) {
                    if (isset($this->arrayBVH[$j + $l])) {
                        // if (isset($arrayBVH[$j + $l]) && $arrayBVH[$j + $l]) {  //po coś to było nie wiem po co
                        array_push($this->joints[$k % count($this->joints)]->channels[$key], $this->arrayBVH[$j + $l]);
                    }
                    $l++;
                }
            }
            $k++;
        }
    }

    /**
     * Funkcja pomocnicza, drukuje podany obiekt
     */
    function print($object) {
        echo "<pre>";
        print_r($object);
        echo "</pre>";
    }
};
