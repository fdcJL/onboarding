app.appControl('MessageController',['$scope', '$rootScope', '$http', '$location', '$window', 'AuthService', function($scope, $rootScope, $http, $location, $window, AuthService){
    $scope.templateUrl = 'views/layout/PagesLayout.html';

    $scope.messageTemplate = 'views/pages/message/components/sendmessage.html';
    
    $scope.messageComponents = function(template, data){
        $scope.messageTemplate = 'views/pages/message/components/' + template + '.html';

        if(template == 'chatbox'){
            $scope.roomConvo(data);
        }
    }

    $scope.messageConvo = function(){
        $http.get(apiUrl+'message').then(function (res) {
            var data = res.data.result
            $scope.message = data;
        });
    }

    $scope.roomConvo = function(data){
        // console.log(data['room_id']);

        $scope.roomid = data['room_id'];
        $scope.receiverid = data['user_id'];

        var urlData = {
            'id': data['room_id'],
        };
        $http.post(apiUrl+'message/chatroom', urlData).then(function (res) {
            var data = res.data.result
            $scope.chatroom = data;
        }, function(error){

        });
    }

    $scope.replyMessage = function(reply){
        var urlData = {
            'room_id' : $scope.roomid,
            'receiver_id':$scope.receiverid,
            'content': reply,
        };
        $http.post(apiUrl+'message/reply', urlData).then(function (res) {
            var data = res.data.result
            $scope.roomConvo(data);
            $scope.messageConvo();
        }, function(error){

        });
    }
}]);