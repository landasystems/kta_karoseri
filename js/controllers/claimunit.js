app.controller('claimunitCtrl', function($scope, Data, toaster) {
//init data
    var tableStateRef;
    $scope.formtitle = "Claim Unit";
    $scope.displayed = [];
    $scope.is_create = true;
    $scope.jenis_kmp = [];
    $scope.bagian = '-';
    $scope.sisa = 0;
    $scope.list = [];

    $scope.kalkuasi = function() {
        $scope.form.total_biaya = (($scope.form.biaya_spd) ? 1 * $scope.form.biaya_spd : 0) + (($scope.form.biaya_tk) ? 1 * $scope.form.biaya_tk : 0) + (($scope.form.biaya_mat) ? 1 * $scope.form.biaya_mat : 0);
    }
    $scope.open1 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.open2 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };
    $scope.kalkulasi = function() {
        $scope.form.total_biaya = (($scope.form.biaya_spd) ? 1 * $scope.form.biaya_spd : 0) + (($scope.form.biaya_tk) ? 1 * $scope.form.biaya_tk : 0) + (($scope.form.biaya_mat) ? 1 * $scope.form.biaya_mat : 0);
    }
    $scope.jenisKmp = function(status, bagian) {
        Data.get('claimunit/jeniskomplain?status=' + status + '&bagian=' + bagian).then(function(data) {
            $scope.jenis_kmp = data.data;
        });
    }
    $scope.cariWo = function($query) {
        if ($query.length >= 3) {
            Data.get('wo/wospkselesai', {nama: $query}).then(function(data) {
                $scope.results = data.data;
            });
        }
    };
    $scope.view = function(form) {
        $scope.is_create = false;
        $scope.form = form;
        $scope.form.tgl_pelaksanaan = new Date(form.tgl_pelaksanaan);
        $scope.jenisKmp(form.stat, form.bag)
        $scope.form.kd_jns = form.kd_jns;
        $scope.kalkuasi();
    };
    $scope.tambah = function(form) {
        $scope.is_create = true;
        $scope.form.stat = '';
        $scope.form.bag = '';
        $scope.form.kd_jns = '';
        $scope.form.problem = '';
        $scope.form.solusi = '';
        $scope.form.tgl_pelaksanaan = new Date();
        $scope.form.pelaksana = '';
        $scope.form.biaya_mat = '0';
        $scope.form.biaya_tk = '0';
        $scope.form.biaya_spd = '0';
        $scope.form.total_biaya = '0';
    };
    $scope.save = function(form) {
        var url = ($scope.is_create == true) ? 'claimunit/create' : 'claimunit/update/' + form.id;
        Data.post(url, form).then(function(result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.listWo(form.no_wo);
                $scope.is_create = false;
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
        });
    };

    $scope.delete = function(row) {
        var wo = row.no_wo;
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('claimunit/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
        $scope.listWo(wo);
    };
    $scope.selected = function($query) {
        Data.get('wo/wospkselesai', {nama: $query}).then(function(data) {
            $scope.form = data.data;
        });
    };
    $scope.sisagaransi = function(no_wo) {
        Data.post('claimunit/sisagaransi', {no_wo: no_wo}).then(function(data) {
            $scope.sisa = data.data;
        });
    };
    $scope.listWo = function(nowo) {
        Data.get('claimunit/view', {no_wo: nowo}).then(function(data) {
            $scope.list = data.data;
        });
    }
    $scope.pilihWo = function($item) {
        $scope.form = $item;
        $scope.form.tgl_pelaksanaan = new Date();
        $scope.sisagaransi($item.no_wo);
        $scope.listWo($item.no_wo);
        $scope.is_create = true;
    }
})
