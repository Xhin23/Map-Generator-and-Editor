<?php
Class Engine_Tiles
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
    
    public function edit_terrain($x,$y,$name)
    {
        $this->write_tiles->set_terrain($x,$y,$name);
    }
    
    public function undo_terrain($x,$y)
    {
        return $this->edit_terrain($x,$y,'');
    }
    
    public function edit_fg($x,$y,$name)
    {
        $this->write_tiles->set_fg($x,$y,$name);
    }
    
    public function undo_fg($x,$y)
    {
        return $this->edit_fg($x,$y,'');
    }
    
    public function revert($x,$y)
    {
        $this->undo_terrain($x,$y);
        $this->undo_fg($x,$y);
    }
    
    public function edit_tiles($data)
    {
        $edited_tiles = $this->read_tiles->get_all();
        
        $data['fg'] = Array();
        foreach ($edited_tiles?:Array() AS $row)
        {
            if ($row['terrain'])
            {
                $data['gen'][$row['x']][$row['y']] = $data['map'][$row['x']][$row['y']];
                $data['map'][$row['x']][$row['y']] = $row['terrain'];
            }
            if ($row['fg'])
            {
                $data['fg'][$row['x']][$row['y']] = $row['fg'];
            }
        }
        
        return $data;
    }
}