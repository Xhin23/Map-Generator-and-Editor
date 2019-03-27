<?php
Class Write_Shared
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
    
    public function publish($sid,$obj,$func,$batch_func,$batch_data,$var1,$var2,$var3,$var4,$var5)
    {
        return ZXC::ins('shared')->set(
            'sid', $sid,
            'obj', $obj,
            'gameid', $this->gameid,
            'func', $func,
            'batch_func', $batch_func,
            'batch_data', $batch_data,
            'var1', $var1,
            'var2', $var2,
            'var3', $var3,
            'var4', $var4,
            'var5', $var5
        )->go();
    }
}