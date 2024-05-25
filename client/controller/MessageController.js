app.appControl('MessageController',['$scope', '$rootScope', '$http', '$location', '$window', 'AuthService', function($scope, $rootScope, $http, $location, $window, AuthService){
    $scope.templateUrl = 'views/layout/PagesLayout.html';

    $scope.messageTemplate = 'views/pages/message/components/compose.html';

    $scope.chatbox = function(){
        $scope.messageTemplate = 'views/pages/message/components/chatbox.html';
    }
    
    $scope.composemessage = function(){
        $scope.messageTemplate = 'views/pages/message/components/compose.html';
    }
}]);