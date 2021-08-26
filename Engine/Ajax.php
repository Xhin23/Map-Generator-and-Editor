<?php
Class Engine_Ajax
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
    
    public function do_changes($data)
    {
        return $this->engine_changes->{$data['func']}($data);
    }
    
    public function do_publish($data)
    {
        $batch_data = $data['batch_data'];
        if ($batch_data) { $batch_data = json_encode($batch_data); }
        
        $this->write_shared->publish(
            $_POST['sid'],
            $data['obj'],
            $data['func'],
            $data['batch_func'],
            $batch_data,
            $data['var1'],
            $data['var2'],
            $data['var3'],
            $data['var4'],
            $data['var5']
        );
    }
    
    public function do_get_new($data)
    {
        $data = $this->read_shared->get_new($_POST['sid'],$data['shareid']);
        foreach ($data?:Array() AS $i => $row)
        {
            if ($row['batch_data'])
            {
                $data[$i]['batch_data'] = json_decode($row['batch_data']);
            }
        }
        return $data;
    }
}
