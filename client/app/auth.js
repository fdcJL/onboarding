const notauthenticated = ['$q', '$location', function($q, $location) {
    var deferred = $q.defer();
    var token = sessionStorage.getItem('token');

    if (token) {
        deferred.reject();
        $location.path('/home');
    } else {
        deferred.resolve();
    }

    return deferred.promise;
}]

const authenticated = ['$q', '$location', function($q, $location) {
    var deferred = $q.defer();
    var token = sessionStorage.getItem('token');

    if (token) {
        deferred.resolve();
    } else {
        deferred.reject();
        $location.path('/login');
    }

    return deferred.promise;
}]

