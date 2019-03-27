<?php
Class Engine_Init
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
    
    private $data;
    public function render()
    {
        $this->regions();
        $this->sprites();
        $this->tiles();
        $this->rules();
        $this->entries();
        $this->explore();
        
        return $this->data;
    }
    
        private function set($name,$data,$init='data')
        {
            $this->data[$name] = Array('init' => $init, 'data' => $data);
        }
    
    private $region_tiles = Array();
    
    public function regions() 
    {
        $tiles = Array();
        $arr = $this->read_regions->get_all_tiles();
        $this->region_tiles = $arr;
        foreach ($arr?:Array() AS $tile)
        {
            $tiles[$tile['regionid']][] = Array($tile['x'],$tile['y']);
        }
        
        $regions = Array();
        $arr = $this->read_regions->get_all();
        foreach ($arr?:Array() AS $region)
        {
            $region['coords'] = $tiles[$region['regionid']]?:Array();
            $regions[$region['regionid']] = $region;
        }
        
        $this->set('regions',$regions);
    }
    
    public function sprites($return=false)
    {
        $sprites = Array('terrain' => Array(), 'fg' => Array());
        $arr = $this->read_sprites->get_all();
        foreach ($arr?:Array() AS $sprite)
        {
            $sprites[$sprite['type']][$sprite['name']] = $sprite['url'];
        }
        
        if ($return) { return $sprites; }
        
        $this->set('custom_tiles',$sprites,'obj');
    }
    
        private function img($dir) { 
            $imgs = scandir('map/__images/'.$dir.'/');
            $arr = Array();
            foreach ($imgs as $img) 
            {
                if ($img == '.' || $img == '..') { continue; } 
                if (substr($img,0,1) == '_') { continue; }
                $img = explode('.',$img); 
                if ($img[1] != 'png') { continue; }
                $arr[] = $img[0];
            }
            return $arr;
        }
    
    public function tiles()
    {
        $tiles = Array();
        $tiles['terrain'] = $this->img('terrain');
        $tiles['fg'] = $this->img('ents');                
        
        $this->set('tiles',$tiles);         
    }
    
    public function rules()
    {
        $rules = Array('fg' => Array(), 'terrain' => Array(), 'region' => Array());
        
        $arr = $this->read_rules->get_all();
        foreach ($arr?:Array() AS $rule)
        {
            $rules[$rule['entype']][$rule['entid']][$rule['ruleid']] = $rule;
        }
        
        $this->set('content',$rules);
    }
    
    public function entries()
    {
        $entries = $this->read_rules->get_entries();
        foreach ($entries?:Array() AS $entry)
        {
            $this->data['content']['data']  [$entry['entype']][$entry['entid']]  [$entry['ruleid']]  ['entries'][$entry['entryid']] = Array(
                'name' => $entry['name'], 
                'weight' => $entry['weight']
            );
        }
    }
    
        private function init_coord($coord,&$coords)
        {
            $xy = $coord['x'].'-'.$coord['y'];
            if (!$coords[$xy])
            {
                $coords[$xy] = Array('regions' => Array(), 'name' => '', 'desc' => '');
            }
        }
    
    public function explore()
    {
        $coords = Array();
        foreach ($this->region_tiles?:Array() AS $tile)
        {
            $xy = $tile['x'].'-'.$tile['y'];
            $this->init_coord($tile,$coords);
            if (!has($coords[$xy]['regions'],$tile['regionid']))
            {
                $coords[$xy]['regions'][] = $tile['regionid'];
            }
        }

        $places = $this->read_tiles->has_name();
        foreach ($places?:Array() AS $place)
        {
            $xy = $place['x'].'-'.$place['y'];
            $this->init_coord($place,$coords);
            $coords[$xy]['name'] = $place['name'];
            $coords[$xy]['desc'] = $place['desc'];
        }

        $this->set('explore',$coords);
    }
}