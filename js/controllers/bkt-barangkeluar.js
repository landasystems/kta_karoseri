app.controller('returbbkCtrl', function($scope, Data, toaster) {
//init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.jenis_kmp = [];
    $scope.bagian = '-';

    $scope.cariWo = function($query) {
        if ($query.length >= 3) {
            Data.get('wo/wospk', {nama: $query}).then(function(data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.cariJabatan = function($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/listjabatan', {nama: $query}).then(function(data) {
                $scope.resultsjabatan = data.data;
            });
        }
    }

    $scope.cariKaryawan = function($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/listkaryawan', {nama: $query}).then(function(data) {
                $scope.resultskaryawan = data.data;
            });
        }
    }

    $scope.cariBarang = function($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function(data) {
                $scope.resultsbarang = data.data;
            });
        }
    }

    $scope.addDetail = function(detail) {
        $scope.detailBbk.unshift({
            kd_barang: '',
            jml: '',
            ket: '',
        })
    };
    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detailBbk);
        if (comArr.length > 1) {
            $scope.detailBbk.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };

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

        Data.get('bbk', param).then(function(data) {
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
        $scope.detailBbk = [{
                kd_barang: '',
                jml: '',
                ket: '',
            }];
        Data.get('bbk/kode').then(function(data) {
            $scope.form.no_bbk = data.kode;
        });
        Data.get('bbk/petugas').then(function(data) {
            $scope.form.petugas = data.petugas;
        });
    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_bbk;
        $scope.form = form;
        $scope.selected(form.no_bbk);
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form.no_bbk);
    };
    $scope.save = function(form, detail) {
        var data = {
            bbk: form,
            detailBbk: detail,
        };
        var url = ($scope.is_create == true) ? 'bbk/create' : 'bbk/update/' + form.no_bbk;
        Data.post(url, data).then(function(result) {
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
            Data.delete('bbk/delete/' + row.no_bbk).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function(id) {
        Data.get('bbk/view/' + id).then(function(data) {
            $scope.form = data.data;
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
