app.controller('deliveryCtrl', function($scope, Data, toaster, FileUploader) {
    var kode_unik = new Date().getUTCMilliseconds() + "" + (Math.floor(Math.random() * (20 - 10 + 1)) + 10);
    var uploader = $scope.uploader = new FileUploader({
        url: 'img/upload.php?folder=delivery&kode=' + kode_unik,
        queueLimit: 1,
        removeAfterUpload: true,
    });

    uploader.filters.push({
        name: 'imageFilter',
        fn: function(item) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
    });
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_create = false;

    $scope.open1 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.cariProduk = function($query) {
        if ($query.length >= 3) {
            Data.get('ujimutu/cari', {nama: $query}).then(function(data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.pilih = function(form, $item) {
        Data.post('delivery/customer/', $item).then(function(data) {
            $scope.sCUstomer = data.customer;
            console.log(data.customer);
        });
        form.merk = $item.merk;
        form.model = $item.model;
        form.sales = $item.sales;
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

        Data.get('delivery', param).then(function(data) {
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
        $scope.form.tgl_delivery = new Date();

    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
        $scope.form.tgl_delivery = new Date(form.tgl_delivery);
        $scope.selected(form.id);
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form.id);
    };
    $scope.save = function(form) {
        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        } else {
            form.foto = '';
        }
        var url = ($scope.is_create == true) ? 'delivery/create' : 'delivery/update/' + form.id;
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
            Data.delete('delivery/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function(id) {
        Data.get('delivery/view/' + id).then(function(data) {
            $scope.form = data.data;
            $scope.form.merk = data.data.no_wo.merk;
            $scope.form.model = data.data.no_wo.model;
            $scope.form.sales = data.data.no_wo.sales;


        });
    }


})
