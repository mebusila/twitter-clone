<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class Configuration {

    static $instance = NULL;
    protected $data;

    private function __construct() {

    }

    static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new Configuration;
        }
        return self::$instance;
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        if (empty($this->data[$name]))
            return false;
        return $this->data[$name];
    }

}
?>
