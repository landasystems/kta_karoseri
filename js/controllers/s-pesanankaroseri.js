app.controller('spkaroseriCtrl', function ($scope, Data, toaster) {
//init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.jenis_kmp = [];
    $scope.bagian = '-';
    $scope.is_ppn = false;

    Data.get('chassis/merk').then(function (data) {
        $scope.listMerk = data.data;
    });

    $scope.typeChassis = function (merk) {
        Data.get('chassis/tipe?merk=' + merk).then(function (data) {
            $scope.listTipe = data.data;
        });
    }

    $scope.getchassis = function (merk, tipe) {
        Data.get('bom/chassis/?merk=' + merk + '&tipe=' + tipe).then(function (data) {
            $scope.form.kd_chassis = data.kode;
        });
    };

    $scope.kode = function (tipe) {
        Data.post('spkaroseri/kode', {tipe: tipe}).then(function (data) {
            $scope.form.no_spk = data.kode;
        });
    }

    $scope.kalkulasi = function () {
        var jml = (!$scope.form.jml_unit) ? 0 : $scope.form.jml_unit * 1;
        var harga_karoseri = (!$scope.form.harga_karoseri) ? 0 : $scope.form.harga_karoseri * 1;
        var harga_optional = (!$scope.form.harga_optional) ? 0 : $scope.form.harga_optional * 1;
        var is_ppn = (!$scope.form.is_ppn) ? 0 : $scope.form.is_ppn * 1;
        var ppn = 0;
        var total = 0;

        if ($scope.is_ppn == true) {
            ppn = (10 / 100) * (harga_karoseri * jml);
            $scope.form.ppn = ppn;
        } else {
            $scope.form.ppn = 0;
            ppn = 0;
        }

        var jml_harga = (harga_karoseri * jml) + harga_optional;
        $scope.form.jml_harga = jml_harga;
        total = jml_harga + ppn;
        $scope.form.total_harga = total;
        var sisa = total - $scope.form.uang_muka;
        $scope.form.sisa_bayar = sisa;
    }

    $scope.cariCustomer = function ($query) {
        if ($query.length >= 3) {
            Data.get('customer/cari', {nama: $query}).then(function (data) {
                $scope.rCustomer = data.data;
            });
        }
    }

    $scope.cariModel = function ($query) {
        if ($query.length >= 3) {
            Data.get('modelkendaraan/listmodel', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.cariSales = function ($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/listkaryawansales', {nama: $query}).then(function (data) {
                $scope.rSales = data.data;
            });
        }
    }

    $scope.cariBom = function ($query) {
        if ($query.length >= 3) {
            Data.get('bom/cari', {nama: $query}).then(function (data) {
                $scope.rBom = data.data;
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

        Data.get('spkaroseri', param).then(function (data) {
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
        $scope.detailBbk = [{
                kd_barang: '',
                jml: '',
                ket: '',
            }];
    };

    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_spk;
        Data.get('chassis/tipe?merk=' + form.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected(form.no_spk);
    };

    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_create = false;
        $scope.formtitle = "Lihat Data : " + form.no_spk;
        Data.get('chassis/tipe?merk=' + form.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected(form.no_spk);
    };

    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'spkaroseri/create' : 'spkaroseri/update/' + form.no_spk;
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
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('spkaroseri/delete/' + row.no_spk).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function (no_spk) {
        Data.get('spkaroseri/view/' + no_spk).then(function (data) {
            $scope.form = data.data;
            $scope.form.tgl = new Date($scope.form.tgl);
        });
    }
});

app.controller('rekapSpk', function ($scope, Data) {
    var tableStateRef;
    var paramRef;

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
        Data.get('spkaroseri/rekap', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };

    $scope.excel = function () {
        Data.get('spkaroseri/rekap', paramRef).then(function (data) {
            window.location = 'api/web/spkaroseri/excel';
        });
    }

    $scope.print = function () {
        Data.get('spkaroseri/rekap', paramRef).then(function (data) {
            window.open('api/web/spkaroseri/excel?print=true');
        });
    }

});
