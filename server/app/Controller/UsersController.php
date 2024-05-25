<?php
App::uses('ApiController', 'Controller');
App::uses('BlowfishPasswordHasher', 'Controller/Component/Auth');

class UsersController extends ApiController {

    public function register(){
        if ($this->request->is('post')) {
            $param = $this->request->input('json_decode', true);

            $this->User->create();

            if ($this->User->save($param)) {
                $lastid = $this->User->id;

                $user = $this->User->findById($lastid);
                
                $bytes = random_bytes(32);
                $token = bin2hex($bytes);

                unset($user['User']['password']);
                $this->Session->write('Auth.User', $user['User']);
                $this->Session->write('Auth.Token', $token);

                $response = [
                    'status'=> 201,
                    'message' => "Registered Successfully!",
                    'success' => true,
                    'token'=> $token
                ];
            }else {
                $errors = $this->User->validationErrors;
                $response = [
                    'status'=> 400,
                    'message' => "The user could not be saved. Please, try again.",
                    'success' => false,
                    'errors' => $errors,
                ];
            }
        }else{
            $response = [
                'status' => 405,
                'message' => "Method Not Allowed",
                'success' => false
            ];
        }
        $this->response->statusCode($response['status']);
        return $this->response->body(json_encode($response));
    }

	public function login() {
        $param = $this->request->input('json_decode', true);

        if ($this->request->is('post')) {
            if (empty($param['email']) && empty($param['password'])) {
                $response = [
                    'status' => 400,
                    'success' => false,
                    'message' => 'email and password cannot be empty',
                ];
            }else{
                $user = $this->User->findByEmail($param['email']);

                $passwordHasher = new BlowfishPasswordHasher();

                if ($user && $passwordHasher->check($param['password'], $user['User']['password'])) {
                    $bytes = random_bytes(32);
                    $token = bin2hex($bytes);

                    unset($user['User']['password']);
                    $this->Session->write('Auth.User', $user['User']);
                    $this->Session->write('Auth.Token', $token);
                    // $this->Cookie->write('csrf', $token);
                    $response = [
                        'status'=> 201,
                        'success' => true, 
                        'message' => 'Login successfully!',
                        'token'=> $token
                    ];
                } else {
                    $response = [
                        'status'=> 400,
                        'success' => false, 
                        'message' => 'Invalid username or password',
                    ];
                }   
            }
        } else {
            $response = [
                'status'=> 405,
                'message' => "Method Not Allowed",
                'success' => false,
            ];
        }
        $this->response->statusCode($response['status']);
        return $this->response->body(json_encode($response));
    }

    public function user() {
        $user = $this->Session->read('Auth.User');
        $user_details = $this->User->findById($user['id']);
        unset($user_details['User']['password']);
        $this->response->statusCode(200);
        return $this->response->body(json_encode($user));
    }

    public function edit(){
        $param = $this->request->input('json_decode', true);
        $user = $this->Session->read('Auth.User');

        $this->User->id = $user['id'];
        unset($param['User']['password']);


        if ($this->request->is('put')) {
            
            if (!empty($param['User']['profile'])) {
                $base64data = $param['User']['profile'];
                $encodedImageData = substr($base64data, strpos($base64data, ',') + 1);
                $decodedImageData = base64_decode($encodedImageData);
            
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mimeType = $finfo->buffer($decodedImageData);
            
                $allowedExtensions = [
                    'image/jpeg' => 'jpg',
                    'image/gif' => 'gif',
                    'image/png' => 'png',
                ];
            
                if (isset($allowedExtensions[$mimeType])) {
                    $fileExtension = $allowedExtensions[$mimeType];

                    $fileExist = $user['profile'];
                    $deleteFile = WWW_ROOT . 'img/profile/' . $fileExist;

                    if (file_exists($deleteFile) && $user['profile'] !== 'default.webp') {
                        unlink($deleteFile);
                    }

                    $filename = 'profile_' . uniqid() . '.' . $fileExtension;
                    $uploadPath = WWW_ROOT . 'img/profile/' . $filename;
                    
                    if (file_put_contents($uploadPath, $decodedImageData)) {
                        $param['User']['profile'] = $filename;
                    } else {
                        $response = [
                            'status' => 400,
                            'message' => "Failed to upload profile picture",
                            'success' => false,
                        ];
                        $this->response->statusCode($response['status']);
                        $this->response->body(json_encode($response));
                        return $this->response;
                    }
                } else {
                    $response = [
                        'status' => 400,
                        'message' => "Invalid file format. Only .jpg, .gif, and .png files are allowed.",
                        'success' => false,
                    ];
                    $this->response->statusCode($response['status']);
                    $this->response->body(json_encode($response));
                    return $this->response;
                }
            }
            

            if($this->User->save($param, array('validate' => false))){
                $this->Session->write('Auth.User', array_merge($user, $param['User']));
                $response = [
                    'status'=> 201,
                    'message' => "Profile Details Updated Successfully",
                    'success' => true,
                    'user' => array_merge($user, $param['User'])
                ];
            }else {
                $response = [
                    'status' => 400,
                    'message' => "Failed to update profile details",
                    'success' => false,
                ];
            }
        } else {
            $param = $this->User->findById($user['id']);
            unset($param['User']['password']);
        }

        $this->response->statusCode($response['status']);
        return $this->response->body(json_encode($response));
    }

    public function logout(){
        $user = $this->Session->read('Auth.User');

        if ($user) {
            $this->User->id = $user['id'];
            $this->User->saveField('last_login', date('Y-m-d H:i:s'));
            $this->Session->destroy();

            $response = [
                'status' => 200,
                'success' => true,
                'message' => 'Logged out successfully'
            ];
        }else {
            $response = [
                'status' => 500,
                'success' => false,
                'message' => 'User not found in session'
            ];
        }

        $this->response->statusCode($response['status']);
        return $this->response->body(json_encode($response));
    }
}