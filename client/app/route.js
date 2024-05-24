app.config(['$controllerProvider','$interpolateProvider', '$httpProvider', '$routeProvider', '$locationProvider', 
    function ($controllerProvider, $interpolateProvider, $httpProvider, $routeProvider, $locationProvider) {
    
    $httpProvider.defaults.userXDomain = true;
    $httpProvider.defaults.withCredentials = true;
    delete $httpProvider.defaults.headers.common['X-Requeste-With'];
    $httpProvider.defaults.headers.common['Content-Type'] = 'application/json';
    $httpProvider.defaults.headers.common['Accept'] = 'application/json';
    $httpProvider.defaults.headers.common['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
    // if (!$httpProvider.defaults.headers.get) {$httpProvider.defaults.headers.get = {};}
    // $httpProvider.defaults.headers.get['If-Modified-Since'] = '0';
    // $httpProvider.defaults.headers.get['Cache-Control'] = 'no-cache';
    // $httpProvider.defaults.headers.get['Pragma'] = 'no-cache';
    $httpProvider.interceptors.push('httpInterceptor');
    $locationProvider.hashPrefix("");

    app.appControl = $controllerProvider.register;

    $routeProvider
        .when('/', {
            redirectTo: '/login'
        })
        .when('/login', {
            templateUrl: './views/login/login.html',
            resolve: {
                load: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load('controller/LoginController.js');
                }],
                app: notauthenticated
            }
        })
        .when('/register', {
            templateUrl: './views/login/register.html',
            resolve: {
                load: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load('controller/RegisterController.js');
                }],
                app: notauthenticated
            }
        })
        .when('/thankyou', {
            templateUrl: './views/pages/thankyou.html',
            resolve: {
                auth: authenticated
            }
        })
        .when('/home', {
            templateUrl: './views/pages/dashboard.html',
            resolve: {
                load: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load('controller/DashboardController.js');
                }],
                auth: authenticated
            }
        })
        .when('/message', {
            templateUrl: './views/pages/message.html',
            resolve: {
                load: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load('controller/MessageController.js');
                }],
                auth: authenticated
            }
        })
        .when('/profile', {
            templateUrl: './views/pages/profile.html',
            resolve: {
                load: ['$ocLazyLoad', function($ocLazyLoad) {
                    return $ocLazyLoad.load('controller/ProfileController.js');
                }],
                auth: authenticated
            }
        })
        .otherwise({
            templateUrl: './views/error/404.html',
        });
}]);