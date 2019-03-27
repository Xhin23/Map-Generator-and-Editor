<?php
Class Read_Games
{
    public function __construct($gameid='')
    {
        $this->gameid = $gameid;
    }
    
        private function game()
        {
            $obj = ZXC::sel('gameid,seed,width,height,image_size,slug,name/games');
            if ($this->gameid)
            {
                $obj = $obj->where('gameid',$this->gameid);
            }
            return $obj;
        }
        
    public function by_gameid($gameid)
    {
        return $this->game()->where('gameid',$gameid)->one();
    }
    
    public function by_slug($slug)
    {
        return $this->game()->where('slug',$slug)->one();
    }
    
        private function setting()
        {
            return ZXC::sel('name,cat,value/game_settings')->where('gameid',$this->gameid);
        }
        
    public function setting_by_name($name)
    {
        $row = $this->setting()->where('name',$name)->one();
        return $row['value'];
    }
        
    public function settings_by_cat($cat)
    {
        $settings = Array();
        $arr = $this->setting()->where('cat',$cat)->go();
        foreach ($arr?:Array() AS $row)
        {
            $settings[$row['name']] = $row['value']; 
        }
        return $settings;
    }
    
}