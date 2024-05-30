app.appControl('MessageController',['$scope', '$rootScope', '$http', '$location', '$window', '$timeout', 'AuthService', 'WebSocketService', function($scope, $rootScope, $http, $location, $window, $timeout, AuthService, WebSocketService){
    $scope.templateUrl = 'views/layout/PagesLayout.html';

    WebSocketService.connect();

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
            'pagination' : $scope.pagination,
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
            $scope.totalItems = data.result.pagination.total;

            WebSocketService.send(JSON.stringify({
                action: 'reply_message',
                chatroom: data.result.latest_chat.result.data,
                message: data.result.result,
                roomid : data.result.latest_chat.room_id,
                receiverid : data.result.latest_chat.receiver_id,
                totalItems : data.result.pagination.total,
            }));
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
            $scope.message = data.result;
            $scope.chatbox = true;
            $scope.chatroom = data.latest_chat.result.data;
            $scope.roomid = data.latest_chat.room_id;
            $scope.receiverid = data.latest_chat.receiver_id;
            $scope.totalItems = data.pagination.total;
        }, function(error){
            if(error.status === 403){
                $scope.chatbox = false;
                $scope.sendmessage = true;
            }
        });
    }
    $scope.loadMoreMessages = function() {
        $scope.pagination.limit += 1;
        $scope.messageConvo();
    };

    $scope.roomConvo = function(data){
        $scope.roomid = data['room_id'];
        $scope.receiverid = data['user_id'];
        var urlData = {
            'id': data['room_id'],
            'pagination' : $scope.pagination,
        };
        $http.post(apiUrl+'message/chatroom', urlData).then(function (res) {
            var data = res.data
            $scope.chatroom = data.result.latest_chat.result.data;
        }, function(error){

        });
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

            // Notify the WebSocket server
            WebSocketService.send(JSON.stringify({
                action: 'reply_message',
                sender_id: data.sender_id,
                chatroom: data.result.latest_chat.result.data,
                message: data.result.result,
                roomid : data.result.latest_chat.room_id,
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

            // Notify the WebSocket server
            WebSocketService.send(JSON.stringify({
                action: 'remove_message',
                data: convo.id
            }));
        }, function(error){

        });
    }

    $scope.$on('socket:message', function(event, data) {
        var messageData = JSON.parse(data);
        if (messageData.action === 'new_message' || messageData.action === 'reply_message') {
            // Filter out messages sent by the current user
            if (messageData.message.sender_id !== AuthService.getUser().id) {
                $scope.chatroom = messageData.chatroom;
                $scope.message = messageData.message;
                $scope.roomid = messageData.roomid;
                $scope.receiverid = messageData.receiverid;
                $scope.totalItems = messageData.totalItems;
    
                console.log($scope.chatroom);
            }
        } else if (messageData.action === 'remove_message') {
            // Handle message removal in chatroom
        }
    })
}]);