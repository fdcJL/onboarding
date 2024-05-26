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
        'Conversation' => array(
            'className' => 'Conversation',
            'foreignKey' => 'convo_id'
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
}