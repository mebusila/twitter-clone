<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class Model {

    var $name;
    var $config;

    public function __construct() {
        $this->config = Configuration::getInstance();
    }

}

?>
