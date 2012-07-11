<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class MongoDBSingleton {

    static $db = NULL;

    static function instance($host = 'localhost', $port = 27017) {
        if (self::$db === null) {
            try {
                self::$db = new Mongo('mongodb://'. $host .':' . $port);
            } catch(MongoConnectionException $e) {
                die('Failed to connect to MongoDB '.$e->getMessage());
            }
        }
        return self::$db;
    }
}
?>
