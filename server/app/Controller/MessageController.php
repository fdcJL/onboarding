<?php
App::uses('ApiController', 'Controller');
App::uses('TimeHelper', 'Lib');

class MessageController extends ApiController {

    public $helpers = array('Time'); 
    public $uses = array('User', 'Room','Message', 'Conversation');

    public function index() {
        $user = $this->Session->read('Auth.User');

        $sql = "
        SELECT m.room_id as room_id,
            IF(m.sender_id = {$user['id']}, m.receiver_id, m.sender_id) AS user_id,
            IF(m.sender_id = {$user['id']}, (SELECT profile as profile FROM users WHERE id = m.receiver_id), (SELECT profile as profile FROM users WHERE id = m.sender_id)) as profile,
            IF(m.sender_id = {$user['id']}, (SELECT CONCAT(fname,' ',lname) as fullname FROM users WHERE id = m.receiver_id), (SELECT CONCAT(fname,' ',lname) as fullname FROM users WHERE id = m.sender_id)) as fullname,
            (SELECT IF(sender_id = {$user['id']}, CONCAT('me:',' ',content), content) as content FROM messages WHERE id = max(c.latest_message_id)) as content,
            (SELECT created FROM messages WHERE id = max(c.latest_message_id)) as created,
            (SELECT COUNT(`status`) FROM messages WHERE room_id = m.room_id AND receiver_id = {$user['id']} AND `status` = 0) AS countunreadmessage
        FROM messages m left join conversations c on m.id = c.latest_message_id 
        WHERE (m.sender_id = {$user['id']} OR receiver_id = {$user['id']})
        GROUP BY m.room_id ORDER BY MAX(m.`created`) DESC";

        $messages = $this->Message->query($sql);

        if($messages){
            $mes = [];
            foreach($messages as $message){

                $TimeHelper = new TimeHelper(new View());
                $created_timestamp = strtotime($message[0]['created']);
                $created_ago = $TimeHelper->timeAgo($created_timestamp);
    
                $fullname = ucwords($message[0]['fullname']);
    
                $mes[] = array(
                    'room_id' => $message['m']['room_id'],
                    'user_id' => $message[0]['user_id'],
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
                'latest_chat' => $this->latest_chat($user['id']),
                'success' => true,
            ];
        }else{
            $response = [
                'status' => 400,
                'message' => "Message not found",
                'success' => false,
            ];
        }

        $this->response->statusCode($response['status']);
        return $this->response->body(json_encode($response));
    }

    public function sendmessage() {
        $param = $this->request->input('json_decode', true);
        $user = $this->Session->read('Auth.User');
        if ($this->request->is('post')) {

            $message = $this->Message->find('first', array(
                'conditions' => array(
                    'OR' => array(
                        array(
                            'sender_id' => $user['id'],
                            'receiver_id' => $param['receiver_id']
                        ),
                        array(
                            'sender_id' => $param['receiver_id'],
                            'receiver_id' => $user['id']
                        )
                    )
                )
            ));
    
            if ($message) {
                $this->Message->create();

                $param['room_id'] = $message['Room']['id'];
                $param['sender_id'] = $user['id'];

                if ($this->Message->save($param)) {
                    $lastid = $this->Message->id;
                    $conversation = $this->Conversation->findByRoomId($param['room_id']);

                    if ($conversation) {
                        $conversation['Conversation']['latest_message_id'] = $lastid;
                        $this->Conversation->save($conversation);

                        $response = [
                            'status' => 201,
                            'result' => 'Inserting Message',
                            'success' => true,
                        ];
                    } else {
                        $response = [
                            'status' => 400,
                            'message' => "Conversation not found",
                            'success' => false,
                        ];
                    }
                }
            } else {
                $this->Room->create();
                if ($this->Room->save()) {
                    $lastroomid = $this->Room->id;

                    $this->Message->create();
                    $param['room_id'] = $lastroomid;
                    $param['sender_id'] = $user['id'];
                    if ($this->Message->save($param)) {
                        $lastid = $this->Message->id;

                        $this->Conversation->create();
                        $convo['room_id'] = $param['room_id'];
                        $convo['latest_message_id'] = $lastid;

                        if ($this->Conversation->save($convo)) {
                            $sql = "
                            SELECT m.room_id as room_id,
                                IF(m.sender_id = {$user['id']}, m.receiver_id, m.sender_id) AS user_id,
                                IF(m.sender_id = {$user['id']}, (SELECT profile as profile FROM users WHERE id = m.receiver_id), (SELECT profile as profile FROM users WHERE id = m.sender_id)) as profile,
                                IF(m.sender_id = {$user['id']}, (SELECT CONCAT(fname,' ',lname) as fullname FROM users WHERE id = m.receiver_id), (SELECT CONCAT(fname,' ',lname) as fullname FROM users WHERE id = m.sender_id)) as fullname,
                                (SELECT IF(sender_id = {$user['id']}, CONCAT('me:',' ',content), content) as content FROM messages WHERE id = max(c.latest_message_id)) as content,
                                (SELECT created FROM messages WHERE id = max(c.latest_message_id)) as created,
                                (SELECT COUNT(`status`) FROM messages WHERE room_id = m.room_id AND receiver_id = {$user['id']} AND `status` = 0) AS countunreadmessage
                            FROM messages m left join conversations c on m.id = c.latest_message_id 
                            WHERE (m.sender_id = {$user['id']} OR receiver_id = {$user['id']}) AND m.room_id = {$param['room_id']}
                            GROUP BY m.room_id";

                            $messages = $this->Message->query($sql);

                            $mes = array();

                            foreach($messages as $message){

                                $TimeHelper = new TimeHelper(new View());
                                $created_timestamp = strtotime($message[0]['created']);
                                $created_ago = $TimeHelper->timeAgo($created_timestamp);

                                $fullname = ucwords($message[0]['fullname']);

                                $mes = array(
                                    'room_id' => $message['m']['room_id'],
                                    'user_id' => $message[0]['user_id'],
                                    'fullname' => $fullname,
                                    'profile' => $message[0]['profile'],
                                    'content' => $message[0]['content'],
                                    'created' => $created_ago,
                                    'count' => $message[0]['countunreadmessage'],
                                );

                            }

                            $response = [
                                'status' => 201,
                                'result' => $mes,
                                'index' => json_decode($this->index()),
                                'success' => true,
                            ];
                        } else {
                            $response = [
                                'status' => 400,
                                'message' => "Conversation not found",
                                'success' => false,
                            ];
                        }
                    } else {
                        $response = [
                            'status' => 400,
                            'message' => "Message not found",
                            'success' => false,
                        ];
                    }

                } else {
                    $response = [
                        'status' => 400,
                        'message' => "Room not found",
                        'success' => false,
                    ];
                }
            }

            $this->response->statusCode($response['status']);
            return $this->response->body(json_encode($response));
        }
    }

    public function chatroom(){
        
        $param = $this->request->input('json_decode', true);
        $user = $this->Session->read('Auth.User');

        $statusUpdate = "UPDATE messages SET status = 1 WHERE room_id = {$param['id']} AND status = 0 AND receiver_id = {$user['id']}";
        $this->Message->query($statusUpdate);

        $sql = "
        SELECT IF(m.sender_id = {$user['id']}, s.`profile`, NULL) AS me_profile,
            IF(m.sender_id = {$user['id']}, m.content, NULL) AS me,
            IF(m.receiver_id = {$user['id']}, r.`profile`, NULL) AS you_profile,
            IF(m.receiver_id = {$user['id']}, m.content, NULL) AS you, m.created
        FROM messages m 
            LEFT JOIN users s ON m.`sender_id` = s.`id` 
            LEFT JOIN users r ON m.`sender_id` = r.`id`
        WHERE m.room_id = {$param['id']}";

        $chatroom = $this->Message->query($sql);

        $convo = [];
        foreach($chatroom as $chat){

            $TimeHelper = new TimeHelper(new View());
            $created_timestamp = strtotime($chat['m']['created']);
            $created_ago = $TimeHelper->timeAgo($created_timestamp);

            $convo[] = array(
                'me_profile' => $chat[0]['me_profile'],
                'me' => $chat[0]['me'],
                'you_profile' => $chat[0]['you_profile'],
                'you' => $chat[0]['you'],
                'created' => $created_ago,
            );
        }

        $sql1 = "
            SELECT receiver_id as receiver
            FROM messages m
            WHERE m.room_id = {$param['id']} AND (m.sender_id = {$user['id']} OR m.receiver_id = {$user['id']}) GROUP BY m.receiver_id";
        $receiver_id = $this->Message->query($sql1);

        $response = [
            'status' => 200,
            'result' => $convo,
            'receiver_id' => $receiver_id[0]['m']['receiver'],
            'success' => true,
        ];

        $this->response->statusCode($response['status']);
        return $this->response->body(json_encode($response));
    }

    public function reply(){
        $param = $this->request->input('json_decode', true);

        if ($this->request->is('post')) {
            $this->Message->create();

            $user = $this->Session->read('Auth.User');
            $param['sender_id'] = $user['id'];

            if ($this->Message->save($param)) {

                $lastid = $this->Message->id;

                $conversation = $this->Conversation->findByRoomId($param['room_id']);
                if ($conversation) {
                    $conversation['Conversation']['latest_message_id'] = $lastid;
                    $this->Conversation->save($conversation);

                    $sql = "
                    SELECT m.room_id as room_id,
                        IF(m.sender_id = {$user['id']}, m.receiver_id, m.sender_id) AS user_id,
                        IF(m.sender_id = {$user['id']}, (SELECT profile as profile FROM users WHERE id = m.receiver_id), (SELECT profile as profile FROM users WHERE id = m.sender_id)) as profile,
                        IF(m.sender_id = {$user['id']}, (SELECT CONCAT(fname,' ',lname) as fullname FROM users WHERE id = m.receiver_id), (SELECT CONCAT(fname,' ',lname) as fullname FROM users WHERE id = m.sender_id)) as fullname,
                        (SELECT IF(sender_id = {$user['id']}, CONCAT('me:',' ',content), content) as content FROM messages WHERE id = max(c.latest_message_id)) as content,
                        (SELECT created FROM messages WHERE id = max(c.latest_message_id)) as created,
                        (SELECT count(status) FROM messages WHERE room_id = c.room_id and receiver_id = {$user['id']} and status = 0) as countunreadmessage
                    FROM messages m left join conversations c on m.id = c.latest_message_id 
                    WHERE (m.sender_id = {$user['id']} OR receiver_id = {$user['id']}) AND m.`room_id` = {$param['room_id']}";

                    $messages = $this->Message->query($sql);

                    $mes = array();

                    foreach($messages as $message){

                        $TimeHelper = new TimeHelper(new View());
                        $created_timestamp = strtotime($message[0]['created']);
                        $created_ago = $TimeHelper->timeAgo($created_timestamp);

                        $fullname = ucwords($message[0]['fullname']);

                        $mes = array(
                            'room_id' => $message['m']['room_id'],
                            'user_id' => $message[0]['user_id'],
                            'fullname' => $fullname,
                            'profile' => $message[0]['profile'],
                            'content' => $message[0]['content'],
                            'created' => $created_ago,
                            'count' => $message[0]['countunreadmessage'],
                        );

                    }

                    $response = [
                        'status' => 201,
                        'result' => $mes,
                        'success' => true,
                    ];
                } else {
                    $response = [
                        'status' => 400,
                        'message' => "Conversation not found",
                        'success' => false,
                    ];
                }
            }

            $this->response->statusCode($response['status']);
            return $this->response->body(json_encode($response));
        }
    }

    protected function latest_chat($id){

        $sql_room = "
        SELECT (SELECT room_id FROM messages WHERE created = MAX(m.created)) AS room_id
        FROM messages m
        WHERE (m.sender_id = {$id} OR receiver_id = {$id})";
        $messages = $this->Message->query($sql_room);

        $sql = "
        SELECT 
            IF(m.sender_id = {$id}, s.profile, NULL) AS me_profile, 
            IF(m.sender_id = {$id}, m.content, NULL) AS me, 
            IF(m.receiver_id = {$id}, s.profile, NULL) AS you_profile, 
            IF(m.receiver_id = {$id}, m.content, NULL) AS you, 
            m.created 
        FROM messages m 
            LEFT JOIN users s ON m.sender_id = s.id 
        WHERE m.room_id = {$messages[0][0]['room_id']}";
        $chatroom = $this->Message->query($sql);

        $convo = [];
        foreach($chatroom as $chat){

            $TimeHelper = new TimeHelper(new View());
            $created_timestamp = strtotime($chat['m']['created']);
            $created_ago = $TimeHelper->timeAgo($created_timestamp);

            $convo[] = array(
                'me_profile' => $chat[0]['me_profile'],
                'me' => $chat[0]['me'],
                'you_profile' => $chat[0]['you_profile'],
                'you' => $chat[0]['you'],
                'created' => $created_ago,
            );
        }

        $sql1 = "
            SELECT IF(m.receiver_id = {$id}, m.sender_id, m.receiver_id) as receiver
            FROM messages m
            WHERE m.room_id = {$messages[0][0]['room_id']} AND (m.sender_id = {$id} OR m.receiver_id = {$id}) GROUP BY m.receiver_id";
        $receiver_id = $this->Message->query($sql1);

        $response = [
            'result' => $convo,
            'receiver_id' => $receiver_id[0][0]['receiver'],
            'room_id' => $messages[0][0]['room_id'],
        ];
        
        return $response;
    }
}