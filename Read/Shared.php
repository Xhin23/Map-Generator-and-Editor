<?php
Class Read_Shared
{
    public function __construct($gameid)
    {
        $this->gameid = $gameid;
    }
    
    public function last_shareid()
    {
        $id = ZXC::sel('>shareid/shared')->where('gameid',$this->gameid)->one();
        return $id;
    }
    
        private function share() 
        {
            return ZXC::sel('shareid,sid,obj,func,batch_func,batch_data,var1,var2,var3,var4,var5/shared')->where('gameid',$this->gameid);  
        }
    
    public function get_new($sid,$shareid)
    {
        return $this->share()->where('sid!=',$sid,'shareid>',$shareid)->sort('shareid++')->go();
    }
}