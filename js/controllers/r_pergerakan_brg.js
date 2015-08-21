app.controller('pergerakanBrgCtrl', function($scope, Data) {

    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;
    $scope.displayed = {};

    $scope.excel = function(form) {
        Data.post('barang/rekappergerakan', form).then(function(data) {
            window.location = 'api/web/barang/excelpergerakan';
        });
    }

    $scope.view = function(form) {
        $scope.show_detail = true;
        Data.post('barang/rekappergerakan', form).then(function(data) {
            $scope.displayed = data.data;
        });
    }

})