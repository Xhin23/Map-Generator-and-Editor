<?php
Class Engine_Changes
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
    
    public function edit_tiles($data)
    {
        $data['tiles'] = json_decode($data['tiles']);
        
        $func = $data['subfunc'];
        foreach ($data['tiles']?:Array() AS $tile)
        {
            $this->engine_tiles->$func($tile[0],$tile[1],$data['var1']);
        }
    }
    
    public function delete_tile_region($data)
    {
        $this->write_regions->remove_tile($data['regionid'],$data['x'],$data['y']);
    }
    
    public function add_region($data)
    {
        return $this->write_regions->add_region($data['name'],$data['desc'],$data['color']);
    }
    
    public function edit_region($data)
    {
        $this->write_regions->update_region($data['id'],$data['name'],$data['desc'],$data['color']);
    }
    
    public function delete_region($data)
    {
        $this->write_regions->reset_coords($data['id']);
        $this->write_regions->delete_region($data['id']);
    }
    
    public function set_region($data)
    {
        $data['coords'] = json_decode($data['coords']);
        
        $this->write_regions->reset_coords($data['id']);
        foreach ($data['coords']?:Array() AS $coord)
        {
            $this->write_regions->add_tile($data['id'],$coord[0],$coord[1]);
        }
    }
    
    // --
    
    public function add_image($data)
    {
        return $this->write_sprites->add($data['type'],$data['name'],$data['url']);
    }
    
    // --
    
    public function undo_tiles($data)
    {
        $data['coords'] = json_decode($data['coords']);
        
        $func = 'undo_'.$data['type'];
        foreach ($data['coords']?:Array() AS $tile)
        {
            $this->engine_tiles->$func($tile[0],$tile[1]);
        }
    }
    
    public function revert($data)
    {
        $data['coords'] = json_decode($data['coords']);
        
        foreach ($data['coords']?:Array() AS $tile)
        {
            $this->engine_tiles->revert($tile[0],$tile[1]);
        }
    }
    
    // ------
    
    public function add_rules($data)
    {
        $entype = $data['entype'];
        $entid = $data['entid'];
        
        $arr = Array();
        $rules = explode("\n",$data['rules']);
        foreach ($rules?:Array() AS $rule)
        {
            $rule = explode('--',$rule);
            $name = trim($rule[0]);
            $chance = trim($rule[1]);
            $ruleid = $this->write_rules->add_rule($entype,$entid,$name,$chance,$data['seed']);
            $arr[$ruleid] = Array('name' => $name, 'chance' => $chance, 'ruletype' => 'trait', 'seed' => $data['seed']);
        }
        
        return $arr;
        
    }
    
    public function update_rules($data)
    {
        foreach ($data['rules']?:Array() AS $rule)
        {
            $this->write_rules->update_rule($rule['ruleid'],$rule['name'],$rule['ruletype'],$rule['chance'],$rule['seed'],$rule['min'],$rule['max']);
        }
    }
    
    public function delete_rule($data)
    {
        $this->write_rules->delete_rule($data['ruleid']);
    }
    
    // ------
    
    public function add_entries($data)
    {
        $ruleid = $data['ruleid'];
        $arr = Array();
        $entries = explode("\n",$data['entries']);
        foreach ($entries?:Array() AS $entry)
        {
            $entry = explode('--',$entry);
            $name = trim($entry[0]);
            $weight = trim($entry[1]);
            $entryid = $this->write_rules->add_entry($ruleid,$name,$weight);
            $arr[$entryid] = Array('name' => $name, 'weight' => $weight);
        }
        return $arr;
    }
    
    public function edit_entries($data)
    {
        foreach ($data['entries']?:Array() AS $entry)
        {
            $this->write_rules->update_entry($entry['entryid'],$entry['data']['name'],$entry['data']['weight']);
        }
    }
    
    public function delete_entry($data)
    {
        $this->write_rules->delete_entry($data['entryid']);
    }
    
    // ----
    
    public function edit_tile_data($data)
    {
        $this->write_tiles->set_data($data['x'],$data['y'],$data['name'],$data['desc']);
    }
    
}