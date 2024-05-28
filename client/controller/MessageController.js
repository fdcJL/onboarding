app.appControl('MessageController',['$scope', '$rootScope', '$http', '$location', '$window', '$timeout', 'AuthService', 'spinnerService', function($scope, $rootScope, $http, $location, $window, $timeout, AuthService, spinnerService){
    $scope.templateUrl = 'views/layout/PagesLayout.html';

    $scope.messageComponents = function(template, data){
        if(template == 'chatbox'){
            $scope.chatbox = true;
            $scope.sendmessage = false;
            $scope.roomConvo(data);
        }else{
            $scope.chatbox = false;
            $scope.sendmessage = true;
        }
    }

    $scope.settings = function(){
        $http.get(apiUrl+'settings').then(function (res) {
            var data = res.data;
            $scope.allusers = data.users;
        });
    }

    $scope.settings();

    $scope.submitMessage = function(send){
        var urlData = {
            'receiver_id' : send.recipient,
            'content' : send.message,
        }
        $http.post(apiUrl+'message/sendmessage', urlData).then(function (res) {
            $scope.chatbox = true;
            $scope.sendmessage = false;
            $scope.roomConvo(res.data.result);
            $scope.messageTemplate = 'views/pages/message/components/chatbox.html';
        }, function(error){

        });
    }

    $scope.messageConvo = function(){
        $http.get(apiUrl+'message').then(function (res) {
            var data = res.data
            $scope.message = data.result;
            $scope.chatbox = true;
            $scope.chatroom = data.latest_chat.result;
            $scope.receiverid = data.latest_chat.receiver_id;
            $scope.roomid = data.latest_chat.room_id;
        }, function(error){
            if(error.status === 400){
                $scope.chatbox = false;
                $scope.sendmessage = true;
            }
        });
    }

    $scope.roomConvo = function(data){
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