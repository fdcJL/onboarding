app.appControl('LoginController', ['$scope', '$http', '$location', '$window', 'AuthService', function($scope, $http, $location, $window, AuthService){
    $scope.signin = {};

    $scope.login = function(signin){
        const validationErrors = validateSignin(signin);
        if (validationErrors) {
            $scope.signinErrors = validationErrors;
            return;
        }

        AuthService.login(signin).then(function () {
            $scope.user = AuthService.getUser();
            $scope.isAuthenticated = AuthService.isAuthenticated();
        }, function(error){
            $scope.signinErrors = '';
            Swal.fire({
                icon: "error",
                title: "Oops...",
                text: error.message,
            });
        });
    }

    // Validation functions
    function validateSignin(signin) {
        let errors = {};
        if (!signin.email) errors.email = 'Email is required';
        if (!signin.password) errors.password = 'Password is required';
        return Object.keys(errors).length ? errors : null;
    }
}]);