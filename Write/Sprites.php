<?php
Class Write_Sprites
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
    
    public function add($type,$name,$url)
    {
        return ZXC::ins('sprites')->set('type',$type,'name',$name,'url',$url,'gameid',$this->gameid)->go();
    }
}