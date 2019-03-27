<?php
Class Read_Rules
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
        private function rule()
        {
            return ZXC::sel('ruleid,entype,entid,name,ruletype,chance,seed,min,max/rules')->where('gameid',$this->gameid);
        }
        
    public function get_all()
    {
        return $this->rule()->go();
    }
        
        private function entry()
        {
            return ZXC::sel('1entryid,1ruleid,1name,1weight,2entype,2entid/rule_entries<ruleid>rules')->where('1gameid',$this->gameid);
        }
        
    public function get_entries()
    {
        return $this->entry()->go();
    }
        
}