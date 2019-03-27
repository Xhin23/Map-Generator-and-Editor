<?php
Class Engine_Interface
{
    public function __construct($gameid)
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
        
        private function import_css($url)
        {
            ?>
            <style type="text/css">
                @import '<?=$url?>';
            </style>
            <?php
        }
        
        public function css($name)
        {
            $this->import_css('css/'.$name.'.css');
        }

        private function js($name)
        {
            ?>
            <script type="text/JavaScript" src="js/packages/jquery.js"></script>
            <script type="text/JavaScript" src="js/packages/jscolor.js"></script>
            <script type="text/JavaScript" src="js/packages/twister.js"></script>
            <script type="text/JavaScript" src="js/common.js"></script>
            <script type="text/JavaScript" src="js/<?=$name?>.js"></script>
            <?php
        }
        
        private function set_js($arr)
        {
            echo '<script type="text/JavaScript">';
            foreach ($arr?:Array() AS $key => $val)
            {
                if (is_string($val)) { $val = "'".$val."'"; }
                echo '_'.$key.' = '.$val."\n";
            }
            echo '</script>';
        }
        
        private function set_css($el,$arr)
        {
            echo '<style type="text/css">'.$el.' {';
            foreach ($arr?:Array() AS $key => $val) 
            {  
                echo $key.': '.$val.";\n";
            }
            echo '</style>';
        }

    public function i($name)
    {
        $this->css($name);
        return "Interface/".$name.'.php';
    }
    
    public function map($data)
    {   
        $map = $data['map'];
        $css = $data['css'];
        $fgs = $data['fg'];
        $gens = $data['gen'];
        $settings = $data['settings'];
        
        $this->set_css('img', Array(
            'width' => $settings['image_size'].'px', 
            'height' => $settings['image_size'].'px')
        );
        
        $custom = $this->engine_init->sprites(true);
     
        require $this->i(__FUNCTION__);
        
        $this->set_js(Array('image_size' => $settings['image_size'])); 
    }

    public function map_generator($data)
    {
        $settings = $data['settings'];
        $ignore_settings = Array('hold_ratios','disable_rules','show_sculpt','dist_units');
        
        $this->import_css('https://fonts.googleapis.com/icon?family=Material+Icons');
        
        if ($_GET['hold_ratios'] == -1 || !isset($_GET['hold_ratios']))
        {
            $hold_ratios = Array('ocean_ratio','land_spread_ratio');
        }
        else
        {
            $hold_ratios = explode(',',$_GET['hold_ratios']);
        }
        
        if (!isset($_GET['hold_ratios'])) 
        { 
            $_GET['hold_ratios'] = -1;
        }
        
        require $this->i(__FUNCTION__);
        
        $this->set_js(Array('seed' => md5(microtime())));
       
        $this->js('map_generator');
    }

    public function editor($game)
    {
        $this->game = $game;
           
        $last_shareid = $this->read_shared->last_shareid();
        $init_data = $this->engine_init->render();
                
        $this->import_css('https://fonts.googleapis.com/icon?family=Material+Icons');
        
        require $this->i(__FUNCTION__);              
        
        $this->set_js(Array(
            'gameid' => $this->gameid,
            'seed' => $this->game['seed'],
            'max_seed' => $this->engine_map->max_seed,
            'sid' => md5(microtime().$_SERVER['REMOTE_ADDR']),
            'shareid' => $last_shareid?:0,
            'init_data' => json_encode($init_data)
        ));
        
        $this->js('editor'); 
    }
}