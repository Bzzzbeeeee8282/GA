<?php

class Res{

    private static $root = "http://localhost/php-router";
    public static function debug($data){
        echo "<pre>";
        var_dump($data);
        echo "<pre>";
    }   

    public static function json($data){

        header("content-type:application/json");
        echo json_encode($data);
    }

    public static function redirect($path){

        header("Location:".self::$root.$path);

    }
    public static function render($content){

       $template = file_get_contents("template.html");
       $template = str_replace("--content--", $content, $template);
       echo $template;

    }


}
