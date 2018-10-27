<?php

class BaseJoint
{
    public $name;
    public $offsetZ = 0;
    public $offsetX = 0;
    public $offsetY = 0;
    public function __construct($newName, $newOffsetX, $newOffsetY, $newOffsetZ)
    {
        $this->name = $newName;
        $this->offsetX = $newOffsetX;
        $this->offsetY = $newOffsetY;
        $this->offsetZ = $newOffsetZ;
    }
}
class Joint extends BaseJoint
{
    public $numberOfChannels = 0;
    public $channels = array(); //lista  $channels=array() ----> $channels=[]

    public function setNumberOfChannels($newNumberOfChannels)
    {
        $this->numberOfChannels = $newNumberOfChannels;
    }
}
