app.controller('claimunitCtrl', function($scope, Data, toaster) {
//init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.jenis_kmp = [];
    $scope.bagian = '-';
    $scope.sisa = 0;

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
    $scope.kalkuasi = function() {
        $scope.form.total_biaya = (1 * $scope.form.biaya_spd) + (1 * $scope.form.biaya_tk) + (1 * $scope.form.biaya_mat);
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
    }
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

        Data.get('claimunit', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.create = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
        $scope.jenisKmp(form.stat, form.bag)
        $scope.form.kd_jns = form.kd_jns;
        $scope.kalkuasi();
        $scope.selected(form.no_wo);
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.jenisKmp(form.stat, form.bag)
        $scope.form.kd_jns = form.kd_jns;
        $scope.kalkuasi();
        $scope.selected(form.no_wo);
    };
    $scope.save = function(form) {
        var url = ($scope.is_create == true) ? 'claimunit/create' : 'claimunit/update/' + form.id;
        Data.post(url, form).then(function(result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
        });
    };
    $scope.cancel = function() {
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('claimunit/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function($query) {
        Data.get('wo/wospkselesai', {nama: $query}).then(function(data) {
            $scope.form.no_wo = data.data[0];
        });
    }
    $scope.sisagaransi = function(no_wo) {
        Data.post('claimunit/sisagaransi', {no_wo: no_wo}).then(function(data) {
            $scope.sisa = data.data;
        });
    }
    $scope.pilihWo = function($item) {
        $scope.form.nm_customer = $item.nm_customer;
        $scope.form.model = $item.model;
        $scope.form.jenis = $item.jenis;
        $scope.form.sales = $item.sales;
        $scope.form.wilayah = $item.wilayah;
        $scope.sisagaransi($item.no_wo);
    }
})
