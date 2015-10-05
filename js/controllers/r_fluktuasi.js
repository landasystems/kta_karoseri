app.controller('fluktuasiCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;

    $scope.displayed = [];
    $scope.paginations = 0;
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;


//    $scope.callServer = function callServer(tableState) {
//        tableStateRef = tableState;
//        $scope.isLoading = true;
//        var offset = tableState.pagination.start || 0;
//        var limit = tableState.pagination.number || 10;
//        var param = {offset: offset, limit: limit};
//
//        if (tableState.sort.predicate) {
//            param['sort'] = tableState.sort.predicate;
//            param['order'] = tableState.sort.reverse;
//        }
//        if (tableState.search.predicateObject) {
//            param['filter'] = tableState.search.predicateObject;
//        }
//        paramRef = param;
//        Data.get('po/fluktuasi', param).then(function (data) {
//            $scope.displayed = data.data;
//            $scope.displayedPrint = data.dataPrint;
//            $scope.paginations = data.totalItems;
//            if (data.totalItems != 0) {
//                tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
//            }
//        });
//
//        $scope.isLoading = false;
//    };
    $scope.r_fluktuasiSrc = [];
    $scope.r_fluktuasi = [];
    $scope.tmpFluktuasiHrg = function (form) {
        var data = form;
        Data.post('po/fluktuasi', data).then(function (data) {
            angular.forEach(data.data, function ($value, $key) {
                $scope.r_fluktuasiSrc.push($value);

            });
        });
        console.log($scope.r_fluktuasi);
    }

    var myDate = new Date();
    var year = myDate.getFullYear();
    var month = ("0" + (myDate.getMonth() + 1)).slice(-2);
    var list = [];
    for (var i = year - 3; i < year + 3; i++) {
        list.push(i);
    }
    
    $scope.form = {};
    $scope.form.tahun = year;
    $scope.form.bulan = month;
//    console.log(myDate);

    $scope.listth = list;
    $scope.listbln = [
        {key: "01", value: "Januari"},
        {key: "02", value: "Februari"},
        {key: "03", value: "Maret"},
        {key: "04", value: "April"},
        {key: "05", value: "Mei"},
        {key: "06", value: "Juni"},
        {key: "07", value: "Juli"},
        {key: "08", value: "Agustus"},
        {key: "09", value: "September"},
        {key: "10", value: "Oktober"},
        {key: "11", value: "November"},
        {key: "12", value: "Desember"}
    ];

    $scope.excel = function () {
        var data = $scope.form;
        Data.post('po/fluktuasi', data).then(function (data) {
            window.location = 'api/web/po/excelfluktuasi';
        });
    };
    $scope.print = function () {
        var data = $scope.form;
        Data.post('po/fluktuasi', data).then(function (data) {
            window.open('api/web/po/excelfluktuasi?print=true', "", "width=500");
        });
    };



})
