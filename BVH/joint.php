<?php

class Joint{
    public $name ;
    public $offsetX = 0;
    public $offsetY = 0;
    public $offsetZ = 0;
    public $numberchannel = 0;
    public $channels; 
    
    function __construct($newName){
        $this->name=$newName;
    }
}
?>