<?php
Class Engine_
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
    
    public function __get($name)
    {
        $name = str_replace('_',' ',$name);
        $name = ucwords($name);
        $name = str_replace(' ','_',$name);
         
        $this->$name = new $name($this->gameid);
        return $this->$name;
    }
}