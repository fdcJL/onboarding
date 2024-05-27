app.appControl('RegisterController', ['$scope','$http','$window','$location','AuthService', function($scope,$http,$window,$location, AuthService){

    $scope.signup = {};
    $scope.register = function(signup){
        var urlData = {
            'User': {
                fname: $scope.signup.fname || '',
                lname: $scope.signup.lname || '',
                email: $scope.signup.email || '',
                password: $scope.signup.password || '',
                confirm_password: $scope.signup.confirm_password || ''
            },
        }
        AuthService.register(urlData).then(function () {
            $scope.user = AuthService.getUser();
            $scope.isAuthenticated = AuthService.isAuthenticated();
        }, function(error){
            $scope.errorlist = error.validation;
        });
    }
}]);