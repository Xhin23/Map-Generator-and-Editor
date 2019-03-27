<?php
Class Mapgame
{
    private $gameid;
    
    public function __construct($gameid='')
    {
        $this->gameid = $gameid;
    }
    
    public function __get($name)
    {
        $name = str_replace('_',' ',$name);
        $name = ucwords($name);
        
        $name = str_replace(' ','_',$name);
         
        $this->$name = new $name($this->gameid);
        return $this->$name;
    }
    
    public function set_gameid($gameid)
    {
        $this->gameid = $gameid;
    }
    
        private function generate_map($data)
        {
            $data = $this->engine_map->generate($data);;
            
            $data = $this->engine_tiles->edit_tiles($data);
   
            $this->engine_interface->css('main');
            
            $this->engine_interface->map($data);      
            
            return $data;
        }
           
        private function set_map_settings()
        {
            $map_settings = $this->read_games->settings_by_cat('map');
            
            $arr = Array('slug','width','height','seed','image_size');
            foreach ($arr?:Array() AS $key)
            {
                $map_settings[$key] = $this->game[$key];
            }
            $map_settings['disable_rules'] = explode(',',$this->read_games->setting_by_name('disable_rules'));
            
            return $map_settings;
        }
    
    public function ajax()
    {
        $func = 'do_'.$_POST['function'];
        $var = $this->engine_ajax->$func($_POST['data']);

        echo json_encode($var);
    }
    
    public function save_map($data)
    {
        $exists = $this->read_games->by_slug($data['game']['slug']);
        if ($exists) { return Array('Game short name already exists'); }
        
        $game_sets = Array('seed','width','height','image_size');
        
        foreach ($_POST['settings']?:Array() AS $key => $value)
        {
            if (in_array($key,$game_sets))
            {
                unset($_POST['settings'][$key]);
                $_POST['game'][$key] = $value;
            }
        }
        
        $this->gameid = $gameid = $this->write_games->add_game($_POST['game']);
        foreach ($_POST['settings']?:Array() AS $key => $value)
        {
            $this->write_games->add_setting($gameid,'map',$key,$value);
        }
        
        $this->write_games->add_setting($gameid,'','disable_rules',$_POST['disable']);
        
        $game = $this->read_games->by_gameid($gameid);
        return '/map/'.$game['slug'];
    }

    public function map_generator()
    {
        $data = $this->generate_map($_GET);
        
        $this->engine_interface->map_generator($data);
    }
    
    public function game($slug)
    {
        $this->game = $this->read_games->by_slug($slug);
        $this->gameid = $this->game['gameid'];
        
        $map_settings = $this->set_map_settings();
        
        $this->generate_map($map_settings);
        
        $this->engine_interface->editor($this->game);
    }
}