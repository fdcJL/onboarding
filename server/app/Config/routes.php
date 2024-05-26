<?php
/**
 * Routes configuration
 *
 * In this file, you set up routes to your controllers and their actions.
 * Routes are very important mechanism that allows you to freely connect
 * different URLs to chosen controllers and their actions (functions).
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
 * @package       app.Config
 * @since         CakePHP(tm) v 0.2.9
 * @license       https://opensource.org/licenses/mit-license.php MIT License
 */
 
/**
 * Here, we are connecting '/' (base path) to controller called 'Pages',
 * its action called 'display', and we pass a param to select the view file
 * to use (in this case, /app/View/Pages/home.ctp)...
 */
	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'home'));
/**
 * ...and connect the rest of 'Pages' controller's URLs.
 */
	Router::connect('/pages/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * for API URLs.
 */
	Router::connect('/api', array('controller' => 'api', 'action' => 'display', 'auth' => false));
	Router::connect('/api/login', array('controller' => 'users', 'action' => 'login', 'auth' => false));
	Router::connect('/api/register', array('controller' => 'users', 'action' => 'register', 'auth' => false));
	Router::connect('/api/logout', array('controller' => 'users', 'action' => 'logout', 'auth' => true));
	Router::connect('/api/user', array('controller' => 'users', 'action' => 'user', 'auth' => true));
	Router::connect('/api/edit', array('controller' => 'users', 'action' => 'edit', 'auth' => true));

	Router::connect('/api/:controller', array('action' => 'index', 'auth' => true));
	Router::connect('/api/:controller/:action/*', array('auth' => true));

/**
 * Load all plugin routes. See the CakePlugin documentation on
 * how to customize the loading of plugin routes.
 */
	CakePlugin::routes();

/**
 * Load the CakePHP default routes. Only remove this if you do not want to use
 * the built-in default routes.
 */
	require CAKE . 'Config' . DS . 'routes.php';
