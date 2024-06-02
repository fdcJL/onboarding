app.appControl('MessageController',['$scope', '$rootScope', '$http', '$location', '$window', '$timeout', 'AuthService', 'WebSocketService', function($scope, $rootScope, $http, $location, $window, $timeout, AuthService, WebSocketService){
    $scope.templateUrl = 'views/layout/PagesLayout.html';

    WebSocketService.connect();

    $scope.messageComponents = function(template, data){
        if(template == 'chatbox'){
            $scope.loadMoreActive = false;
            $scope.pagination = {
                limit: 10
            };
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
            'pagination' : $scope.pagination,
        }
        $http.post(apiUrl+'message/sendmessage', urlData).then(function (res) {
            var data = res.data;
            $scope.chatbox = true;
            $scope.sendmessage = false;
            $scope.chatroom = data.result.latest_chat.result.data;
            $scope.message = data.result.result;
            $scope.roomid = data.result.latest_chat.room_id;
            $scope.receiverid = data.result.latest_chat.receiver_id;
            $scope.totalItems = data.result.pagination.total;

            WebSocketService.send(JSON.stringify({
                action: 'new_message',
                chatroom : data.result.latest_chat.websocket.chatbox,
                message : data.result.latest_chat.websocket.message,
                room_id : data.result.latest_chat.websocket.room_id,
                receiverid : $scope.send.recipient,
            }));

            $scope.send.recipient = '';
            $scope.send.message = '';
        }, function(error){

        });
    }

    $scope.pagination = {
        current: 1,
        limit: 5
    };
    $scope.messageConvo = function(){
        var urlData = {
            'pagination' : $scope.pagination,
            'search' : $scope.search_name,
        };

        $http.post(apiUrl+'message', urlData).then(function (res) {
            var data = res.data
            $scope.roomid = data.latest_chat.room_id;
            $scope.message = data.result;
            $scope.chatbox = true;
            $scope.chatroom = data.latest_chat.result.data;
            $scope.receiverid = data.latest_chat.receiver_id;
            $scope.totalItems = data.pagination.total;
            $scope.totalconvo = data.latest_chat.result.total;
            $scope.user = $rootScope.user;
        }, function(error){
            if(error.status === 403){
                $scope.chatbox = false;
                $scope.sendmessage = true;
            }
        });
    }
    $scope.loadMoreMessages = function() {
        $scope.pagination.limit += 5;
        $scope.messageConvo();
    };

    $scope.searchConvo = function(){
        var urlData = {
            'pagination' : $scope.pagination,
            'search' : $scope.search_name,
        };

        $http.post(apiUrl+'message', urlData).then(function (res) {
            var data = res.data
            $scope.roomid = data.latest_chat.room_id;
            $scope.message = data.result;
            $scope.chatroom = data.latest_chat.result.data;
            $scope.receiverid = data.latest_chat.receiver_id;
            $scope.totalItems = data.pagination.total;
            $scope.user = $rootScope.user;
        }, function(error){
        });
    }

    $scope.roomConvo = function(data){
        $scope.roomid = data['room_id'];
        $scope.receiverid = data['user_id'];
        var urlData = {
            'id': data['room_id'],
            'pagination' : $scope.pagination,
            
        };
        $http.post(apiUrl+'message/chatroom', urlData).then(function (res) {
            var data = res.data
            $scope.chatroom = data.convo.data;
            $scope.message = data.result.result;
            $scope.totalconvo = data.convo.total;

            console.log($scope.totalconvo);
        }, function(error){

        });
    }
    $scope.loadMoreActive = false;
    $scope.loadMoreConvos = function(){
        $scope.pagination.limit += 10;

        var data = {
            'room_id':$scope.roomid,
            'user_id':$scope.receiverid
        }
        $scope.roomConvo(data);
        $scope.loadMoreActive = true;
    }

    $scope.chat = {};
    $scope.replyMessage = function(reply){

        var urlData = {
            'room_id' : $scope.roomid,
            'receiver_id':$scope.receiverid,
            'content': reply,
            'pagination' : $scope.pagination,
        };
        $http.post(apiUrl+'message/reply', urlData).then(function (res) {
            var data = res.data;
            $scope.chatroom = data.result.latest_chat.result.data;
            $scope.message = data.result.result;
            $scope.roomid = data.result.latest_chat.room_id;
            $scope.receiverid = data.result.latest_chat.receiver_id;
            $scope.chat.reply = '';

            WebSocketService.send(JSON.stringify({
                action: 'reply_message',
                chatroom : data.result.latest_chat.websocket.chatbox,
                message : data.result.latest_chat.websocket.message,
                room_id : data.result.latest_chat.websocket.room_id,
                receiverid : data.result.latest_chat.receiver_id,
            }));
        }, function(error){

        });
    }

    $scope.removeMessage = function(convo){
        var urlData = {
            data: {
                'id': convo.id,
                'room_id': convo.room_id,
                'pagination' : $scope.pagination,
            },
        };
        $http.delete(apiUrl+'message/delete', urlData).then(function (res) {
            var data = res.data;
            $scope.chatroom = data.result.latest_chat.result.data;
            $scope.message = data.result.result;
            $scope.roomid = data.result.latest_chat.room_id;
            $scope.receiverid = data.result.latest_chat.receiver_id;

            WebSocketService.send(JSON.stringify({
                action: 'remove_message',
                chatroom : data.result.latest_chat.websocket.chatbox,
                message : data.result.latest_chat.websocket.message,
                room_id : data.result.latest_chat.websocket.room_id,
                receiverid : data.result.latest_chat.receiver_id,
            }));
        }, function(error){

        });
    }

    $scope.$on('socket:message', function(event, data) {
        var message = JSON.parse(data);
        $scope.user = $rootScope.user;

        if($scope.user.id === message.receiverid){
            $scope.message = message.message;
            if($scope.roomid == message.room_id){
                $scope.chatroom = message.chatroom;
            }
        }
    })
}]);