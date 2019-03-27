<?php
Class Write_Rules
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
    
    public function add_rule($entype,$entid,$name,$chance,$seed)
    {
        return ZXC::ins('rules')->set('entype',$entype,'entid',$entid,'name',$name,'chance',$chance,'seed',$seed,'ruletype','trait','gameid',$this->gameid)->go();
    }
    
    public function update_rule($ruleid,$name,$ruletype,$chance,$seed,$min,$max)
    {
        ZXC::up('rules')->set('name',$name,'ruletype',$ruletype,'chance',$chance,'seed',$seed,'min',$min,'max',$max)->where('ruleid',$ruleid,'gameid',$this->gameid)->go();
    }
    
    public function delete_rule($ruleid)
    {
        ZXC::del('rules')->where('ruleid',$ruleid,'gameid',$this->gameid)->go();
    }
    
    // --
    
    public function add_entry($ruleid,$name,$weight)
    {
        return ZXC::ins('rule_entries')->set('ruleid',$ruleid,'gameid',$this->gameid,'name',$name,'weight',$weight)->go();
    }
    
    public function update_entry($entryid,$name,$weight)
    {
        ZXC::up('rule_entries')->set('name',$name,'weight',$weight)->where('entryid',$entryid,'gameid',$this->gameid)->go();
    }
    
    public function delete_entry($entryid)
    {
        ZXC::del('rule_entries')->where('entryid',$entryid,'gameid',$this->gameid)->go();
    }
}