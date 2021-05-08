<?php

spl_autoload_register(function ($class_name) {
    include 'classes/'.$class_name . '.php';
});

$t = new TimeLogClass('вся страница');

require_once 'func.php';


function getMenu($p_id=0){
    $sql = "SELECT * FROM category WHERE parent_id =".$p_id;
    $menu = pdSql($sql);

    foreach ($menu as $k => $item){
        $child = getMenu($item['cat_id']);
        $menu[$k]['child'] = $child;
    }
    return $menu;
}



$root_menu = getMenu();




deb($root_menu);

c_deb("запросов к бд - ".$sql_count);


$t->timerStop();