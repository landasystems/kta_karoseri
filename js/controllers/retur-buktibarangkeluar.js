app.controller('returbbkCtrl', function($scope, Data, toaster) {
//init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.jenis_kmp = [];
    $scope.bagian = '-';

    $scope.kalkulasi = function(jml_bbk, jml_keluar) {
        var selisih = jml_bbk - jml_keluar;
        if (selisih > 0) {
            $scope.form.jml = selisih;
        } else {
            $scope.form.jml = 0;
            toaster.pop('error', "Jumlah retur tidak boleh melebihi jumlah BBK");
        }
    }

    $scope.cariBbk = function($query) {
        if ($query.length >= 3) {
            Data.get('bbk/listbbk', {nama: $query}).then(function(data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.cariBarang = function($query, no_bbk) {
        if ($query.length >= 1) {
            Data.post('returbbk/barangkeluar', {barang: $query, no_bbk: no_bbk}).then(function(data) {
                $scope.resultsbarang = data.data;
            });
        }
    }

    $scope.open1 = function($event) {
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

        Data.get('returbbk', param).then(function(data) {
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
        $scope.form.tgl = new Date();
        $scope.detailBbk = [{
                kd_barang: '',
                jml: '',
                ket: '',
            }];
        Data.get('returbbk/kode').then(function(data) {
            $scope.form.no_retur_bbk = data.kode;
        });
    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_bbk;
        $scope.form = form;
        $scope.selected(form.no_retur_bbk);
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form.no_retur_bbk);
    };
    $scope.save = function(form) {
        var url = ($scope.is_create == true) ? 'returbbk/create' : 'returbbk/update/' + form.no_retur_bbk;
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
            Data.delete('returbbk/delete/' + row.no_retur_bbk).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function(id) {
        Data.get('returbbk/view/' + id).then(function(data) {
            $scope.form = data.data;
            $scope.form.tgl = new Date($scope.form.tgl);
            if (jQuery.isEmptyObject(data.detail)) {
                $scope.detailBbk = [{
                        kd_barang: '',
                        jml: '',
                        ket: '',
                    }];
            } else {
                $scope.detailBbk = data.detail;
            }
        });
    }
})
