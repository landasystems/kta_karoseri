app.controller('barangCtrl', function ($scope, Data, toaster, FileUploader, $modal) {
    //init data;

    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.form = {};
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
                $scope.form = {};
                $scope.is_create = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });
    };
    $scope.cancel = function () {
        $scope.gambar = [];
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
    };

    //============================GAMBAR===========================//
    var uploader = $scope.uploader = new FileUploader({
        url: Data.base + 'barang/upload/?folder=barang',
        formData: [],
        removeAfterUpload: true,
    });

    $scope.uploadGambar = function (form) {
        $scope.uploader.uploadAll();
    };

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

    uploader.filters.push({
        name: 'sizeFilter',
        fn: function (item) {
            var xz = item.size < 2097152;
            if (!xz) {
                toaster.pop('error', "Ukuran gambar tidak boleh lebih dari 2 MB");
            }
            return xz;
        }
    });

    $scope.gambar = [];

    uploader.onSuccessItem = function (fileItem, response) {
        if (response.answer == 'File transfer completed') {
            $scope.gambar.unshift({name: response.name});
            $scope.form.foto = $scope.gambar;
        }
    };

    uploader.onBeforeUploadItem = function (item) {
        item.formData.push({
            kode: $scope.form.kd_barang,
        });
    };

    $scope.removeFoto = function (paramindex, namaFoto) {
        var comArr = eval($scope.gambar);
        Data.post('barang/removegambar', {kode: $scope.form.kd_barang, nama: namaFoto}).then(function (data) {
            $scope.gambar.splice(paramindex, 1);
        });

        $scope.form.foto = $scope.gambar;
    };

    $scope.modal = function (kd_barang, img) {
        var modalInstance = $modal.open({
            template: '<img src="img/barang/' + kd_barang + '-350x350-' + img + '" class="img-full" >',
            size: 'md',
        });
    };
}
);