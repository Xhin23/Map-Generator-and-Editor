<?php

$dir = scandir('.');

sort($dir, SORT_NUMERIC);

echo '<table>';
foreach ($dir AS $img)
{
    if ($img == '.' || $img == '..' || $img == 'index.php' || $img == 'unused') { continue; }
    echo '<tr><td>'.str_replace('.png','',$img).'</td><td><img src="'.$img.'" /></td></tr>';
}
echo '</table>';