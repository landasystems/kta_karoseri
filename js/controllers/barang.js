app.controller('barangCtrl', function($scope, Data, toaster, FileUploader) {
    var kode_unik = new Date().getUTCMilliseconds() + "" + (Math.floor(Math.random() * (20 - 10 + 1)) + 10);
    var uploader = $scope.uploader = new FileUploader({
        url: 'js/controllers/upload.php?folder=barang&kode=' + kode_unik,
        queueLimit: 1,
        removeAfterUpload: true
    });
    // FILTERS
    uploader.filters.push({
        name: 'imageFilter',
        fn: function(item) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
    });

    //init data;
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

    $scope.jenis_barang = {
        minimumInputLength: 3,
        allowClear: true,
    }

    Data.get('barang/jenis').then(function(data) {
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

        Data.get('barang', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function(form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('barang/kode').then(function(data) {
            $scope.form.kd_barang = data.kode;
        });
    };
    $scope.update = function(form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nm_barang;
        $scope.form = form;
    };
    $scope.view = function(form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nm_barang;
        $scope.form = form;
    };
    $scope.save = function(form) {
        $scope.uploader.uploadAll();
        form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        var url = ($scope.is_create == true) ? 'barang/create/' : 'barang/update/' + form.kd_barang;
        Data.post(url, form).then(function(result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });
    };
    $scope.cancel = function() {
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.callServer(tableStateRef);
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('barang/delete/' + row.kd_barang).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})