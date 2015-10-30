app.controller('returbarangmasukCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;

    $scope.displayed = [];
    $scope.paginations = 0;
    $scope.bayar = '';
    $scope.tanggal = '';

    $scope.callServer = function callServer(tableState) {
        tableStateRef = tableState;
        $scope.isLoading = true;
        var offset = tableState.pagination.start || 0;
        var limit = tableState.pagination.number || 10;
        var param = {offset: offset, limit: limit};

        if (tableState.sort.predicate) {
            param['sort'] = tableState.sort.predicate;
            param['order'] = tableState.sort.reverse;
        }
        if (tableState.search.predicateObject) {
            param['filter'] = tableState.search.predicateObject;
        }
        paramRef = param;
        Data.get('bbm/rekap', param).then(function (data) {
            $scope.displayed = data.data;
            $scope.displayedPrint = data.dataPrint;
            $scope.paginations = data.totalItems;
            if (data.totalItems != 0) {
                tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
            }
        });

        $scope.isLoading = false;
    };

    $scope.excel = function () {
        if (typeof $scope.tanggal.startDate && $scope.tanggal.startDate == null) {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih periode terlebih dahulu");
        } else {
            Data.get('bbm/rekap', paramRef).then(function (data) {
                window.location = 'api/web/bbm/excel';
            });
        }
    }
    $scope.print = function () {
        if (typeof $scope.tanggal.startDate && $scope.tanggal.startDate == null) {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih periode terlebih dahulu");
        } else {
            Data.get('bbm/rekap', paramRef).then(function (data) {
                window.open('api/web/bbm/excel?print=true', "", "width=500");
            });
        }
    }

    $scope.excel2 = function () {
        if (typeof $scope.tanggal.startDate && $scope.tanggal.startDate == null) {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih periode terlebih dahulu");
        } else {
            if ($scope.bayar == '') {
                toaster.pop('error', "Terjadi Kesalahan", "Pilih jenis pembayaran terlebih dahulu");
            } else {
                Data.get('bbm/rekap', paramRef).then(function (data) {
                    window.location = 'api/web/bbm/excel2';
                });
            }
        }
    }
    $scope.print2 = function () {
        if (typeof $scope.tanggal.startDate && $scope.tanggal.startDate == null) {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih periode terlebih dahulu");
        } else {
            if ($scope.bayar == '') {
                toaster.pop('error', "Terjadi Kesalahan", "Pilih jenis pembayaran terlebih dahulu");
            } else {
                Data.get('bbm/rekap', paramRef).then(function (data) {
                    window.open('api/web/bbm/excel2?print=true', "", "width=500");
                });
            }
        }
    }

    $scope.excelRekap = function () {
        if (typeof $scope.tanggal.startDate && $scope.tanggal.startDate == null) {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih periode terlebih dahulu");
        } else {
            Data.get('bbm/rekap', paramRef).then(function (data) {
                window.location = 'api/web/bbm/excelrekap';
            });
        }
    }
    $scope.printRekap = function () {
        if (typeof $scope.tanggal.startDate && $scope.tanggal.startDate == null) {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih periode terlebih dahulu");
        } else {
            Data.get('bbm/rekap', paramRef).then(function (data) {
                window.open('api/web/bbm/excelrekap?print=true', "", "width=500");
            });
        }
    }

    $scope.excelSerahTerima = function () {
        if (typeof $scope.tanggal.startDate && $scope.tanggal.startDate == null) {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih periode terlebih dahulu");
        } else {
            Data.get('bbm/rekap', paramRef).then(function (data) {
                window.location = 'api/web/bbm/excelserahterima';
            });
        }
    }

    $scope.printSerahTerima = function () {
        if (typeof $scope.tanggal.startDate && $scope.tanggal.startDate == null) {
            toaster.pop('error', "Terjadi Kesalahan", "Pilih periode terlebih dahulu");
        } else {
            Data.get('bbm/rekap', paramRef).then(function (data) {
                window.open('api/web/bbm/excelserahterima?print=true', "", "width=500");
            });
        }
    }
})
