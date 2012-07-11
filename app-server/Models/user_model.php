<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class UserModel extends AppModel {

    var $name = 'UserModel';
    var $uses = 'users';
    var $Post;

    public function __construct() {
        parent::__construct();
        $this->Post = new PostModel;
        $this->Follower = new FollowerModel();
    }

    public function add($name, $email, $password) {
        $user = $this->find(array('email' => $email))->getNext();
        if (!empty($user)) {
            return array(
                'error' => 'This email address is already used!'
            );
        }

        $user = array(
            'name' => $name,
            'email' => $email,
            'email_hash' => md5($email),
            'password' => md5($password),
            'slug' => $this->createSlug($name),
            'created' => new DateTime,
            'last_login' => new DateTime
        );
        $this->insert($user, true);
        return $user;
    }

    public function getLoggedInUser() {
        if (!empty($_SESSION['user']['id'])) {
            $user = $this->find(array('_id' => new MongoId($_SESSION['user']['id'])))->getNext();
            if (!empty($user['_id'])) {
                $user['id'] = (string) $user['_id'];
                $user['created'] = $user['created']['date'];
                $user['last_login'] = $user['last_login']['date'];
                $user['posts_stats'] = $this->Post->count(array('user_id' => $user['id']));
                $user['following_stats'] = $this->Follower->count(array('follower_id' => $user['id']));
                $user['followers_stats'] = $this->Follower->count(array('user_id' => $user['id']));
                unset($user['_id']);
                return $user;
            }
            return false;
        }
        return false;
    }

    public function authenticate($email, $password) {
        $user = $this->find(array('email' => $email, 'password' => md5($password)))->getNext();
        if (!empty($user)) {
            $this->update(
                    array(
                '_id' => $user['_id']
                    ), array(
                '$set' => array(
                    'last_login' => new DateTime
                )
                    )
            );
            $user['id'] = (string) $user['_id'];
            unset($user['_id']);
            $user['token'] = $this->generateToken($user['id']);
            $_SESSION['user'] = $user;
            return $user;
        }
        return false;
    }

    public function getUserBySlug($slug) {
        $user = $this->find(array('slug' => $slug))->getNext();
        if (!empty($user['_id'])) {
            $user['id'] = (string) $user['_id'];
            $user['created'] = $user['created']['date'];
            $user['last_login'] = $user['last_login']['date'];
            $user['posts_stats'] = $this->Post->count(array('user_id' => $user['id']));
            $user['following_stats'] = $this->Follower->count(array('follower_id' => $user['id']));
            $user['followers_stats'] = $this->Follower->count(array('user_id' => $user['id']));
            $loggedUser = $this->getLoggedInUser();
            if (!empty($loggedUser)) {
                $user['logged_user_id'] = $loggedUser['id'];
                $user['is_following'] = $this->Follower->count(array('follower_id' => $loggedUser['id'], 'user_id' => $user['id']));
            }
            unset($user['_id']);
            return $user;
        }
        return false;
    }

    public function getUserbyId($user_id) {
        $user = $this->find(array('_id' => new MongoId($user_id)))->getNext();
        if (!empty($user['_id'])) {
            $user['id'] = (string) $user['_id'];
            $user['created'] = $user['created']['date'];
            $user['last_login'] = $user['last_login']['date'];
            $user['posts_stats'] = $this->Post->count(array('user_id' => $user['id']));
            $user['following_stats'] = $this->Follower->count(array('follower_id' => $user['id']));
            $user['followers_stats'] = $this->Follower->count(array('user_id' => $user['id']));
            $loggedUser = $this->getLoggedInUser();
            if (!empty($loggedUser)) {
                $user['logged_user_id'] = $loggedUser['id'];
                $user['is_following'] = $this->Follower->count(array('follower_id' => $loggedUser['id'], 'user_id' => $user['id']));
            }
            unset($user['_id']);
            return $user;
        }
        return false;
    }

    protected function createSlug($name) {
        $users = $this->find(array('name' => $name));
        if ($users->count() > 0) {
            $name .= ' ' . $users->count();
        }
        return strtolower(preg_replace('/[^A-Za-z0-9-]+/', '-', $name));
    }

    protected function generateToken($id) {
        return md5($id . time());
    }

}

?>
