<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class View {

    function __construct() {

    }

    public function render($name) {
        require 'Views/' . $name . '.php';
        exit();
    }

}

?>
