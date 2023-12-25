<?php

require_once __DIR__ . '/../vendor/autoload.php';

$request = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim(explode('?', $request)[0], '/'));

$controllerName = $parts[0] ?: 'User';
$action = $parts[1] ?? 'index';

$controller = 'Kento1221\\UserUsergroupCrudApp\\Controllers\\' . ucfirst(strtolower($controllerName)) . 'Controller';

if (!class_exists($controller)) {
    exit("Controller `$controller` not found");
}

$controllerObject = new $controller();

if (!method_exists($controllerObject, $action)) {
    exit("Action `$action` not found");
}

$controllerObject->$action();