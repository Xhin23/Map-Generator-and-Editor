<?php

// TODO: remove debugging tools

function d($backticks=false)
{   
    ZXC::CONF('debug_all',true);
    if ($backticks)
    {
        ZXC::CONF('debug_backticks',true);
    }
}

function ud()
{
    ZXC::CONF('debug_all',false);
}

function flow()
{
    $args = func_get_args();
    $i = 1;
    $imax = count($args);
    echo '<pre>';
    foreach($args AS $arg)
    {
        if (is_array($arg) || is_object($arg))
        {
            print_r($arg);
        }
        else 
        {
            echo $arg;
        }
        
        if ($i != $imax)
        {
            echo ' -- ';
        }
        $i++;
    }
    echo '</pre>';
}

function print_x($x) { echo '<pre>'; print_r($x); echo '</pre>'; }

// --

function redir_to($url)
{
    ?><script type="text/JavaScript">window.location.href = '<?=$url?>';</script><?php
    die;
}

function has($haystack,$needle,$i=false)
{
    // if the needle is an array
    if (is_array($needle))
    {
        foreach ($needle?:Array() AS $unit)
        {
            if (has($haystack,$unit,$i))    
            {
                return true;    
            }
        }
        return false;
    }
    
    // if the haystack is an array
    if (is_array($haystack))
    {
        return in_array($needle,$haystack);
    }
    
    // if the haystack is a string
    else
    {
        // case-insensitivity
        if ($i)
        {
            if (stripos($haystack,$needle) !== false)
            {
                return true;
            }
        }
        // case-sensitive (default)
        else
        {   
            if (strpos($haystack,$needle) !== false)
            {
                return true;
            }
        }
    }
}

function slug($var)
{
    $var = strtolower(str_replace(' ','_',$var));
    $var = preg_replace("/[^A-Za-z0-9_]/", '', $var);
    return $var;
}

function deslug($var)
{
    return ucwords(str_replace(Array('_','-'),' ',$var));
}

function pick($arr)
{
    $key = array_rand($arr);
    return $arr[$key];
}

function pair($arr,$id,$name='')
{
    if (!$arr) { return Array(); }
    $new_arr = Array();
    foreach ($arr?:Array() AS $row)    
    {
        $row['id'] = $row[$id]; 
        $new_arr[$row[$id]] = $row;
        // For the CP's select functionality
        if ($name)
        {
            $new_arr[$row[$id]]['name'] = $row[$name];
        }
    }
    return $new_arr;
}

function map_deep( $value, $callback ) {
        if ( is_array( $value ) ) {
                foreach ( $value as $index => $item ) {
                        $value[ $index ] = map_deep( $item, $callback );
                }
        } elseif ( is_object( $value ) ) {
                $object_vars = get_object_vars( $value );
                foreach ( $object_vars as $property_name => $property_value ) {
                        $value->$property_name = map_deep( $property_value, $callback );
                }
        } else {
                $value = call_user_func( $callback, $value );
        }

        return $value;
}

function stripslashes_from_strings_only( $value ) {
        return is_string( $value ) ? stripslashes( $value ) : $value;
}

function deepslash( $value ) {
        return map_deep( $value, 'stripslashes_from_strings_only' );
}

if ($_POST)
{
    $_POST = deepslash($_POST);
}
