<?php
App::uses('Model', 'Model');

class Conversation extends Model {
    public $timestamps = true;

    public $hasMany = [
        'Message' => [
            'className' => 'Message',
            'foreignKey' => 'convo_id'
        ]
    ];
}