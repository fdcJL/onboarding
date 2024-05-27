app.appControl('MessageController',['$scope', '$rootScope', '$http', '$location', '$window', 'AuthService', function($scope, $rootScope, $http, $location, $window, AuthService){
    $scope.templateUrl = 'views/layout/PagesLayout.html';

    $scope.messageTemplate = 'views/pages/message/components/sendmessage.html';
    
    $scope.messageComponents = function(template, room){
        $scope.messageTemplate = 'views/pages/message/components/' + template + '.html';

        if(template == 'chatbox'){
            $scope.roomConvo(room);
        }
    }

    $scope.messageConvo = function(send){
        $http.get(apiUrl+'message').then(function (res) {
            var data = res.data.result
            $scope.message = data;
        });
    }

    $scope.roomConvo = function(room){
        console.log(room);   
    }

    $scope.messageConvo();
}]);