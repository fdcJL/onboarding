app.appControl('MessageController',['$scope', '$rootScope', '$http', '$location', '$window', 'AuthService', function($scope, $rootScope, $http, $location, $window, AuthService){
    $scope.templateUrl = 'views/layout/PagesLayout.html';

    $scope.messageTemplate = 'views/pages/message/components/sendmessage.html';
    
    $scope.messageComponents = function(template){
        $scope.messageTemplate = 'views/pages/message/components/' + template + '.html';
    }

    $scope.submitMessage = function(send){
        $http.get(apiUrl+'message').then(function (res) {
            console.log(res);
        });
    }

    $scope.submitMessage();
}]);