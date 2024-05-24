app.appControl('RegisterController', ['$scope','$http','$window','$location','AuthService', function($scope,$http,$window,$location, AuthService){

    $scope.register = function(signup){
        var urlData = {
            'User': signup,
        }
        AuthService.register(urlData).then(function () {
            $scope.user = AuthService.getUser();
            $scope.isAuthenticated = AuthService.isAuthenticated();
        }, function(error){
            $scope.errorlist = error.validation;
        });
    }
}]);