<table class="map">
<?php

$s = $settings['image_size']-4;

for ($y = 0; $y < $settings['height']; $y++)
{
    echo '<tr>';
    for ($x = 0; $x < $settings['width']; $x++)
    {
        $style = $css[$x][$y];
        $sprite = $map[$x][$y]; 
        $gen = $gens[$x][$y]?:$sprite;
        $fg = $fgs[$x][$y];  
        
        $terrain_url = $custom['terrain'][$sprite] ?: '/map/__images/terrain/'.$sprite.'.png';
        
        echo '<td id="coord-'.$x.'-'.$y.'" class="tile '.$class.'" tile="'.$sprite.'" original_fg="" original_tile="'.$sprite.'" gen="'.$gen.'" x="'.$x.'" y="'.$y.'">
        <img src="'.$terrain_url.'" style="'.$style.'" class="tile-terrain" />';
        if ($fg)
        {
            $fg_url = $custom['fg'][$fg] ?: '/map/__images/ents/'.$fg.'.png';
            
            echo '<img src="'.$fg_url.'" class="tile-ent" fg="'.$fg.'" style="width: '.$s.'px; height: '.$s.'px;" />';
        }
        echo '</td>';
    }
    echo '</tr>';
}
?>
</table>