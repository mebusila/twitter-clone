<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class PostModel extends AppModel {

    var $name = 'PostModel';
    var $uses = 'posts';

    public function findLatestPosts() {
        $posts = array();
        $user = new UserModel;
        $cursor = $this->find();
        $cursor->sort(array('created' => -1));
        $posts['current_user'] = $user->getLoggedInUser();

        foreach ($cursor as $doc) {
            $posts['items'][] = array(
                'id' => $doc['_id']->__toString(),
                'message' => $doc['message'],
                'created' => $doc['created']['date'],
                'user' => $user->find(array('_id' => new MongoId($doc['user_id'])))->getNext()
            );
        }
        return $posts;
    }

    public function findLatestPostsByUser($slug = NULL) {
        $posts = array();
        $user = new UserModel;
        $posts['current_user'] = $user->getUserBySlug($slug);

        $cursor = $this->find(array('user_id' => $posts['current_user']['id']));
        $cursor->sort(array('created' => -1));

        foreach ($cursor as $doc) {
            $posts['items'][] = array(
                'id' => $doc['_id']->__toString(),
                'message' => $doc['message'],
                'created' => $doc['created']['date'],
                'user' => $posts['current_user']
            );
        }
        return $posts;
    }

    public function add($message, $user_id) {
        $post = array(
            'message' => $message,
            'user_id' => $user_id,
            'created' => new DateTime
        );
        $this->insert($post, true);
        return $post;
    }

}

?>
