<?php

require "Joint.php";
require 'BVHconsts.php';
require "addDataToDataBase.php";

/**
 * Przekształca plik .bvh na obiekt
 */
class Parser
{
    private $joints = array();      // tablica stawów
    private $framesNumber;          // ilość klatek
    private $frameTimeNumber;       // czas trwania klatki
    private $parents = array();     // tablica rodziców
    private $parentHierarchy = 0;   // hierarchia rodziców
    private $file;                  // nazwa pliku
    private $fileSize;              // rozmiar pliku
    private $fileIsCorrect = true;  // sprawdzenie poprawności pliku
    private $arrayBVH = array();    // plik bvh umieszczony w tablicy

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
     *0/ Funkcja główna, wywołuje resztę funkcji
     */
    public function readBVH()
    {
        global $HIERARCHY, $ROOT;

        // ustawienie pliku bvh w tablicy 
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
     * 3/ Funkcja sprawdzająca poprawność pliku na podstawie hierarchii stawów
     */
    private function checkHierarchy()
    {
        global $BRACE_LEFT, $BRACE_RIGHT;

        foreach ($this->arrayBVH as $arrayElement) {  //obecnie sprawdzany element w tablicy arrayBVH nazywa się arrayElement
            $hierarchy = 0;
            if ($this->areEqual($arrayElement, $BRACE_LEFT)) {   //jeżeli obecny element będzie {, zwróć prawdę
                $hierarchy++;
            } elseif ($this->areEqual($arrayElement, $BRACE_RIGHT)) {
                $hierarchy--;
            }
        }
        return $hierarchy == 0;   
    }

    /**
     * 1/ Funkcja czyta plik i tworzy listę
     */
    private function getBVHArray()
    {
        $stringContent = fread($this->file, $this->fileSize);     //odczytuje plik do podanego rozmiaru, zwraca łancuch znaków
        $stringNoWS = preg_replace('/\s+/', '#', $stringContent); // zamienia białe znaki na #
        return explode("#", $stringNoWS);                         // explode dzieli stringa i przypisuje każdą kolejną wartość do nowego indeksu w tablicy  
    }

    /**
     * 2/ Funkcja porównująca dwa słowa
     */
    private function areEqual($arrayElement, $itemCompared)
    {
        return strcmp($arrayElement, $itemCompared) == 0;  //zwraca true jeśli oba elementy równe 
    }

    /**
     *  Rekursywna funkcja chodząca po stworzonej liście i tworzy Jointy
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
                // MUSZE LEPIEJ OGARNAC TO CO TU SIE DZIEJE
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
     * 4/ przesunięcie wskaźnika o tyle ile jest "}"
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
     * 5/ Funkcja sprawdza cyfrę przed channelami, inicjuje listę channeli obiektu Joint nowymi pustymi listami
     */
    private function checkChannels($newJoint, $i)
    {
        $numberOfChannels = (int) $this->arrayBVH[$i + 2]; // rzutowanie na inta ze stringa  //
        $newJoint->setNumberOfChannels($numberOfChannels);    //wywołanie metody (funkcji wewnątrz klasy) ustawiającej numer channeli
        for ($j = 0; $j < $numberOfChannels; $j++) {
            $newJoint->channels[$this->arrayBVH[3 + $i + $j]] = array();  

        }
        return $newJoint;

    }

    /**
     * 6/ Funkcja przesuwa wskaźnik o ilość "}"  i sprawdza czy jest End site
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
     * 7/ funkcja czyta ilość klatek i czas trwania jednej klatkis
     */
    private function setFramesAndFrameTime($i)
    {
        global $FRAMES, $FRAME, $TIME;
        $this->fileIsCorrect = $this->areEqual($this->arrayBVH[$i], $FRAMES);
        $this->framesNumber = (int) $this->arrayBVH[$i + 1];  // rzutowanie stringa na inta
        $this->fileIsCorrect =
        $this->areEqual($this->arrayBVH[$i + 2], $FRAME)
        && $this->areEqual($this->arrayBVH[$i + 3], $TIME);
        $this->frameTimeNumber = (float) $this->arrayBVH[$i + 4];  // rzutowanie stringa na floata bo frame time wyglada na przykład tak 0.03333
        $i += 4;
        return $i;
    }
    /**
    *Funkcja czyta numery i wkłada je do odpowiednich  tablic
     */
    private function fillJointsWithData($i)
    {
        $i += 1;     // dodanie jeden bo znajdowaliśmy się na wartości frame time
        $arraySize = count($this->arrayBVH);   //zlicza ilość słów w tablicy 

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
