<?php
App::uses('ApiController', 'Controller');

class MessageController extends ApiController {

    public $uses = array('User', 'Conversation', 'Message');

    public function index() {
        $user = $this->Session->read('Auth.User');

        $sql = "SELECT 
            IF(receiver_id = '".$user['id']."', b.fname, (SELECT fname FROM users WHERE id = receiver_id)) AS firstname, 
            IF(receiver_id = '".$user['id']."', b.lname, (SELECT lname FROM users WHERE id = receiver_id)) AS lastname
        FROM messages a 
        LEFT JOIN users b ON a.sender_id = b.id
        WHERE (sender_id = '".$user['id']."' OR receiver_id = '".$user['id']."') 
        GROUP BY convo_id";

        $messages = $this->Message->query($sql);

        $mes = [];

        foreach($messages as $message){
            $mes[] = array(
                'fname' => $message[0]['firstname'],
                'lname' => $message[0]['lastname'],
            );

        }

        $response = [
            'status' => 200,
            'result' => $mes,
            'success' => true,
        ];

        $this->response->statusCode($response['status']);
        return $this->response->body(json_encode($response));
    }
}