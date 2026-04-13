<?php

class El{


    public static function h2($text){

        return "<h2>$text</h2>";

    }
    public static function p($text){

        return "<p>$text</p>";

    }
        public static function a($text, $href){

        return "<a href = '$href'>$text</a>";

    }



}