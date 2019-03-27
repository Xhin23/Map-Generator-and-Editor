<?php
Class Write_Regions
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
    
    public function add_region($name,$desc,$color)
    {
        return ZXC::ins('regions')->set('name',$name,'desc',$desc,'color',$color,'gameid',$this->gameid)->go();
    }
    
    public function update_region($regionid,$name,$desc,$color)
    {
        ZXC::up('regions')->set('name',$name,'desc',$desc,'color',$color)->where('regionid',$regionid,'gameid',$this->gameid)->go();
    }
    
    public function delete_region($regionid)
    {
        ZXC::del('regions')->where('regionid',$regionid,'gameid',$this->gameid)->go();
    }
    
    // --
    
    public function reset_coords($regionid)
    {
        ZXC::del('region_tiles')->where('regionid',$regionid,'gameid',$this->gameid)->go();
    }
    
    public function add_tile($regionid,$x,$y)
    {
        ZXC::ins('region_tiles')->set('regionid',$regionid,'x',$x,'y',$y,'gameid',$this->gameid)->go();
    }
    
    public function remove_tile($regionid,$x,$y)
    {
        ZXC::del('region_tiles')->where('regionid',$regionid,'x',$x,'y',$y,'gameid',$this->gameid)->go();
    }
}