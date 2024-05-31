<?php
App::uses('ApiController', 'Controller');
App::uses('TimeHelper', 'Lib');
// use GuzzleHttp\Client;
// use Cake\Log\Log;

class MessageController extends ApiController {

    public $helpers = array('Time'); 
    public $uses = array('User', 'Room','Message', 'Conversation');
    
    public function index() {
        $param = $this->request->input('json_decode', true);
        $user = $this->Session->read('Auth.User');

        $limit = $param['pagination']['limit'];
        $search = "";

        if( !empty( $param['search'] ) ){ $search=" AND (u.fname LIKE '%". $param['search'] ."%' OR u.lname LIKE '%". $param['search'] ."%') "; }

        $sql = "
        SELECT u.id as user_id, m.room_id, u.profile, CONCAT(u.fname,' ',u.lname) as fullname, 
            IF(m.sender_id = {$user['id']}, CONCAT('me: ','', m.content), m.content) as content, 
            IF(m.receiver_id = {$user['id']}, count(status), 0) as countunread, m.created
        FROM messages m left join users u on IF(m.sender_id = {$user['id']}, m.receiver_id, m.sender_id) = u.id
        WHERE (sender_id = {$user['id']} OR receiver_id = {$user['id']}) {$search} GROUP BY m.room_id ORDER BY m.created DESC LIMIT {$limit}";

        $messages = $this->Message->query($sql);

        if($messages){

            $total = "select room_id from messages where sender_id = {$user['id']} OR receiver_id = {$user['id']} group by room_id";
            $totalItems = $this->Message->query($total);

            $mes = [];
            foreach($messages as $message){

                $TimeHelper = new TimeHelper(new View());
                $created_timestamp = strtotime($message['m']['created']);
                $created_ago = $TimeHelper->timeAgo($created_timestamp);
    
                $fullname = ucwords($message[0]['fullname']);
    
                $mes[] = array(
                    'room_id' => $message['m']['room_id'],
                    'user_id' => $message['u']['user_id'],
                    'fullname' => $fullname,
                    'profile' => $message['u']['profile'],
                    'content' => $message[0]['content'],
                    'created' => $created_ago,
                    'count' => $message[0]['countunread'],
                );
    
            }
    
            $response = [
                'status' => 200,
                'result' => $mes,
                'latest_chat' => $this->latest_chat($user['id']),
                'success' => true,
                'pagination' => array(
                    'limit' => (int)$limit,
                    'total' => count($totalItems),
                )
            ];

        }else{
            $response = [
                'status' => 403,
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

                        $modifiedUpdate = "UPDATE conversations SET modified = NOW() WHERE room_id = {$param['room_id']}";
                        $this->Message->query($modifiedUpdate);

                        $response = [
                            'status' => 201,
                            'result' => json_decode($this->index()),
                            'success' => true,
                        ];

                        $this->sendWebSocketMessage([
                            'action' => 'new_message',
                            'data' => $param
                        ]);
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
                            $response = [
                                'status' => 201,
                                'websocket' => $this->websocketdata($param['receiver_id']),
                                'result' => json_decode($this->index()),
                                'success' => true,
                            ];

                            $this->sendWebSocketMessage([
                                'action' => 'new_message',
                                'data' => $param
                            ]);
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

        $modifiedUpdate = "UPDATE conversations SET modified = NOW() WHERE room_id = {$param['id']}";
        $this->Message->query($modifiedUpdate);

        $response = [
            'status' => 200,
            'result' => json_decode($this->index()),
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

                    $statusUpdate = "UPDATE messages SET status = 1 WHERE room_id = {$param['room_id']} AND status = 0 AND sender_id = {$param['receiver_id']}";
                    $this->Message->query($statusUpdate);

                    $conversation['Conversation']['latest_message_id'] = $lastid;
                    $this->Conversation->save($conversation);

                    $response = [
                        'status' => 201,
                        'sender_id' => $param['sender_id'],
                        'result' => json_decode($this->index()),
                        'user_id' => $user['id'],
                        'success' => true,
                    ];

                    $this->sendWebSocketMessage([
                        'action' => 'reply_message',
                        'data' => $param
                    ]);

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

    public function delete(){
        if ($this->request->is('delete')) {
            $param = $this->request->input('json_decode', true);

            $id = $param['id'];

            $conversation = $this->Conversation->findByRoomId($param['room_id']);

            if ($conversation) {
                if ($this->Message->delete($id)) {

                    $lastid = $this->Message->find('first', [
                        'conditions' => ['Message.room_id' => $param['room_id']],
                        'order' => ['Message.created' => 'DESC'],
                        'fields' => ['Message.id'],
                        'recursive' => -1
                    ]);

                    $conversation['Conversation']['latest_message_id'] = $lastid['Message']['id'];
                    $this->Conversation->save($conversation);

                    $response = [
                        'status' => 200,
                        'result' => json_decode($this->index()),
                        'success' => true,
                    ];
                } else {
                    $response = [
                        'status' => 403,
                        'message' => "Message could not be deleted",
                        'success' => false,
                    ];
                }
            } else {
                $response = [
                    'status' => 400,
                    'message' => "Conversation not found",
                    'success' => false,
                ];
            }
    
            $this->response->statusCode($response['status']);
            return $this->response->body(json_encode($response));
        }   
    }

    protected function latest_chat($id){

        $modified = $this->Message->find('first', [
            'conditions' => array(
                'OR' => [
                    ['Message.sender_id' => $id],
                    ['Message.receiver_id' => $id]
                ]
            ),
            'joins' => [
                [
                    'table' => 'conversations',
                    'alias' => 'Conversation',
                    'type' => 'LEFT',
                    'conditions' => [
                        'Conversation.room_id = Message.room_id'
                    ]
                ]
            ],
            'fields' => [
                'Message.room_id',
            ],
            'order' => ['Conversation.modified' => 'DESC'],
            'group' => ['Message.room_id'],
            'recursive' => -1
        ]);

        $convo = $this->conversation($id, $modified['Message']['room_id']);

        $response = [
            'result' => $convo,
            'receiver_id' => $convo['receiver_id'],
            'room_id' => $modified['Message']['room_id'],
        ];
        
        return $response;
    }

    protected function conversation($id, $room_id){
        $sql = "
        SELECT 
            IF(m.sender_id = {$id}, s.id, r.id) AS user_id,
            IF(m.sender_id = {$id}, s.`profile`, NULL) AS me_profile,
            IF(m.sender_id = {$id}, m.content, NULL) AS me,
            IF(m.receiver_id = {$id}, r.`profile`, NULL) AS you_profile,
            IF(m.receiver_id = {$id}, m.content, NULL) AS you, m.created, m.id
        FROM messages m 
            LEFT JOIN users s ON m.`sender_id` = s.`id` 
            LEFT JOIN users r ON m.`sender_id` = r.`id`
        WHERE m.room_id = {$room_id}";

        $chatroom = $this->Message->query($sql);

        $convo = [];
        foreach($chatroom as $chat){

            $TimeHelper = new TimeHelper(new View());
            $created_timestamp = strtotime($chat['m']['created']);
            $created_ago = $TimeHelper->timeAgo($created_timestamp);

            $convo[] = array(
                'user_id' => $chat[0]['user_id'],
                'me_profile' => $chat[0]['me_profile'],
                'me' => $chat[0]['me'],
                'you_profile' => $chat[0]['you_profile'],
                'you' => $chat[0]['you'],
                'created' => $created_ago,
                'id' => $chat['m']['id'],
                'room_id' => $room_id
            );
        }

        $sql1 = "
            SELECT IF(m.receiver_id = {$id}, m.sender_id, m.receiver_id) as receiver
            FROM messages m
            WHERE m.room_id = {$room_id} AND (m.sender_id = {$id} OR m.receiver_id = {$id}) GROUP BY m.receiver_id";
        $receiver_id = $this->Message->query($sql1);

        $data = array(
            'data' => $convo,
            'receiver_id' => $receiver_id[0][0]['receiver'],
        );

        return $data;
    }

    protected function websocketdata($receiver){
        $sql = "
        SELECT u.id as user_id, 
            m.room_id, 
            u.profile, CONCAT(u.fname,' ',u.lname) as fullname, 
            IF(m.sender_id = {$receiver}, CONCAT('me: ','', m.content), m.content) as content, 
            IF(m.receiver_id = {$receiver}, count(status), 0) as countunread, 
            m.created
        FROM messages m left join users u on IF(m.sender_id = {$receiver}, m.receiver_id, m.sender_id) = u.id
        WHERE (sender_id = {$receiver} OR receiver_id = {$receiver}) GROUP BY m.room_id ORDER BY max(m.created) DESC";

        $messages = $this->Message->query($sql);

        if($messages){

            // $total = "select room_id from messages where sender_id = {$user['id']} OR receiver_id = {$user['id']} group by room_id";
            // $totalItems = $this->Message->query($total);

            $mes = [];
            foreach($messages as $message){

                $TimeHelper = new TimeHelper(new View());
                $created_timestamp = strtotime($message['m']['created']);
                $created_ago = $TimeHelper->timeAgo($created_timestamp);
    
                $fullname = ucwords($message[0]['fullname']);
    
                $mes[] = array(
                    'room_id' => $message['m']['room_id'],
                    'user_id' => $message['u']['user_id'],
                    'fullname' => $fullname,
                    'profile' => $message['u']['profile'],
                    'content' => $message[0]['content'],
                    'created' => $created_ago,
                    'count' => $message[0]['countunread'],
                );
    
            }
            return $mes;
        }
    }

    private function sendWebSocketMessage($msg) {
        $url = 'http://localhost:8080';
        $jsonData = json_encode($msg);
    
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData))
        );
    
        try {
            $response = curl_exec($curl);
            if ($response === false) {
                throw new Exception(curl_error($curl), curl_errno($curl));
            }
    
            // Optionally, you can handle the response if you need to.
    
        } catch (Exception $e) {
            echo 'Failed to send WebSocket message: ' . $e->getMessage();
        } finally {
            curl_close($curl);
        }
    }
}