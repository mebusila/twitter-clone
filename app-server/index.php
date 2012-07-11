<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
require 'libs/Configuration.php';
require 'config.php';

require 'libs/Bootstrap.php';
require 'libs/Controller.php';
require 'libs/View.php';
require 'libs/Model.php';
require 'libs/general.php';
require 'libs/mongo.php';

function __autoload($classname) {

    if ((strripos($classname, 'Model') + 5) == strlen($classname)) {
        $filename = 'Models/' . from_camel_case($classname) . '.php';
    }

    if ((strripos($classname, 'Controller') + 10) == strlen($classname)) {
        $filename = 'Controllers/' . from_camel_case($classname) . '.php';
    }

    if (!empty($filename) && file_exists($filename)) {
        require_once $filename;
        return true;
    }
    throw new Exception('File not found: ' . $filename);
}

session_start();
$app = new Bootstrap();
?>
