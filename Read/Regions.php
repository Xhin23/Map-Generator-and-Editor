<?php
Class Read_Regions
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
    
        private function region()
        {
            return ZXC::sel('regionid,name,color,desc/regions')->where('gameid',$this->gameid);
        }
        
    public function get_all()
    {
        return $this->region()->go();
    }
        
        private function tile()       
        {
            return ZXC::sel('x,y,regionid/region_tiles')->where('gameid',$this->gameid);
        }
        
    public function get_all_tiles()
    {
        return $this->tile()->go();
    }
    
}