app.controller('BelajarController', function ($scope, $http) {
    $http.get('api/web/belajar').success(function (data) {
        $scope.users = data;
    });
})
