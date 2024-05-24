<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (https://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (https://cakefoundation.org)
 * @link          https://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package		app.Controller
 * @link		https://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class ApiController extends Controller {

    public function display() {
        $this->response->body(json_encode('Hello Welcome to API'));
    }

    public $components = array(
        'Session',
        'Cookie',
    );

    public function beforeFilter() {
        parent::beforeFilter();
        $this->autoRender = false;
        $this->response->type('application/json');
        $this->Cookie->time = 7200;

        if ($this->request->params['auth']) {
            $this->authenticateUser();
        }

        $this->response->header('Access-Control-Allow-Origin', 'http://localhost/onboarding/client');
        $this->response->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $this->response->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
        $this->response->header('Access-Control-Allow-Credentials', 'true');
        $this->response->header('Content-Type: application/json');
    }

    protected function authenticateUser(){
        
        $authHeader = $this->request->header('Authorization');
        $auth_token = $this->Session->read('Auth.Token');

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            if ($this->isValidToken($token, $auth_token)) {
                return;
            }
        }
        // throw new UnauthorizedException('Unauthorized');

        $this->response->body(json_encode('Unauthorized'));
        $this->response->statusCode(401);
        $this->response->send();
        $this->_stop();
    }

    protected function isValidToken($token, $auth_token) {
        return ($token === $auth_token);
    }
}
