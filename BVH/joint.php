<?php

class Joint{
    public $name ;
    public $offsetX = 0;
    public $offsetY = 0;
    public $offsetZ = 0;
    public $numberOfChannels = 0;
    public $channels=array(); //lista  $channels=array() ----> $channels=[]
    
    function __construct($newName,$newOffsetX,$newOffsetY,$newOffsetZ){
        $this->name=$newName;
        $this->offsetX=$newOffsetX;
        $this->offsetY=$newOffsetY;
        $this->offsetZ=$newOffsetZ;
    }
    function setNumberOfChannels($newNumberOfChannels){
        $this->numberOfChannels=$newNumberOfChannels;
    }
}
?>