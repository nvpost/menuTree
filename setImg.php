<?php

spl_autoload_register(function ($class_name) {
    include 'classes/'.$class_name . '.php';
});
$t = new TimeLogClass('добавляем картинки');

require_once 'func.php';



$root_menu = checkMainMenuCache("MainMenu");

//deb($root_menu);



function loopArr($arr){
    foreach ($arr as $k => $item){
        //deb($item);
        if(isset($item['child'])){
            loopArr($item['child']);
        }
        else{
            $cat_id = $item['cat_id'];
            $updateData = array(
                'cat_id' => $cat_id,
                'src' => $item['src']
            );
            setImg($updateData);
        }
    }
}



function setImg($updateData){
    deb($updateData);
    global $db;
    $sql = "UPDATE category SET src=:src WHERE cat_id=:cat_id";
    $res = $db->prepare($sql);
    deb($db->errorInfo());

    $res->execute($updateData);
    deb($res);
}


loopArr($root_menu);


$t->timerStop();

//Запрос нового кеша из индексного файла
