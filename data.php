<?php

class Data{

public static function getData($file){

$filename = $file . ".json";

$data = file_get_contents($filename);
return json_decode($data, true);

}


public static function saveData($file, $data){

$filename = $file . ".json";

//convert data  to json

//JSON_PRETTY_PRINT: Flagga för att det ska se snyggare ut i filen 

$data = json_encode($data, JSON_PRETTY_PRINT);

file_put_contents($filename, $data);

}
}