var app = angular.module('app', ['ngRoute', 'oc.lazyLoad'])

// Auth Store
app.service('AuthService', ['$http', '$q', '$rootScope', '$window', '$location', function ($http, $q, $rootScope, $window, $location) {
    const service = this;

    // Cache for user data
    var userCache = null;
    var userPromise = null;

    $rootScope.isLoggedin = $window.localStorage.getItem("token") || false;
    $rootScope.authUser = null;

    // Mutations
    service.setUser = function (user) {
        $rootScope.authUser = user;
        userCache = user;
        $rootScope.$broadcast('userUpdated', user);
    };
    service.setAuthenticate = function (auth) {
        $rootScope.isLoggedin = auth;
    };

    // Actions
    service.fetchUser = function () {
        if (userCache) {
            return $q.resolve(userCache); // Return cached user data
        } else if (userPromise) {
            return userPromise; // Return the ongoing promise
        } else if ($rootScope.isLoggedin) {
            userPromise = $http.get(apiUrl+'user').then(function (res) { // Fetch user data from server only if logged in
                service.setUser(res.data);
                userPromise = null; // Clear the promise after fetching
                return res.data;
            }).catch(function (error) {
                userPromise = null; // Clear the promise on error
                return $q.reject(error);
            });
            return userPromise;
        } else {
            return $q.reject('User not logged in'); // Return a rejected promise if not logged in
        }
    };
    service.login = function (signin) {
        return $http.post(apiUrl+ 'login', signin).then(function (res) {
            $window.localStorage.setItem('token', res.data.token);
            service.setAuthenticate(res.data.token);
            return service.fetchUser().then(function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: "success",
                    title: res.data.message
                });
                $location.path('/home');
            });
        });
    };
    service.register = function (signup) {
        return $http.post(apiUrl+ "register", signup).then(function (res) {
            $window.localStorage.setItem("token", res.data.token);
            service.setAuthenticate(res.data.token);
            return service.fetchUser().then(function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: "success",
                    title: res.data.message
                });
                $location.path('/thankyou');
            });
        });
    };
    service.logout = function () {
        return $http.get(apiUrl+"logout").then(function () {
            $window.localStorage.removeItem("token");
            service.setUser(null);
            $location.path('/login');
        });
    };
    service.update = function (info) {
        return $http.put(apiUrl+"edit", info).then(function (res) {
            $rootScope.$broadcast('userUpdated', res.data.user);
            return service.fetchUser().then(function () {
                const Toast = Swal.mixin({
                    toast: true,
                    position: "top-end",
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.onmouseenter = Swal.stopTimer;
                        toast.onmouseleave = Swal.resumeTimer;
                    }
                });
                Toast.fire({
                    icon: "success",
                    title: res.data.message
                });
            });
        });
    };

    // Getters
    service.getUser = function () {
        return $rootScope.authUser;
    };
    service.isAuthenticated = function () {
        return $rootScope.isLoggedin;
    };

}]);

app.factory('httpInterceptor', ['$q', '$location', '$window', function($q, $location, $window) {
    return {
        request: function(config) {
        
            config.headers.Authorization = 'Bearer ' + localStorage.getItem("token");

            return config;
        },
        responseError: function(rejection) {
            var error = {
                status: rejection.status,
                original: rejection,
                validation: {},
                message: rejection.data.message,
            };

            switch (rejection.status) {
                case 400:
                    if (rejection.data.errors) {
                        for (var field in rejection.data.errors) {
                            if (rejection.data.errors.hasOwnProperty(field)) {
                                error.validation[field] = rejection.data.errors[field][0];
                            }
                        }
                    }
                    break;
                case 401:
                    $window.localStorage.removeItem('token');
                    $location.path("/login");
                    break;
                default:
                    break;
            }
            return $q.reject(error);
        }
    };
}]);

app.directive("datepickers", function () {
    return {
        restrict: "A",
        require: "ngModel",
        link: function (scope, elem, attrs, ngModelCtrl) {
            var updateModel = function (dateText) {
                scope.$apply(function () {
                    ngModelCtrl.$setViewValue(dateText);
                });
            };
            var options = {
                dateFormat: "yy-mm-dd",
                changeMonth: true,
                changeYear: true,
                yearRange: "-100:+1",
                showButtonPanel: true,
                closeText: 'Clear',
                onSelect: function (dateText) {
                    updateModel(dateText);
                },
                onClose: function (dateText, inst) {
                    if ($(window.event.srcElement).hasClass('ui-datepicker-close')) {
                        dateText = "";
                        document.getElementById(this.id).value = dateText;
                        updateModel(" ");
                    }
                }
            };
            $(elem).datepicker(options);
        }
    }
});

app.directive('fileUpload', ['$parse', function($parse) {
    return {
        restrict: 'A',
        scope: {
            ngModel: '=',
        },
        link: function(scope, element, attrs) {
            element.bind('change', function() {
                var reader = new FileReader();
                reader.onload = function(e) {
                    scope.$apply(function() {
                        // Set the model value with the file data
                        scope.ngModel = e.target.result;
                    });
                };
                reader.readAsDataURL(element[0].files[0]);
            });
        }
    };
}]);

app.directive('perfectScrollbarTop', function($timeout) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            const ps = new PerfectScrollbar(element[0]);

            scope.$watch(
                function() {
                    return element[0].scrollHeight;
                },
                function() {
                    $timeout(function() {
                        element[0].scrollTop = 0;
                        ps.update();
                    }, 0);
                }
            );

            scope.$on('$destroy', function() {
                ps.destroy();
            });
        }
    };
});


app.directive('perfectScrollbarBottom', function($timeout) {
    return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            const ps = new PerfectScrollbar(element[0]);

            scope.$watch(
                function() {
                    return element[0].scrollHeight;
                },
                function() {
                    $timeout(function() {
                        element[0].scrollTop = element[0].scrollHeight;
                        ps.update();
                    }, 0);
                }
            );

            scope.$on('$destroy', function() {
                ps.destroy();
            });
        }
    };
});

app.directive("select2", function () {
    return {
        restrict: "A",
        require: "ngModel",
        link: function (scope, elem, attrs, ngModelCtrl) {
            function formatState(state) {
                if (!state.id) {
                    return state.text;
                }
                var imageUrl = $(state.element).data('image');
                if(imageUrl){
                    var $state = $(
                        '<span><img src="' + $(state.element).data('image') + '" class="img-flag" style="width: 30px;height: 30px;margin-right: 10px;" /> ' + state.text + '</span>'
                    );
                    return $state;
                }else{
                    return state.text;
                }
            }

            $(elem).select2({
                placeholder: attrs.placeholder || attrs.dataPlaceholder,
                allowClear: true,
                templateResult: formatState,
                // templateSelection: formatState
            });

            $(elem).on('select2:select', function (e) {
                scope.$apply(function () {
                    ngModelCtrl.$setViewValue(e.params.data.id);
                });
            });

            $(elem).on('select2:unselect', function () {
                scope.$apply(function () {
                    ngModelCtrl.$setViewValue(null);
                });
            });

            scope.$watch(attrs.ngModel, function (newVal) {
                if (newVal === undefined || newVal === null) {
                    $(elem).val(null).trigger('change');
                } else {
                    $(elem).val(newVal).trigger('change');
                }
            });
        }
    };
});

app.factory('WebSocketService', function($rootScope) {
    var ws;
    return {
        connect: function() {
            ws = new WebSocket('ws://localhost:8080'); // Update the URL if necessary
            ws.onmessage = function(event) {
                $rootScope.$apply(function() {
                    $rootScope.$broadcast('socket:message', event.data);
                });
            };
            ws.onopen = function() {
                console.log('WebSocket connection opened');
            };
            ws.onclose = function() {
                console.log('WebSocket connection closed');
            };
            ws.onerror = function(error) {
                console.log('WebSocket error: ' + error);
            };
        },
        send: function(message) {
            ws.send(message);
        }
    };
});
