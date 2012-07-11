<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class PostsController extends Controller {

    var $models = array('Post', 'User');

    public function index($user = null) {
        if (!empty($user)) {
            $this->view->posts = $this->Post->findLatestPostsByUser($user[0]);
        } else {
            $this->view->posts = $this->Post->findLatestPosts();
        }
        $this->view->render('posts/list');
    }

    public function add() {
        $data = $this->post;
        if ($this->isValidUser()) {
            $post = $this->Post->add($data['message'], $data['user_id']);
            if (!empty($post)) {
                $this->view->post = $post;
                $this->view->render('posts/add');
            }
        }
        header('HTTP/1.0 401 Unauthorized');
    }

}

?>
