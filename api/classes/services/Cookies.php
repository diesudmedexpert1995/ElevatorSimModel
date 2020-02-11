<?php


class Cookies
{
    public function setCookie($name, $value)
    {
        setcookie($name,$value,time()+3600);
    }

    public function getCookie($name){
        return $_COOKIE[$name];
    }

}