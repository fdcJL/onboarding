app.appControl('ProfileController',['$scope', '$rootScope', '$http', '$location', '$window', 'AuthService', function($scope, $rootScope, $http, $location, $window, AuthService){
    $scope.templateUrl = 'views/layout/PagesLayout.html';
    $scope.info = {};

    $scope.update = function (info) {
        $scope.user = $rootScope.user;
        $scope.path = '../../onboarding/server/app/webroot/img/profile/'+ $scope.user.profile;
        if($scope.profile !== $scope.path){
            $scope.info.profile = $scope.profile;
        }

        if (info) {
            var urlData = {
                User: info,
            };

            console.log(urlData);

            AuthService.update(urlData).then(function () {
                $location.path('/profile');
                $scope.user = $rootScope.user;
            }, function (error) {
                $scope.errorlist = error.validation;
            });
        }
    };
}]);