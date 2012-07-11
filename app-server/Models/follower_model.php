<?php

/**
 *
 * @author Sherban Carlogea <sherban.carlogea@gmail.com>
 */
class FollowerModel extends AppModel {

    var $name = 'FollowerModel';
    var $uses = 'followers';

    public function followUser($source, $destination) {
        $follow = array(
            'user_id' => $destination,
            'follower_id' => $source
        );
        $this->insert($follow, true);
        return true;
    }

    public function unFollowUser($source, $destination) {
        $follow = array(
            'user_id' => $destination,
            'follower_id' => $source
        );
        $this->remove($follow);
        return true;
    }

}

?>
