<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class Bootstrap {

    function __construct() {

        $controller = 'default';
        $action = 'index';
        $params = '';

        $url = !empty($_GET['url']) ? $_GET['url'] : 'default';
        $url = rtrim($url, '/');
        $url = explode('/', $url);

        $controller = $url[0];
        $url = array_slice($url, 1);

        $controller = ucfirst($controller) . 'Controller';
        $obj = new $controller;

        if (!empty($url[0]) && method_exists($obj, $url[0])) {
            $action = $url[0];
            $url = array_slice($url, 1);
        }

        if (!empty($url)) {
            $params = $url;
        }

        if (!empty($_POST)) {
            $obj->post = $_POST;
            unset($_POST);
        }

        $obj->{$action}($params);
    }

}

?>