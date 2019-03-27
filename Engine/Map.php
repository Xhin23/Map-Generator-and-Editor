<?php
Class Engine_Map
{
    public $max_seed = 99999999;
    
    public function __construct($gameid)
    {
        $this->gameid = $gameid;

        $this->settings['seed'] = rand(1,$this->max_seed);
    }
    
    public function __get($name)
    {
        $name = str_replace('_',' ',$name);
        $name = ucwords($name);
        $name = str_replace(' ','_',$name);
         
        $this->$name = new $name($this->gameid);
        return $this->$name;
    }
    
            private function set_ratios()
            {
                
                foreach ($this->settings AS $key => $value)
                {
                    if (strpos($key,'_ratio') === false) { continue; }
                    
                    $value = explode('/',$value);
                    $key = str_replace('_ratio','',$key);
                    $this->settings[$key.'_min'] = $value[0];
                    $this->settings[$key.'_max'] = $value[1];
                }    
            }
    
        private $settings = Array(
            'height' => 25,
            'width' => 70,
            'image_size' => 20,
            'seed' => '',
            
            'sculpt_left' => 4,
            'sculpt_right' => 0,
            'sculpt_top' => 4,
            'sculpt_bottom' => 0,  
            'sculpt_top_left' => 4,
            'sculpt_bottom_right' => 4,
            'sculpt_bottom_left' => 0,
            'sculpt_top_right' => 0,
            
            'ocean_ratio' => Array('3/4','water'),
            
            'land_spread_ratio' => Array('1/5','field'),
            
            'beach_seed_ratio' => Array('1/2','sand'),
            'beach_spread_ratio' => Array('1/2','sand'),
            
            'island_chance_ratio' => Array('1/5','island'),
                                    
            'isle_spread' => Array(5,'island'),
            'mountain_kill_ratio' => Array('2/7','mountain'),
            'forest_kill_ratio' => Array('1/10','forest'),
            
            'volcano_chance_ratio' => Array('1/2','volcano'),
            'island_to_volcano_chance_ratio' => Array('0/1','volcano'),
            'oasis_chance_ratio' => Array('1/7','oasis'),
            
            'biome_chance_ratio' => '6/10',
            'ice_to_desert_ratio' => Array('0/1','desert'),
            'desert_to_ice_ratio' => Array('0/1','ice'),
            
            'lava_chance_ratio' => Array('1/9','lava'),
            'desert_blend_chance_ratio' => Array('1/5','desert_blend'),
            
            'snow_chance_ratio' => Array('1/7','snow'),
            'ice_blend_ratio' => Array('1/5','ice_blend'),
            
            'desert_cave_chance_ratio' => Array('1/7','desert_cave'),
            
            'harvest_seed_ratio' => Array('1/4','harvest'),
            'harvest_spread_ratio' => Array('2/3','harvest'),
            'bigtree_chance_ratio' => Array('1/10','bigtree')
        );
    
        private $help_tiles;
        private $init_settings;
    
        private function set_settings($settings)
        {
            foreach ($this->settings?:Array() AS $key => $value)
            {
                if (is_array($value))
                {
                    $this->help_tiles[$key] = $value[1];
                    $this->settings[$key] = $value[0];
                }
            }
            
            $this->init_settings = $this->settings;
            
            foreach ($settings?:Array() AS $key => $value)
            {
                if (($key == 'width' || $key == 'height') && $value > 200) { $value = 200; }
                if (substr($key,0,6) == 'sculpt' && $value > 10) { $value = 10; }
                if ($key == 'seed' && !$value) { continue; }
                $this->settings[$key] = $value;
            }

            $this->set_settings = $this->settings;
            
            $this->set_ratios();
            
            srand($this->settings['seed']);
        }
    
        private $rules = Array(
            'seed',
            'oceans',
            'outline_continents',
            'fill_continents',
            'clean_continents',
            'beach_seeding',
            'beach_spreading',
            'island_generation',
            'tree_and_mountain_seeding',
            'mountain_kill',
            'forest_kill',
            'mountain_ranges',
            'forest_spread',
            'volcano_generation',
            'oasis_generation',
            'desert_and_ice_seeding',
            'desert_and_ice_swap',
            'desert_generation',
            'ice_generation',
            'lava_generation',
            'desert_polishing',
            'snow_generation',
            'ice_polishing',
            'cave_generation',
            'savannah_seeding',
            'savannah_spread',
            'big_tree_generation',
            'savannah_polishing'
        );
    
    private $x,$y;
    private $loc = Array();
    private $tile;
    
    public function generate($settings=Array())
    {
        $this->set_settings($settings);
        
        foreach ($this->rules?:Array() AS $rule)
        {
            if (has($settings['disable_rules'],$rule)) { continue; }
            if ($rule == '') { break; }
            $func = 'rule_'.$rule;
            for ($this->y = 0; $this->y < $this->settings['height']; $this->y++)
            {
                for ($this->x = 0; $this->x < $this->settings['width']; $this->x++)
                {
                    $this->tile = $this->loc[$this->x][$this->y];
                    $this->$func($this->loc[$this->x][$this->y]);
                    $this->loc[$this->x][$this->y] = $this->tile;
                }
            }
        }
        
        return Array(
            'map' => $this->loc, 
            'css' => $this->css,
            'settings' => $this->set_settings,
            'init_settings' => $this->init_settings,
            'help_tiles' => $this->help_tiles,
            'rules' => $this->rules
        );
    }
    
        private $css = Array();
    
        private function set_css($css)
        {
            $this->css[$this->x][$this->y] = $css;
        }
        
        private $var = Array();
        private function set_var($name,$val)
        {
            $this->var[$name][$this->x][$this->y] = $val;
        }
        
        private function get_var($name,$cx=0,$cy=0)
        {
            return $this->var[$name][$this->x+$cx][$this->y+$cy];
        }
        
        // random functions
        
        private function r($min,$max,$arr)
        {
            if (!is_array($arr))
            {
                $arr = Array($arr);
            }
            $value = $arr[array_rand($arr)];
            
            $r = rand(1,$max);
            if ($r <= $min)
            {
                $this->tile = $value;
            }
            
        }
        
        private function smin($name)
        {
            return $this->settings[$name.'_min'];
        }
        
        private function smax($name)
        {
            return $this->settings[$name.'_max'];
        }
        
        private function rvar($min,$max,$var,$value)
        {
            $r = rand(1,$max);
            if ($r <= $min) 
            {
                $this->set_var($var,$value);
            }
        }

    // ---------------------------------------
    // ---------------- Rules ----------------
    // ---------------------------------------

    private function rule_seed() 
    {
        $this->tile = pick(Array('field','water'));
    }
    
    private function rule_oceans()
    {
        $this->spread('water',$this->smin('ocean'),$this->smax('ocean'));
    }
    
    private function rule_outline_continents() 
    { 
        $this->x_connect($this->settings['sculpt_left'],'field',-1); // left
        $this->x_connect($this->settings['sculpt_right'],'field',1); // right
        $this->y_connect($this->settings['sculpt_top'],'field',-1); // top
        $this->y_connect($this->settings['sculpt_bottom'],'field',1); // bottom
           
        $this->diag_connect($this->settings['sculpt_top_left'],'field',-1,-1); // top left
        $this->diag_connect($this->settings['sculpt_bottom_right'],'field',1,1);  // bottom right
        $this->diag_connect($this->settings['sculpt_bottom_left'],'field',-1,1); // bottom left
        $this->diag_connect($this->settings['sculpt_top_right'],'field',1,-1); //top right
    }
    
    private function rule_fill_continents()
    {   
        $this->xy_connect(3,'field');
        $this->spread('field',$this->smin('land_spread'),$this->smax('land_spread'));
    }

    private function rule_clean_continents()
    {
        // kill random-looking islands
        if ($this->tile == 'field' && $this->surrounded_by('water')) 
        {
             $this->tile = 'water'; 
        }
        // turn random-looking water into lakes
        elseif ($this->tile == 'water' && $this->surrounded_by('field')) 
        {
            $this->tile = 'lake';
        }
        
        $this->xy_connect(2,'water');
    }

    private function rule_beach_seeding() 
    { 
        if ($this->tile != 'water' && $this->around('water'))
        {
            $this->r($this->smin('beach_seed'),$this->smax('beach_seed'),'sand');
        }
    }
    
    private function rule_beach_spreading() 
    {
        if ($this->tile != 'water')
        { 
            $this->spread('sand',$this->smin('beach_spread'),$this->smax('beach_spread'));
        }
    }

    private function rule_island_generation() 
    {
        if ($this->surrounded_by('water') && $this->loc[$this->x-$this->settings['isle_spread']][$this->y] == 'water' 
                                          && $this->loc[$this->x+$this->settings['isle_spread']][$this->y] == 'water'
                                          && $this->loc[$this->x][$this->y-$this->settings['isle_spread']] == 'water' 
                                          && $this->loc[$this->x][$this->y+$this->settings['isle_spread']] == 'water'
                                          && !$this->diag_to('island'))
         {
             $this->r($this->smin('island_chance'),$this->smax('island_chance'),'island');
         }
    }

    private function rule_tree_and_mountain_seeding() 
    {
        if ($this->tile == 'field')
        {
            $this->tile = pick(Array('mountain','field','forest'));
        }
    }
    
    private function rule_mountain_kill() 
    {
        if ($this->tile == 'mountain')
        {
            $this->r($this->smin('mountain_kill'),$this->smax('mountain_kill'),'field');
        }
    }
    
    private function rule_forest_kill() 
    {
        if ($this->tile == 'forest')
        {
            $this->r($this->smin('forest_kill'),$this->smax('forest_kill'),'field');
        }
    }

    private function rule_mountain_ranges() 
    {
        $this->xy_connect(4,'mountain');
        $this->diag_connect(3,'mountain',1,1);
        $this->diag_connect(3,'mountain');
    }
    
    private function rule_forest_spread() 
    {
        $this->connect(2,'forest');
        
        if ($this->near('forest') && $this->tile == 'field')
        {
            $this->r(1,2,'forest');
        }
    }
    
    private function rule_volcano_generation() 
    {    
        if ($this->surrounded_by('water') && $this->tile != 'water' && $this->tile != 'island') 
        {
            $this->tile = 'water';
        }
        
        if ($this->surrounded_by('water') && $this->tile != 'water' && $this->tile != 'island') 
        {
            $this->r($this->smin('volcano_chance'),$this->smax('volcano_chance'),'volcano');            
        }

        if ($this->tile == 'island')
        {
            $this->r($this->smin('island_to_volcano_chance'),$this->smax('island_to_volcano_chance'),'volcano');
        }

        $this->connect(3,'volcano');
    }
    
    private function rule_oasis_generation() 
    {
        if ($this->tile == 'field' && $this->surrounded_by('sand'))
        {
            $this->tile = 'oasis';
        }
        
        if ($this->surrounded_by('sand'))
        {
            $this->r(1,$this->smax('oasis_chance'),'oasis');
        }
    }

    private function rule_desert_and_ice_seeding() 
    {     
        if ($this->cornered_by('mountain'))
        {
            $this->r($this->smin('biome_chance'),$this->smax('biome_chance'),Array('desert','ice'));
        }
    }
    
    private function rule_desert_and_ice_swap() 
    {
        if ($this->tile == 'desert')
        {
            $this->r($this->smin('desert_to_ice'),$this->smax('desert_to_ice'),'not_yet_ice');
        }
        
        if ($this->tile == 'ice')
        {
            $this->r($this->smin('ice_to_desert'),$this->smax('ice_to_desert'),'desert');    
        }
        
        if ($this->tile == 'not_yet_ice')
        {
            $this->tile = 'ice';
        }
    }
    
    private function rule_desert_generation() 
    {
        $this->biome('desert');
        
        $this->beach('desert','sand');
    }
    
    private function rule_ice_generation() 
    { 
        $this->biome('ice');
        
        $this->beach('ice','ice_beach');
    }

    private function rule_lava_generation() 
    {   
        if ($this->tile == 'desert')
        {
            $this->r(1,$this->smax('lava_chance'),'lava');
        }
    }
     
    private function rule_desert_polishing() 
    {  
        if ($this->tile == 'desert' && $this->around_green())
        {
            $this->tile = 'desert_blend';
        }

        if ($this->tile != 'water')
        {
            $this->spread('desert_blend',$this->smin('desert_blend_chance'),$this->smax('desert_blend_chance'));
        }
    }
    
    private function rule_snow_generation() 
    {  
        if ($this->tile == 'ice')
        {   
            $this->r(1,$this->smax('snow_chance'),'snow');
        }
    }
     
    private function rule_ice_polishing() 
    {     
        if ($this->tile == 'ice' && $this->around_green())
        {
            $this->tile = 'ice_blend';
        }
 
        if ($this->tile != 'water')
        {
            $this->spread('ice_blend',$this->smin('ice_blend'),$this->smax('ice_blend'));
        }
    }
     
    private function rule_cave_generation() 
    {       
        if ($this->surrounded_by('mountain'))
        {
            $this->tile = 'cave';     
        }
        
        if ($this->surrounded_by('sand'))
        {
            $this->r(1,$this->smax('desert_cave_chance'),'desert_cave');
        }
    }
    
    private function rule_savannah_seeding() 
    {
        if ($this->tile == 'field' && $this->bordered_by('sand'))
        {
            $this->r($this->smin('harvest_seed'),$this->smax('harvest_seed'),'harvest');
        }
    }
    
    private function rule_savannah_spread() 
    {
        if ($this->near('harvest') && $this->tile != 'water')
        {
            $this->r($this->smin('harvest_spread'),$this->smax('harvest_spread'),'harvest');
        } 
    }
    
    private function rule_big_tree_generation() 
    {
        if ($this->near('harvest') && $this->tile == 'tree')
        {
            $this->tile = 'bigtree';
        }
        
        if ($this->near('harvest'))
        {
            $this->r(1,$this->smax('bigtree_chance'),'bigtree');
        }
    }
    
    private function rule_savannah_polishing() 
    {
        if ($this->around('harvest') && $this->tile == 'mountain')
        {
            $this->tile = 'harvest_mountain';
        }
        
        $this->beach('harvest','harvest_beach');
    }

    // -------------
    // -------------
    
    private function get_the_loc($x_inc=0,$y_inc=0)
    {
        return $this->loc[$this->x+$x_inc][$this->y+$y_inc];    
    }
    
    private function left()
    {
        return $this->get_the_loc(-1);
    }
    
    private function right()
    {
        return $this->get_the_loc(1);
    }
    
    private function up()
    {
        return $this->get_the_loc(0,-1);
    }
    
    private function down()
    {
        return $this->get_the_loc(0,1);
    }
    
    private function cornered_by($n)
    {
        if ($this->left() == $n && $this->up() == $n && $this->loc[$this->x-1][$this->y-1] == $n)
        {
            return true;
        }
    }
    
    private function bordered_by($n)
    {
        if ($this->left() == $n && $this->up() == $n)
        {
            return true;
        }
    }
    
    private function near($n)
    {
        if ($this->left() == $n || $this->up() == $n)
        {
            return true;
        }
    }
    
    private function around_green()
    {
        return $this->around(Array('field','forest','mountain'));
    }
    
    private function around($arr)
    {
        if (!is_array($arr))
        {
            $arr = Array($arr);
        }
        foreach ($arr?:Array() AS $n)
        {
            if ($this->left() == $n || $this->right() == $n || $this->up() == $n || $this->down() == $n)
            {
                return true;    
            }
        }
    }
    
    private function surrounded_by($n)
    {
        if ($this->left() == $n && $this->right() == $n && $this->up() == $n && $this->down() == $n)
        {
            return true;
        }
    }
    
    private function diag_to($n)
    {
        if ($this->get_the_loc(-1,-1) == $n 
        ||  $this->get_the_loc(-1,1) == $n 
        ||  $this->get_the_loc(1,-1) == $n 
        ||  $this->get_the_loc(1,1) == $n)
        {
            return true;
        }
    }
    
    // -----------
    
        private function set($val)
        {
            $this->set_point(0,0,$val);
            $this->tile = $val;
        }
    
        private function set_point($cx,$cy,$val)
        {
            $this->loc[$this->x+$cx][$this->y+$cy] = $val; 
        }
    
    private function connect($point,$value,$sculpt='')
    {
        $this->xy_connect($point,$value);
        $this->diag_connect($point,$value); 
    }
    
    private function xy_connect($point,$value)
    {
        $this->x_connect($point,$value);
        $this->y_connect($point,$value);        
    }
    
    // Originally a bug, but I like the way these generate better
    private function field_or_not($name)
    {
        if ($name != 'field') { return 1; }
        else { return 0; }
    }
    
    private function x_connect($point, $value, $xdir=-1)
    {
        $fix = $value;
        $xpoint = $xdir*$point;
        if ($this->x+$xpoint < 0)
        {
            $fix = $this->field_or_not($value); 
        }
        if ($this->tile == $value && $this->loc[$this->x+$xpoint][$this->y] == $fix)
        {
            for ($i = 1; $i < $point; $i++)
            {
                $this->set_point(($i*$xdir),0,$value);
            }
        }
    }
    
    private function y_connect($point, $value, $ydir=-1)
    {
        $fix = $value;
        $ypoint = $ydir*$point;
        if ($this->y+$ypoint < 0)
        {
            $fix = $this->field_or_not($value);
        }
        if ($this->tile == $value && $this->loc[$this->x][$this->y+$ypoint] == $fix)
        {
            for ($i = 1; $i < $point; $i++)
            {
                $this->set_point(0,($i*$ydir),$value);
            }
        }
    }
    
    private function diag_connect($point, $value, $xdir=-1, $ydir=-1)
    {
        $xpoint = $xdir*$point;
        $ypoint = $ydir*$point;
        
        $fix = $value;
        if ($this->x+$xpoint < 0 || $this->y+$ypoint < 0)
        {
            $fix = $this->field_or_not($value);
        }
        if ($this->tile == $value && $this->loc[$this->x+$xpoint][$this->y+$ypoint] == $fix)
        {
            for ($i = 1; $i < $point; $i++)
            {
                $this->set_point(($i*$xdir),($i*$ydir),$value);
            }
        }
    }
    
    // -----
        
    private function spread($tile,$min,$max)
    {
        if ($this->near($tile))
        {
            $this->r($min,$max,$tile);
        }
    }
    
    private function beach($from,$to)
    {
        if ($this->tile == $from && $this->around('water'))
        {
            $this->tile = $to;
        }
    }
    
    private function biome($tile)
    {
        if ($this->near($tile) && $this->tile != 'water')
        {
            $this->tile = $tile;
        }
    }
}