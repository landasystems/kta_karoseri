phonecatServices.factory('Phone', ['$resource',
  function($resource){
    return $resource('phones/:phoneId.json', {}, {
      query: {method:'GET', params:{phoneId:'phones'}, isArray:true}
    });
  }]);
  
app.controller('BelajarController', function ($scope, $http) {
    $http.get('api/web/belajars').success(function (data) {
        $scope.users = data;
    });
})
app.controller('BelajarDetController', function ($scope, $http, $stateParams) {
    $scope.id = $stateParams.id;
    $scope.id2 = $stateParams.id2;

    $http.get('api/web/belajars').success(function (data) {
        $scope.users = data;
    });
})
