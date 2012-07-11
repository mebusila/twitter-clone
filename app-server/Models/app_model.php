<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class AppModel extends Model {

    var $name = 'AppModel';

    function __construct() {
        parent::__construct();
        $this->mongo = MongoDBSingleton::instance($this->config->mongo['host'], $this->config->mongo['port']);
        $this->db = $this->mongo->selectDB($this->config->db);
        $this->collection = new MongoCollection($this->db, $this->uses);
    }

    public function insert($data, $safe = false) {
        $this->collection->insert($data, $safe);
    }

    public function find($filter = NULL) {
        if (!empty($filter))
            return $this->collection->find($filter);
        else
            return $this->collection->find();
    }

    public function findOne($filter) {
        return $this->collection->{$this->uses}->findOne($filter);
    }

    public function update($criteria, $data) {
        return $this->collection->update($criteria, $data);
    }

    public function count($criteria = NULL) {
        return $this->collection->count($criteria);
    }

    public function remove($criteria = NULL) {
        return $this->collection->remove($criteria);
    }

}

?>
