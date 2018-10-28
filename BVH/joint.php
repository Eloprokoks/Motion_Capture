<?php
/**
 * Klasa reprezentująca podstawowy staw, bez CHANNELS,
 *  tylko End Site wykorzystuje samego BaseJointa
 */
class BaseJoint
{
    public $name;
    public $offsetZ = 0;
    public $offsetX = 0;
    public $offsetY = 0;
    public $parent;              // informacja o rodzicu

    /**
     * 
     */
    public function __construct($newName, $newOffsetX, $newOffsetY, $newOffsetZ,$newParent)
    {
        $this->name = $newName;
        $this->offsetX = $newOffsetX;
        $this->offsetY = $newOffsetY;
        $this->offsetZ = $newOffsetZ;
        $this->parent = $newParent;
        
    }
}

/**
 * Rozszerzenie klasy BaseJoint, dodanie channeli
 */
class Joint extends BaseJoint
{
    public $numberOfChannels = 0;
    public $channels = array(); //lista  $channels=array() ----> $channels=[]

    public function setNumberOfChannels($newNumberOfChannels)
    {
        $this->numberOfChannels = $newNumberOfChannels;
    }
}
