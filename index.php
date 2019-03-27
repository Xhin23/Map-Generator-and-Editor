<?php
require "php/functions.php";
require "php/ZXC.php";

ZXC::INIT('<HOST>', '<USERNAME>', '<PASSWORD>','<DATABASE>');
ZXC::CONF('result_type','ASSOC');
ZXC::CONF('old_mode',true);

function autoloader($class) {

    $class = explode('_',$class);
    $include_path =  ucfirst($class[0]).'/'.ucfirst($class[1]).'.php';
  
    if (!stream_resolve_include_path($include_path))
    {
        return;
    }

    include_once $include_path;
}

set_include_path(get_include_path() . PATH_SEPARATOR . "./map");
spl_autoload_register('autoloader');

require "Mapgame.php";
$obj = new Mapgame();

$REQ = explode('/',$_SERVER['REQUEST_URI']);
array_shift($REQ);

if ($_POST['save_map'])
{
    $link = $obj->save_map($_POST);
    if (is_array($link))
    {
        echo '<div class="error"><ul><li>'.implode('</li><li>',$link).'</li></ul></ul>';
        die;
    }
    
    redir_to($link);
}
elseif ($REQ[1] == 'ajax')
{
    $obj->set_gameid($_POST['gameid']);
    $obj->ajax();
}
elseif ($REQ[1] && !$_GET)
{
    $obj->game($REQ[1]);
}
else
{
    $obj->map_generator();
}
