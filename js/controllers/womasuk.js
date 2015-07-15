app.controller('womasukCtrl', function($scope, Data, toaster, FileUploader) {
    var kode_unik = new Date().getUTCMilliseconds() + "" + (Math.floor(Math.random() * (20 - 10 + 1)) + 10);
    var uploader = $scope.uploader = new FileUploader({
        url: 'js/controllers/upload.php?folder=womasuk&kode=' + kode_unik,
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
    $scope.open2 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };
    $scope.open3 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened3 = true;
    };
    Data.post('womasuk/warna').then(function (data) {
        $scope.list_warna = data.warna;
    });

    $scope.cariSpk = function($query) {
        if ($query.length >= 3) {
            Data.get('womasuk/cari', {nama: $query}).then(function(data) {
                $scope.results = data.data;
            });
        }
    }
    Data.post('womasuk/spk').then(function (data) {
        $scope.list_spk = data.spk;
    });
    $scope.getspk = function (wo) {
//        alert('asjdfhasjdfkas');
        Data.post('womasuk/getspk/', wo).then(function (data) {
            $scope.form = data.spk;
            console.log($scope.form);

        });
    };

    $scope.pilih = function(form, $item) {
        console.log($item.customer);
        form.customer = $item.customer;
        form.pemilik = $item.pemilik;
        form.sales = $item.sales;
        form.warna = $item.warna;
//        form.model_kendaraan = $item.warna;
        form.merk = $item.merk;
        form.model_chassis = $item.model_chassis;
        form.no_chassis = $item.no_chassis;
        form.no_mesin = $item.no_mesin;
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

        Data.get('womasuk', param).then(function(data) {
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
         $scope.eks = {};
        $scope.inter = {};

    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form);
        $scope.form = {};
        $scope.eks = {};
        $scope.inter = {};
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
         $scope.selected(form.id);
    };
    $scope.save = function(form) {
        $scope.uploader.uploadAll();
        form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
         var data = {
            womasuk: form,
            eksterior: eks,
            interior: inter,
        };
        var url = ($scope.is_create == true) ? 'womasuk/create' : 'womasuk/update/'+ form.no_wo;
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
            Data.delete('delivery/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function(form) {
        Data.post('womasuk/select/', form).then(function(data) {
            $scope.form = data.data;
            $scope.eks = data.eksterior[0];
            $scope.inter = data.interior[0];
            $scope.form.warna = data.det.warna;
            $scope.form.no_spk = '234';
            $scope.form.customer = data.det.customer;
            $scope.form.sales = data.det.sales;
            $scope.form.pemilik = data.det.pemilik;
            $scope.form.model_chassis = data.det.model_chassis;
            $scope.form.merk = data.det.merk;
            $scope.form.tipe = data.det.tipe;
            $scope.form.model = data.det.model;
            $scope.form.no_rangka = data.det.no_rangka;
            $scope.form.no_mesin = data.det.no_mesin;
            $scope.form.jenis = data.det.jenis;
            $scope.form.jenis = data.det.jenis;
            $scope.form.no_spk = data.data.no_spk.as;
            console.log(data.data);
            

        });
    }


})
