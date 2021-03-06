app.controller('stiCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

    Data.get('chassis/merk').then(function (data) {
        $scope.listMerk = data.data;
    });

    $scope.typeChassis = function (merk) {
        Data.get('chassis/tipe?merk=' + merk).then(function (data) {
            $scope.listTipe = data.data;
        });
    };
    
    $scope.created_kode = function (kode) {
        var kods = $scope.form.kd_titipan;
        var buat = $scope.is_create;
        if (buat == true) {
            Data.get('serahterimain/kode', {kd: kode}).then(function (data) {
                $scope.form.kd_titipan = data.data;
            });
        } else {
            $scope.form.kd_titipan = kods;
        }
    }

    $scope.getchassis = function (merk, tipe) {
        Data.get('bom/chassis/?merk=' + merk + '&tipe=' + tipe).then(function (data) {
            $scope.form.kd_chassis = data.kode;
            $scope.form.jenis = data.jenis;
        });
    };

    $scope.cariCustomer = function ($query) {
        if ($query.length >= 3) {
            Data.get('customer/cari', {nama: $query}).then(function (data) {
                $scope.kdCust = data.data;
            });
        }
    };
    $scope.getCustomer = function (form, items) {
        form.kd_cust = items.kd_cust;
        form.alamat1 = items.alamat1;
    };
    $scope.cariChassis = function ($query) {
        if ($query.length >= 3) {
            Data.get('chassis/cari', {nama: $query}).then(function (data) {
                $scope.kdChassis = data.data;
            });
        }
    };
    $scope.getChassis = function (form, items) {
        form.kd_chassis = items.kd_chassis;
        form.merk = items.merk;
        form.tipe = items.tipe;
    };
    Data.get('serahterimain/warna').then(function (data) {
        $scope.list_warna = data.list_warna;
    });
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.open2 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };
    $scope.open3 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened3 = true;
    };
    $scope.open4 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened4 = true;
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
        paramRef = param;
        Data.get('serahterimain', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function () {
        Data.get('serahterimain', paramRef).then(function (data) {
            window.location = 'api/web/serahterimain/excel';
        });
    }

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tgl_terima = new Date();
        $scope.form.serah_terima = new Date();
        $scope.form.tgl_prd = new Date();
        $scope.form.tgl_pdc = new Date();
    };
    $scope.update = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.kd_titipan;
        var kode = form.kd_titipan;
        $scope.format_kode = kode.substr(0,2);
        $scope.selected(form);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.kd_titipan;
        var kode = form.kd_titipan;
        $scope.format_kode = kode.substr(0,2);
        $scope.selected(form);
    };
    
    $scope.selected = function (form) {
        $scope.form = form;
        
        $scope.form.tgl_terima = new Date(form.tgl_terima);
        $scope.form.serah_terima = new Date(form.serah_terima);
        $scope.form.tgl_prd = new Date(form.tgl_prd);
        $scope.form.tgl_pdc = new Date(form.tgl_pdc);
       Data.get('chassis/tipe?merk=' + form.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.form.tipe = form.tipe;
    };
    
    $scope.save = function (form) {
        var url = 'serahterimain/create';
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.callServer(tableStateRef); //reload grid ulang
                $scope.is_edit = false;
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });

    };
    $scope.cancel = function () {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.is_edit = false;
        $scope.is_view = false;
    };

    $scope.trash = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS item ini ?")) {
            row.is_deleted = 1;
            Data.post('serahterimain/update/' + row.id, row).then(function (result) {
                ctrl.displayed.splice(ctrl.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.restore = function (row) {
        if (confirm("Apa anda yakin akan MERESTORE item ini ?")) {
            row.is_deleted = 0;
            Data.post('serahterimain/update/' + row.id, row).then(function (result) {
                ctrl.displayed.splice(ctrl.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.delete = function (row) {
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('serahterimain/delete/' + row.kd_cust).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.tagTransform = function (newTag) {
        var item = {
            kd_warna: '',
            warna: newTag,
        };

        return item;
    };
});
