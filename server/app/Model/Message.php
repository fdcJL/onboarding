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

    public $hasMany = [
        'LatestMessage' => [
            'className' => 'Conversation',
            'foreignKey' => 'sender_id'
        ],
    ];
}