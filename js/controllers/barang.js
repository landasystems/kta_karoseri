app.controller('barangCtrl', function ($scope, Data, toaster, FileUploader, $modal) {
    var kode_unik = new Date().getUTCMilliseconds() + "" + (Math.floor(Math.random() * (20 - 10 + 1)) + 10);
    var uploader = $scope.uploader = new FileUploader({
        url: 'img/upload.php?folder=barang&kode=' + kode_unik,
        removeAfterUpload: true,
    });
    uploader.filters.push({
        name: 'imageFilter',
        fn: function (item) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            var x = '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            if (!x) {
                toaster.pop('error', "Jenis gambar tidak sesuai");
            }
            return x;
        }
    });

//    uploader.filters.push({
//        name: 'sizeFilter',
//        fn: function (item) {
//            var xz = item.size <= 1048576;
//            if (!xz) {
//                toaster.pop('error', "Ukuran gambar tidak boleh lebih dari 1 MB");
//            }
//        }
//    });

    $scope.gambar = [];

    // CALLBACKS
    uploader.onSuccessItem = function (fileItem, response) {
        if (response.answer == 'File transfer completed') {
            $scope.gambar.unshift({name: response.name});
            $scope.form.foto = $scope.gambar;
        }
    };

    $scope.removeFoto = function (paramindex) {
        var comArr = eval($scope.gambar);
        if (comArr.length > 1) {
            $scope.gambar.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
        $scope.form.foto = $scope.gambar;
    };

    $scope.modal = function (img) {
        var modalInstance = $modal.open({
            template: '<img src="img/barang/' + img + '" style="width:100%;" class="img-responsive">',
            size: 'lg',
        });
    };

    //init data;
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.qty = function (max, saldo) {
        var qty = max - saldo;
        $scope.form.qty = qty;
    }
    Data.get('barang/jenis').then(function (data) {
        $scope.jenis_brg = data.jenis_brg;
    });
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
        Data.get('barang', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.excel = function () {
        Data.get('barang', paramRef).then(function (data) {
            window.location = 'api/web/barang/excel';
        });
    };
    $scope.print = function () {
        Data.get('barang', paramRef).then(function (data) {
            window.open('api/web/barang/excel?print=true', "", "width=500");
        });
    };
    $scope.kode = function (kd_jenis) {
        Data.post('barang/kode', {kd_jenis: kd_jenis}).then(function (data) {
            $scope.form.kd_barang = data.kode;
        });
    }
    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
    };
    $scope.update = function (form) {
        $scope.form = form;
        $scope.gambar = ($scope.form.foto == null) ? [] : $scope.form.foto;
        $scope.selectJenis(form);
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nm_barang;
        $scope.qty(form.max, form.saldo);
    };
    $scope.view = function (form) {
        $scope.form = form;
        $scope.gambar = $scope.form.foto;
        $scope.selectJenis(form);
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nm_barang;
        $scope.qty(form.max, form.saldo);
    };
    $scope.selectJenis = function (form) {
        $scope.form.jenis = {
            kd_jenis: form.kd_jenis,
            jenis_brg: form.jenis_brg,
            kd: form.kd,
        }
    };
    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'barang/create/' : 'barang/update/' + form.kd_barang;
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.barang = [];
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });
    };
    $scope.cancel = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
    };
    $scope.delete = function (row) {
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('barang/delete/' + row.kd_barang).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    }
    ;
});