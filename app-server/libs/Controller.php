<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class Controller {

    var $models = array();
    protected $data = null;

    function __construct() {
        if (!empty($this->models)) {
            foreach ($this->models as $model) {
                $this->_loadModel($model);
            }
        }
        $this->view = new View();
    }

    private function _loadModel($model) {
        $model_name = ucfirst($model) . "Model";
        return $this->{ucfirst($model)} = new $model_name;
    }

    public function __set($name, $value) {
        $this->data[$name] = $value;
    }

    public function __get($name) {
        return $this->data[$name];
    }

    protected function isValidUser() {
        $data = $this->post;
        if (!empty($data) &&
                $data['user_id'] == $_SESSION['user']['id'] &&
                $data['token'] == $_SESSION['user']['token']) {
            return true;
        }
        return false;
    }

    public function index() {
        return;
    }

}

?>
