<?php

function router($path,$http404,$action = null,$methods = 'POST|GET'){
    static $routes = [];
    if($action){
        return $routes['('.$methods.')_'.$path] = $action;
    }
    foreach ($routes as $route => $action) {
        $regEx = "~^$route/?$~i";
        $matches = [];
        if (!preg_match($regEx, $path, $matches)) {
            continue;
        }
        if (!is_callable($action)) {
            return call_user_func_array($http404,$matches);
        }
        array_shift($matches);
        array_shift($matches);
        return call_user_func_array($action,$matches);
    }
    return call_user_func_array($http404,[$path]);
}