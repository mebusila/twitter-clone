<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */

$config = Configuration::getInstance();
$config->mongo = array(
    'host' => 'localhost',
    'port' => 27017
);
$config->db = 'twitter-clone';
?>
