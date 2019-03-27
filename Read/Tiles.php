<?php
Class Read_Tiles
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
    
        private function tile()
        {
            return ZXC::sel('x,y,name,desc,terrain,fg/tiles')->where('gameid',$this->gameid);
        }
        
    public function get_all()
    {
        return $this->tile()->go();
    }
    
    public function has_name()
    {
        return $this->tile()->where('name!=','')->go();
    }
}