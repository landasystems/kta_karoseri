app.controller('returBbmCtrl', function ($scope, Data, toaster) {
//init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.jenis_kmp = [];
    $scope.bagian = '-';

    $scope.kalkulasi = function (jml_bbm, jml_retur) {
        if (jml_retur.length >= 1) {
            var selisih = jml_bbm - jml_retur;
            if (selisih >= 0) {
                $scope.form.jumlah = selisih;
            } else {
                $scope.form.jml = 0;
                toaster.pop('error', "Jumlah retur tidak boleh melebihi jumlah BBM");
            }
        }
    }

    $scope.cariBbm = function ($query) {
        if ($query.length >= 3) {
            Data.get('bbm/listbbm', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.cariBarang = function ($query, no_bbm) {
        if ($query.length >= 1) {
            Data.post('returbbm/barangmasuk', {barang: $query, no_bbm: no_bbm}).then(function (data) {
                $scope.resultsbarang = data.data;
            });
        }
    }

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

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

        Data.get('returbbm', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tgl = new Date();
        $scope.detailBbm = [{
                kd_barang: '',
                jml: '',
                ket: '',
            }];
        Data.get('returbbm/kode').then(function (data) {
            $scope.form.no_retur_bbm = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_bbm;
        $scope.form = form;
        $scope.form.tgl = new Date(form.tgl);
        $scope.selected(form.no_retur_bbm);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form.no_retur_bbm);
    };
    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'returbbm/create' : 'returbbm/update/' + form.no_retur_bbm;
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
        });
    };
    $scope.cancel = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('returbbm/delete/' + row.no_retur_bbm).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function (id) {
        Data.get('returbbm/view/' + id).then(function (data) {
            $scope.form = data.data;
            if (jQuery.isEmptyObject(data.detail)) {
                $scope.detailBbm = [{
                        kd_barang: '',
                        jml: '',
                        ket: '',
                    }];
            } else {
                $scope.detailBbm = data.detail;
            }
        });
    }
})
