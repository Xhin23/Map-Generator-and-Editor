<?php
Class Read_Sprites
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
        
        private function sprite()
        {
            return ZXC::sel('spriteid,type,name,url/sprites')->where('gameid',$this->gameid);
        }
        
    public function get_all()
    {
        return $this->sprite()->go();
    }
}