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
                'message' => 'This email is already in use!',
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
            'length' => array(
                'rule' => array('between', 5, 20),
                'message' => 'Password must be between 5 and 20 characters'
            )
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
            'length' => array(
                'rule' => array('between', 5, 20),
                'message' => 'Password must be between 5 and 20 characters'
            )
        ),
        'bdate' => array(
            'valid' => array(
                'message' => 'Birdate is Required!',
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
        return true;
    }
}
