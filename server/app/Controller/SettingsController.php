<?php
App::uses('ApiController', 'Controller');
App::uses('TimeHelper', 'Lib');

class SettingsController extends ApiController {

    public $uses = array('User', 'Room','Message', 'Conversation');

    public function index() {
        $data = array(
            'users' => $this->alluser(),
        );
        return $this->response->body(json_encode($data));
    }

    public function alluser(){
        $sql = "
        SELECT id, CONCAT(lname, ', ' ,fname) as fullname, profile FROM users";

        $allusers = $this->User->query($sql);

        $users = [];
        foreach($allusers as $user){
            $fullname = ucwords($user[0]['fullname']);
            $users[] = array(
                'id' => $user['users']['id'],
                'fullname' => $fullname,
                'profile' => $user['users']['profile'],
            );
        }

        return $users;
    }
}