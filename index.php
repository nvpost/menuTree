<?php

spl_autoload_register(function ($class_name) {
    include 'classes/'.$class_name . '.php';
});

$t = new TimeLogClass('вся страница');

require_once 'func.php';

$menuItemForRemove = array();
$productCounter = 0;

function getMenu($p_id=0){
    global $menuItemForRemove;
    global $productCounter;
    $sql = "SELECT * FROM category WHERE parent_id =".$p_id;
    $menu = pdSql($sql);

    foreach ($menu as $k => $item){
        $child = getMenu($item['cat_id']);
        if($child){
            $menu[$k]['child'] = $child;
        }else{
            $prod = getProducts($item['cat_id']);
            if($prod){
                $menu[$k]['prod'] = $prod;
                //$productCounter = $productCounter + count($prod);
            }else{
                $menuItemForRemove[] = $menu[$k]['cat_id'];
                //removeCats($menu[$k]['cat_id']);
                unset($menu[$k]);
            }

        }

    }
    return $menu;
}

function getProducts($cat_id){
    $sql = "SELECT * FROM products WHERE category_id =".$cat_id;
    $prod = pdSql($sql, true);
    if($prod){
        $img_src = getImg($prod['prod_id']);
        if($img_src){
            $prod['src'] = $img_src;
        }
    }
    return $prod;
}

function getImg($prod_id){
    $sql = "SELECT * FROM img WHERE prod_id =".$prod_id;
    $img = pdSql($sql, true);
//    deb($img['src']);
    return $img['src'];
}


$root_menu = getMenu(0);

if($menuItemForRemove){
    $menuItemForRemove_list = implode(", ", $menuItemForRemove);
    deleteCats($menuItemForRemove_list);
    deb($menuItemForRemove_list);
}




deb($root_menu);



function deleteCats($menuItemForRemove_list){
    $sql = "DELETE FROM category WHERE cat_id IN(".$menuItemForRemove_list.")";
    global $db;
    global $menuItemForRemove;
    $res = $db->prepare($sql);
    deb($db->errorInfo());
    $res->execute();

    deb("Удаляем - ".$menuItemForRemove_list);
    deb("Удалили ".count($menuItemForRemove));
}

c_deb("запросов к бд - ".$sql_count);

$t->timerStop();