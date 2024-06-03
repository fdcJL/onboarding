<?php
App::uses('Model', 'Model');

class Message extends Model {
    public $validate = array(
        'content' => array(
            'rule' => 'notBlank'
        ),
    );
    public $timestamps = true;

    public $belongsTo = array(
        'Room' => array(
            'className' => 'Room',
            'foreignKey' => 'room_id'
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'sender_id'
        ),
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'receiver_id'
        )
    );

    public function beforeSave($options = array()) {
        if (!$this->id && isset($_SERVER['SERVER_NAME'])) {
            $this->data[$this->alias]['created_ip'] = $this->getIPv4Address($_SERVER['SERVER_NAME']);
        }
    
        if (isset($_SERVER['SERVER_NAME'])) {
            $this->data[$this->alias]['modified_ip'] = $this->getIPv4Address($_SERVER['SERVER_NAME']);
        }
    
        return true;
    }
    
    private function getIPv4Address($host) {
        $ip = gethostbyname($host);
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            return $ip;
        } else {
            return '';
        }
    }
}