<div id="interface" class="map-generator">
<div class="form">
<form action="" method="GET" id="map-form">
    
<table id="basic-settings">
    <tr><td colspan="4"><h2>Basic Settings</h2></td></tr>
    <tr id="help"><td><i class="material-icons">help</i></td><td colspan=4">
        <a href="http://gtx0.com/j/mapgame/">Help</a>
        <a href="/map/demo">Demo Map</a>
    </tr>
    <?php foreach ($settings?:Array() AS $key => $value) 
    {
        $style = '';
        $class = '';
        $is_sculpt = 0;
        
        if (has($ignore_settings,$key)) { continue; }
         
        if (substr($key,0,6) == 'sculpt') 
        {
            $is_sculpt = 1;
            $class = 'sculpt sculpt-input';
            
            if (!$_GET['show_sculpt']) 
            {
                $style = 'display: none;'; 
            }
            
            if (!$sculpt_menu) 
            { 
                ?>
                <tr>
                    <td class="help-tile"><img src="__images/sculpt/sculpt_icon.png" /></td>
                    <td colspan="3"><a href="" id="toggle-sculpt">Toggle Continent Sculpting Options</a></td>
                </tr>
                <tr class="sculpt" style="<?=$style?>">
                    <td class="help-tile"><img src="__images/sculpt/sculpt_icon.png" /></td>
                    <td>Distribute units</td>
                    <td><input id="sculpt-dist" value="<?=$_GET['dist_units']?:16?>" class="input" name="dist_units" /></td>
                    <td><input type="button" id="sculpt-dist-button" value="&#10225;" /></td>
                </tr>
                <?php
                $sculpt_menu = true;
            }
        }
        
        $help_tile = '__images/'; 
        if ($is_sculpt) 
        {    
            $help_tile .= 'sculpt/'.str_replace('sculpt_','',$key);
        }
        elseif ($data['help_tiles'][$key])
        {
            $help_tile .= 'terrain/'.$data['help_tiles'][$key];
        }
        else
        {
            $help_tile .= 'terrain/_blank'.($i%2);
        }
        $i++;
        
        $ratio = 0;
        if (strpos($key,'_ratio') !== false) { $ratio = 1; }
        
        
    ?>
    <tr style="<?=$style?>" class="<?=$class?>">
        <td class="help-tile">
            <img src="<?=$help_tile?>.png" />
        </td>
        <td><?=deslug($key)?></td>
        <td>
            <input name="<?=$key?>" value="<?=$value?>" ratio="<?=$ratio?>" init="<?=$data['init_settings'][$key]?>" class="input"  autocomplete="off" />
        </td>
        <td>
        <?php if ($ratio) {
            $sel = ''; 
            if (has($hold_ratios,$key)) { $sel = ' sel'; }
        ?>
            <input type="button" class="stop-random switch-button<?=$sel?>" value="&#0248;" key="<?=$key?>" /> 
            <input type="button" class="rand-field" value="&#8635;" />
        <?php } ?>
        </td>
    </tr>
    <?php } ?>
</table> 
   
<table id="advanced-settings"> 
    <tr><td colspan="4"><h2>Disable Generation</h2></td></tr>
    <?php $i = 0;
    foreach ($data['rules']?:Array() AS $rule) 
    {
        $chek = '';
        $sel = '';
        if ($rule == 'seed') { continue; } 
        if (has($_GET['disable_rules'],$rule)) { $chek = 'checked="checked"'; $sel = 'sel'; }
    ?>
        <tr>
        <td>
            <label class="<?=$sel?>" row="<?=$i?>">
                <input name="disable_rules[]" type="checkbox" value="<?=$rule?>" <?=$chek?> />
                <span><?=deslug($rule)?></span>
            </label>
        </td>
        <td>
            <input type="button" class="toggle-down" value="v" row="<?=$i?>" />
        </td>
        </tr>
    <?php $i++; } ?>
</table>
<div id="submit-wrapper">
    <input type="hidden" id="show-sculpt" name="show_sculpt" value="<?=$_GET['show_sculpt']?:'0'?>" />
    <input type="hidden" id="hold-ratios" name="hold_ratios" value="<?=$_GET['hold_ratios']?>" />
    <input type="submit" value=" Submit " class="submit"  />
</div>

</form>
</div>
<div class="controls">
    <input type="button" id="random-seed" value=" Random Seed " /><br />
    <input type="button" id="random-ratios" value=" Randomize Ratios " /><br />
    <input id="reset-dims" type="button" value="Reset Dimensions" /><br />
    <input type="button" id="reset" value=" Reset All " />
</div>

<div id="save">
    <input type="button" value=" Use Map " />
</div>

<div id="save-form" style="display: none;">
    <form action="" method="POST">
    <input type="hidden" name="save_map" value="1" />
    <table>
        <tr><td>Name</td><td><input name="game[name]" id="name" class="input" value="Test" /></td></tr>
        <tr><td>URL Name</td><td><input name="game[slug]" id="slug" class="input" value="test" /></td></tr>
    </table>
    <input type="submit" class="submit" value=" Save " />
    </form>
</div>

</div>