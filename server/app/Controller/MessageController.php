<?php
App::uses('ApiController', 'Controller');
App::uses('TimeHelper', 'Lib');

class MessageController extends ApiController {

    public $helpers = array('Time'); 
    public $uses = array('User', 'Conversation','Message', 'Room');

    public function index() {
        $user = $this->Session->read('Auth.User');

        $sql = "
        SELECT m.room_id as room_id,
            IF(m.sender_id = {$user['id']}, (SELECT profile as profile FROM users WHERE id = m.receiver_id), (SELECT profile as profile FROM users WHERE id = m.sender_id)) as profile,
            IF(m.sender_id = {$user['id']}, (SELECT CONCAT(fname,' ',lname) as fullname FROM users WHERE id = m.receiver_id), (SELECT CONCAT(fname,' ',lname) as fullname FROM users WHERE id = m.sender_id)) as fullname,
            (SELECT IF(sender_id = {$user['id']}, CONCAT('me:',' ',content), content) as content FROM messages WHERE id = max(c.latest_message_id)) as content,
            (SELECT created FROM messages WHERE id = max(c.latest_message_id)) as created,
            (SELECT count(status) FROM messages WHERE room_id = c.room_id and receiver_id = {$user['id']} and status = 0) as countunreadmessage
        FROM messages m left join conversations c on m.id = c.latest_message_id 
        WHERE (m.sender_id = {$user['id']} OR receiver_id = {$user['id']})
        GROUP BY m.room_id";

        $messages = $this->Message->query($sql);

        $mes = [];

        foreach($messages as $message){

            $TimeHelper = new TimeHelper(new View());
            $created_timestamp = strtotime($message[0]['created']);
            $created_ago = $TimeHelper->timeAgo($created_timestamp);

            $fullname = ucwords($message[0]['fullname']);

            $mes[] = array(
                'room_id' => $message['m']['room_id'],
                'fullname' => $fullname,
                'profile' => $message[0]['profile'],
                'content' => $message[0]['content'],
                'created' => $created_ago,
                'count' => $message[0]['countunreadmessage'],
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