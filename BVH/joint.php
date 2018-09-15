<?php

class Joint{
    public $name ;
    public $offsetX = 0;
    public $offsetY = 0;
    public $offsetZ = 0;
    public $numberchannel = 0;
    public $channels; 
    
    function __construct($newName,$newOffsetX,$newOffsetY,$newOffsetZ){
        $this->name=$newName;
        $this->offsetX=$newOffsetX;
        $this->offsetY=$newOffsetY;
        $this->offsetZ=$newOffsetZ;
    }
}
?>