app.run(["$rootScope","$http","$location","$routeParams","$window","$timeout",'$filter',"AuthService", 
    function ($rootScope, $http, $location, $routeParams, $window, $timeout, $filter, AuthService) {
    // Update user and authentication status on login/logout
    $rootScope.$on("userUpdated", function (event, user) {
        $rootScope.user = user;
        if(user){
          var created = new Date(user.created);
          var last_login = new Date(user.last_login);
          var birthDate = new Date(user.bdate);

          user.created == null ? $rootScope.user.created = null : $rootScope.user.created = $filter('date')(created, 'MMM d, yyyy h:mm a');
          user.last_login == null ? $rootScope.user.last_login = null : $rootScope.user.last_login = $filter('date')(last_login, 'MMM d, yyyy h:mm a');

          function calculateAge(birthDate) {
              var today = new Date();
              var age = today.getFullYear() - birthDate.getFullYear();
              var monthDifference = today.getMonth() - birthDate.getMonth();
              var dayDifference = today.getDate() - birthDate.getDate();

              if (monthDifference < 0 || (monthDifference === 0 && dayDifference < 0)) {
                  age--;
              }
              return age;
          }

          $rootScope.user.age = calculateAge(birthDate);

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
