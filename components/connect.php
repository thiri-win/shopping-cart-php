<?php

$db_user = "root";
$db_password = "";
$db_name = 'mysql:host=localhost; dbname=shop';
$conn = new PDO($db_name, $db_user, $db_password);

function create_unique_id() {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $character_length = strlen($characters);
    $random = '';
    for($i = 0; $i < 20; $i++) {
        $random .= $characters[mt_rand(0, $character_length -1)];
    }
    return $random;
}