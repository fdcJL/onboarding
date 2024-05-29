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

    $scope.send = {};
    $scope.submitMessage = function(send){
        var urlData = {
            'receiver_id' : send.recipient,
            'content' : send.message,
        }
        $http.post(apiUrl+'message/sendmessage', urlData).then(function (res) {
            var data = res.data;
            $scope.chatbox = true;
            $scope.sendmessage = false;
            $scope.chatroom = data.result.latest_chat.result.data;
            $scope.message = data.result.result;
            $scope.send.recipient = '';
            $scope.send.message = '';
            $scope.roomid = data.result.latest_chat.room_id;
            $scope.receiverid = data.result.latest_chat.receiver_id;
        }, function(error){

        });
    }

    $scope.messageConvo = function(){
        $http.get(apiUrl+'message').then(function (res) {
            var data = res.data
            $scope.message = data.result;
            $scope.chatbox = true;
            $scope.chatroom = data.latest_chat.result.data;
            $scope.roomid = data.latest_chat.room_id;
            $scope.receiverid = data.latest_chat.receiver_id;
        }, function(error){
            if(error.status === 403){
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
            var data = res.data
            $scope.chatroom = data.result.data;
        }, function(error){

        });
    }

    $scope.chat = {};
    $scope.replyMessage = function(reply){

        var urlData = {
            'room_id' : $scope.roomid,
            'receiver_id':$scope.receiverid,
            'content': reply,
        };
        $http.post(apiUrl+'message/reply', urlData).then(function (res) {
            var data = res.data;
            $scope.chatroom = data.result.latest_chat.result.data;
            $scope.message = data.result.result;
            $scope.roomid = data.result.latest_chat.room_id;
            $scope.receiverid = data.result.latest_chat.receiver_id;
            $scope.chat.reply = '';
        }, function(error){

        });
    }

    $scope.removeMessage = function(convo){
        var urlData = {
            data: {
                'id': convo.id,
                'room_id': convo.room_id
            },
        };
        $http.delete(apiUrl+'message/delete', urlData).then(function (res) {
            console.log(res.data);
        }, function(error){

        });
    }
}]);