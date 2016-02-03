app.controller('pergerakanBrgCtrl', function ($scope, Data, toaster) {

    var tableStateRef;
    var paramRef;
    $scope.form = {};
    $scope.show_detail = false;

    $scope.print = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('barang/rekappergerakan', form).then(function (data) {
                window.open('api/web/barang/excelpergerakan?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    }

    $scope.excel = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('barang/rekappergerakan', form).then(function (data) {
                window.location = 'api/web/barang/excelpergerakan';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    }

    $scope.printminggu = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('barang/rekappergerakan', form).then(function (data) {
                window.open('api/web/barang/excelpergerakan2?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    }

    $scope.excelminggu = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('barang/rekappergerakan', form).then(function (data) {
                window.location = 'api/web/barang/excelpergerakan2';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    }

    $scope.excelBbmBbk = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('barang/rekapbbmbbk', form).then(function (data) {
                window.location = 'api/web/barang/excelbbmbbk';
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    }

    $scope.printBbmBbk = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            Data.post('barang/rekapbbmbbk', form).then(function (data) {
                window.open('api/web/barang/excelbbmbbk?print=true', "", "width=500");
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    }

    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.resultsbarang = data.data;
            });
        }
    }

    $scope.listSrc = [];
    $scope.list = [];
    $scope.view = function (form) {
        if ('tanggal' in form && form.tanggal.startDate != null) {
            $scope.show_detail = true;
            Data.post('barang/rekappergerakan', form).then(function (data) {
                $scope.listSrc = [];
                angular.forEach(data.data, function ($value, $key) {
                    $scope.listSrc.push($value);
                });
            });
        } else {
            toaster.pop('error', "Terjadi Kesalahan", "Masukkan periode terlebih dahulu");
        }
    }

})
