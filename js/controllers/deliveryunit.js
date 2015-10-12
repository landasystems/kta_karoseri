app.controller('deliveryCtrl', function ($scope, Data, toaster, FileUploader, $modal) {
    //============================GAMBAR===========================//
    var uploader = $scope.uploader = new FileUploader({
        url: Data.base + 'delivery/upload/?folder=delivery',
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
            kode: $scope.form.id,
        });
    };

    $scope.removeFoto = function (paramindex, namaFoto) {
        var comArr = eval($scope.gambar);
        Data.post('delivery/removegambar', {kode: $scope.form.id, nama: namaFoto}).then(function (data) {
            $scope.gambar.splice(paramindex, 1);
        });

        $scope.form.foto = $scope.gambar;
    };

    $scope.modal = function (id, img) {
        var modalInstance = $modal.open({
            template: '<img src="img/delivery/' + id + '-350x350-' + img + '" class="img-full" >',
            size: 'md',
        });
    };

    //init data
    var tableStateRef;
    var paramRef;

    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_print = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_create = false;



    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.cariProduk = function ($query) {
        if ($query.length >= 3) {
            Data.get('ujimutu/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }
    $scope.cariCustomer = function ($query) {
        if ($query.length >= 3) {
            Data.get('customer/cari', {nama: $query}).then(function (data) {
                $scope.kdCust = data.data;
            });
        }
    };
    $scope.getCustomer = function (form, items) {
        form.kd_cust = items.kd_cust;
    };

    $scope.pilih = function (form, $item) {

        Data.post('delivery/customer/', $item).then(function (data) {
            form.customer = data.customer.nm_customer + " - " + data.customer.alamat1;
            form.kd_cust = data.customer.kd_cust;

        });
        form.merk = $item.merk;
        form.model = $item.model;
        form.sales = $item.sales;
        form.no_wo = $item.no_wo;
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
        paramRef = param;
        Data.get('delivery', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.printG = function () {
        Data.get('delivery', paramRef).then(function (data) {
            window.open = 'api/web/delivery/garansi';
        });
    }

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_print = false;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('delivery/kode').then(function (data) {
            $scope.form.no_delivery = data.kode;
        });
        $scope.form.tgl_delivery = new Date();

    };
    $scope.update = function (form) {
        $scope.is_print = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
        $scope.gambar = $scope.form.foto;
        $scope.form.customer = form.customer.nm_customer + " - " + form.customer.alamat1;
        $scope.form.tgl_delivery = new Date(form.tgl_delivery);
    };
    $scope.view = function (form) {

        $scope.is_print = true;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.gambar = $scope.form.foto;
    };
    $scope.save = function (form) {
        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        } else {
            form.foto = '';
        }
        var url = ($scope.is_create == true) ? 'delivery/create' : 'delivery/update/' + form.id;
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
            Data.delete('delivery/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function (id) {
        Data.get('delivery/view/' + id).then(function (data) {
            $scope.form = data.data;
            $scope.form.merk = data.data.no_wo.merk;
            $scope.form.model = data.data.no_wo.model;
            $scope.form.sales = data.data.no_wo.sales;
        });
    }


})
