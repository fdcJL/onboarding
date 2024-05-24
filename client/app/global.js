app.run(["$rootScope","$http","$location","$routeParams","$window","$timeout","AuthService", 
    function ($rootScope, $http, $location, $routeParams, $window, $timeout, AuthService) {
    // Update user and authentication status on login/logout
    $rootScope.$on("userUpdated", function (event, user) {
        $rootScope.user = user;
        if(user){
          $rootScope.profile = "../../onboarding/server/app/webroot/img/profile/"+ user.profile;
        }
    });

    if (AuthService.isAuthenticated()) {
      AuthService.fetchUser().then(
        function (res) {
          $rootScope.user = res;
        }, function (error) {
          console.log(error);
        }
      );
    }

    $rootScope.logout = function () {
      AuthService.logout().then(function () {
        $rootScope.user = AuthService.getUser();
        $rootScope.isAuthenticated = AuthService.isAuthenticated();
      });
    };
  },
]);

const apiUrl = "http://localhost/onboarding/server/api/";
