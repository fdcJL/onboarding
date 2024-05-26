<?php
App::uses('Model', 'Model');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');
class User extends Model {
    public $validate = array(
        'fname' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Firstname is Required!',
                'required' => true
            )
        ),
        'lname' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Lastname is Required!',
                'required' => true
            )
        ),
        'email' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Email is Required!',
                'required' => true,
            ),
            'unique' => array(
                'rule' => 'isUnique',
                'message' => 'The email address is already in use. Please use a different email address.',
            ),
            'email' => array(
                'rule' => 'email',
                'message' => 'Please enter a valid email address!',
            )
        ),
        'password' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Password is required',
                'required' => true
            ),
        ),
        'confirm_password' => array(
            'required' => array(
                'rule' => 'notBlank',
                'message' => 'Please confirm your password',
                'required' => true
            ),
            'matchPasswords' => array(
                'rule' => array('matchPasswords'),
                'message' => 'Passwords do not match'
            ),
        ),
        'bdate' => array(
            'valid' => array(
                'message' => 'Birdate is Required!',
                'allowEmpty' => true,
            )
        ),
        'gender' => array(
            'valid' => array(
                'message' => 'Gender is Required!',
                'allowEmpty' => true,
            )
        ),
        'position' => array(
            'valid' => array(
                'message' => 'Position is Required!',
                'allowEmpty' => true,
            )
        ),
        'hubby' => array(
            'valid' => array(
                'message' => 'Hubby is Required!',
                'allowEmpty' => true,
            )
        ),
        'profile' => array(
            'valid' => array(
                'rule' => 'notBlank',
                'message' => 'Profile Picture is Required!',
                'allowEmpty' => true,
            )
        ),
        'last_login' => array(
            'validDateTime' => array(
                'rule' => array('datetime'),
                'message' => 'Please enter a valid datetime.'
            ),
            'allowEmpty' => true
        ),
    );

    public function matchPasswords() {
        if ($this->data['User']['password'] === $this->data['User']['confirm_password']) {
            return true;
        }
        return false;
    }

    public $timestamps = true;

    public function beforeSave($options = array()) {
        if (isset($this->data[$this->alias]['password'])) {
            $passwordHasher = new BlowfishPasswordHasher();
            $this->data[$this->alias]['password'] = $passwordHasher->hash(
                $this->data[$this->alias]['password']
            );
        }

        $ipAddress = env('REMOTE_ADDR');

        if (empty($this->data[$this->alias]['id'])) {
            $this->data[$this->alias]['created_ip'] = $ipAddress;
        }
        $this->data[$this->alias]['modified_ip'] = $ipAddress;

        return true;
    }

    public $hasMany = [
        'SentMessages' => [
            'className' => 'Message',
            'foreignKey' => 'sender_id'
        ],
        'ReceivedMessages' => [
            'className' => 'Message',
            'foreignKey' => 'receiver_id'
        ],
    ];
}
