<?php
// Instance of PDO object, connecting in mysql
$conn = new PDO('mysql:host=localhost;dbname=db_manwithavan', 'root', '');
// Select the active database
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function protection($string) {
    $string = str_replace(" or ", "", $string);
    $string = str_replace("select ", "", $string);
    $string = str_replace("delete ", "", $string);
    $string = str_replace("create ", "", $string);
    $string = str_replace("drop ", "", $string);
    $string = str_replace("update ", "", $string);
    $string = str_replace("drop table", "", $string);
    $string = str_replace("show table", "", $string);
    $string = str_replace("applet", "", $string);
    $string = str_replace("object", "", $string);
    $string = str_replace("'", "", $string);
    $string = str_replace("#", "", $string);
    $string = str_replace("=", "", $string);
    $string = str_replace("--", "", $string);
    $string = str_replace(";", "", $string);
    $string = str_replace("*", "", $string);
    $string = strip_tags($string);
    return $string;
}
?>