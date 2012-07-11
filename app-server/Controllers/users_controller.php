<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class UsersController extends Controller {

    var $models = array('Post', 'User', 'Follower');

    public function login() {
        $data = $this->post;
        if (!empty($data)) {
            $user = $this->User->authenticate($data['email'], $data['password']);
            if (!empty($user)) {
                $this->view->user = $user;
                $this->view->render('users/login');
            }
        }
        header('HTTP/1.0 401 Unauthorized');
    }

    public function register() {
        $data = $this->post;
        if (!empty($data)) {
            $user = $this->User->add($data['name'], $data['email'], $data['password']);
            echo json_encode($user);
        }
    }

    public function follow($user = null) {
        $loggedUser = $this->User->getLoggedInUser();
        if (!$loggedUser) {
            header('HTTP/1.0 401 Unauthorized');
        }
        $destinationUser = $this->User->getUserBySlug($user[0]);
        if ($destinationUser && $this->Follower->followUser($loggedUser['id'], $destinationUser['id'])) {
            $this->view->data = array('success' => true);
            $this->view->render('users/follow');
        }

        header('HTTP/1.0 400 Not found');
    }

    public function unfollow($user = null) {
        $loggedUser = $this->User->getLoggedInUser();
        if (!$loggedUser) {
            header('HTTP/1.0 401 Unauthorized');
        }
        $destinationUser = $this->User->getUserBySlug($user[0]);
        if ($destinationUser && $this->Follower->unFollowUser($loggedUser['id'], $destinationUser['id'])) {
            $this->view->data = array('success' => true);
            $this->view->render('users/follow');
        }

        header('HTTP/1.0 400 Not found');
    }

    public function following($user = null) {
        $user = $this->User->getUserBySlug($user[0]);
        $cursor = $this->Follower->find(array('follower_id' => $user['id']));
        foreach ($cursor as $following) {
            $users[] = $this->User->getUserbyId($following['user_id']);
        }
        $this->view->data = array(
            'current_user' => $user,
            'items' => $users
        );
        $this->view->render('users/following');
    }

    public function followers($user = null) {
        $user = $this->User->getUserBySlug($user[0]);
        $cursor = $this->Follower->find(array('user_id' => $user['id']));
        foreach ($cursor as $following) {
            $users[] = $this->User->getUserbyId($following['follower_id']);
        }
        $this->view->data = array(
            'current_user' => $user,
            'items' => $users
        );
        $this->view->render('users/following');
    }

    public function isAuthenticated($user = NULL) {
        if (!empty($_SESSION['user']) && $user = $_SESSION['user'])
            return true;
        header('HTTP/1.0 401 Unauthorized');
    }

    public function logout() {
        $_SESSION['user'] = null;
        unset($_SESSION['user']);
    }

}

?>
