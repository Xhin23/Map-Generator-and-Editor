<?php
Class Write_Games
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
    
    public function add_game($data)
    {        
        return ZXC::ins('games')->vset('seed,width,height,slug,name,image_size',$data)->go();
    }
    
    public function add_setting($gameid,$cat,$name,$value)
    {
        return ZXC::ins('game_settings')->set('cat',$cat,'name',$name,'value',$value,'gameid',$gameid)->go();
    }
    
    public function activate()
    {
        ZXC::up('games')->set('status','active')->where('gameid',$this->gameid)->go();
    }
    
    public function set_pos($pos)
    {
        ZXC::up('games')->set('pos',$pos)->where('gameid',$this->gameid)->go();
    }
}