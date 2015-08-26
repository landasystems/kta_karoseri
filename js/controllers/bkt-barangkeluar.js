app.controller('bbkCtrl', function($scope, Data, toaster) {
//init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_copy = false;
    $scope.jenis_kmp = [];
    $scope.bagian = '-';

    $scope.print = function(no_bbk) {
        Data.get('bbk/print', {no_bbk: no_bbk}).then(function(data) {
            $scope.form.satus = 1;
        });
    }

    $scope.kalkulasi = function(sisa, stok, jml_keluar) {
        $scope.sisa_pengambilan = sisa - jml_keluar;
        $scope.stok_sekarang = stok - jml_keluar;
        if ($scope.sisa_pengambilan > 0) {
            $scope.sisa_pengambilan = $scope.sisa_pengambilan;
        } else {
            $scope.sisa_pengambilan = 0;
            toaster.pop('error', "Sisa pengambilan bahan telah habis");
        }
    }

    $scope.detailstok = function(sisa, stok) {
        $scope.sisa_pengambilan = sisa;
        $scope.stok_sekarang = stok;
    }

    $scope.detailstok(0, 0);

    $scope.cariWo = function($query) {
        if ($query.length >= 3) {
            Data.get('wo/wospk', {nama: $query}).then(function(data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.cariJabatan = function($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/cari', {nama: $query}).then(function(data) {
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

    $scope.listBarang = function($query, no_wo, kd_jab) {
        if ($query.length >= 1) {
            Data.post('bbk/listbarang', {nama: $query, no_wo: no_wo, kd_jab: kd_jab}).then(function(data) {
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

//    $scope.listBarang = function(no_wo, kd_jab) {
//        Data.post('bbk/listbarang', {no_wo: no_wo, kd_jab: kd_jab}).then(function(data) {
//            if (jQuery.isEmptyObject(data.data)) {
//                $scope.detailBbk = [{
//                        kd_barang: '',
//                        jml: '',
//                        ket: '',
//                    }];
//            } else {
//                $scope.detailBbk = data.data;
//            }
//        });
//    }

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

    $scope.copyData = function(bbk, kd_bbk) {
        $scope.form = bbk;
        $scope.selected(bbk.no_bbk, kd_bbk);
    };

    $scope.cariBbk = function($query) {
        if ($query.length >= 3) {
            Data.get('bbk/listbbk', {nama: $query}).then(function(data) {
                $scope.resultBbk = data.data;
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

        Data.get('bbk', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };

    $scope.copy = function(form, detail) {
        $scope.is_copy = true;
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Salin Data";
        $scope.form = {};
        $scope.detailBbk = [{
                kd_barang: '',
                jml: '',
                ket: '',
            }];
        Data.get('bbk/kode').then(function(data) {
            $scope.form.no_bbk = data.kode;
        });
        Data.get('pengguna/profile').then(function(data) {
            $scope.form.petugas = data.data.nama;
        });
    };

    $scope.create = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_copy = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('pengguna/profile').then(function(data) {
            $scope.form.petugas = data.data.nama;
        });
        Data.get('bbk/kode').then(function(data) {
            $scope.form.no_bbk = data.kode;
        });
        $scope.detailBbk = [{
                kd_barang: '',
                jml: '',
                ket: '',
            }];
        $scope.form.tanggal = new Date();
    };

    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_copy = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_bbk;
        $scope.form = form;
        $scope.form.tanggal = new Date(form.tanggal);
        $scope.selected(form.no_bbk, '');
    };

    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_bbk;
        $scope.form = form;
        $scope.form.tanggal = new Date(form.tanggal);
        $scope.selected(form.no_bbk, '');
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
        $scope.is_copy = false;
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

    $scope.selected = function(id, id_baru) {
        Data.get('bbk/view/' + id).then(function(data) {
            $scope.form = data.data;

            if (id_baru != '') {
                $scope.form.no_bbk = id_baru;
                $scope.form.tanggal = new Date();
                Data.get('pengguna/profile').then(function(data) {
                    $scope.form.petugas = data.data.nama;
                });
            }

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
