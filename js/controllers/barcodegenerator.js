app.controller('barcodeGeneratorCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.form = {};

    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.resultsbrg = data.data;
            });
        }
    };

    $scope.generate = function (form) {
        var data = {form: form};
        var url = 'barcode/generate';
        Data.postJson(url, data).then(function (result) {
            if(result.status == 1){
                window.open('api/web/barcode/view?print=true', "", "width=500");
            }else{
                toaster.pop('Error', "Error", "Terjadi Kesalahan");
            }
        });

    };

});
