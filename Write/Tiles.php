<?php
Class Write_Tiles
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
    
        private function alt_obj($x,$y)
        {
            return ZXC::alt('tiles')->key('gameid',$this->gameid,'x',$x,'y',$y);
        }
    
        private function alt($x,$y,$field,$val)
        {
            $this->alt_obj($x,$y)->set($field,$val)->go();
        }
    
    public function set_terrain($x,$y,$name)
    {
        $this->alt($x,$y,'terrain',$name);
    }
    
    public function set_fg($x,$y,$name)
    {
        $this->alt($x,$y,'fg',$name);
    }
    
    public function set_data($x,$y,$name,$desc)
    {
        $this->alt_obj($x,$y)->set('name',$name,'desc',$desc)->go();
    }
}