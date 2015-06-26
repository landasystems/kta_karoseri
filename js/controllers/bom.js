app.controller('bomCtrl', function($scope, $http, Data, toaster, FileUploader) {
    var kode_unik = new Date().getUTCMilliseconds() + "" + (Math.floor(Math.random() * (20 - 10 + 1)) + 10);
    var uploader = $scope.uploader = new FileUploader({
        url: 'js/controllers/upload.php?folder=bom&kode=' + kode_unik,
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


    $scope.listMerk = {};
    $scope.refreshMerk = function(listMerk) {
        var params = {listMerk: listMerk, sensor: false};
        Data.get('bom/merk/?kata=' + listMerk).then(function(response) {
            $scope.merk = response.merk;
        });
    };

    $scope.people = [
        {name: 'Adam', email: 'adam@email.com', age: 10},
        {name: 'Amalie', email: 'amalie@email.com', age: 12},
        {name: 'Wladimir', email: 'wladimir@email.com', age: 30},
        {name: 'Samantha', email: 'samantha@email.com', age: 31},
        {name: 'Estefanía', email: 'estefanía@email.com', age: 16},
        {name: 'Natasha', email: 'natasha@email.com', age: 54},
        {name: 'Nicole', email: 'nicole@email.com', age: 43},
        {name: 'Adrian', email: 'adrian@email.com', age: 21}
    ];

    //init data;
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.detBom = [
        {
            bagian: '',
            kd_barang: '',
            nama_barang: '',
            qty: '',
            keterangan: '',
        }
    ],
            $scope.addDetail = function() {
                var newDet = {
                    bagian: '',
                    kd_barang: '',
                    nama_barang: '',
                    qty: '',
                    keterangan: '',
                }
                $scope.detBom.push(newDet);
            }

    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detBom);
        if (comArr.length > 1) {
            $scope.detBom.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
//    Data.get('bom/merk').then(function(data) {
//        $scope.merk = data.merk;
//    });
    $scope.gettipe = function(merk) {
        Data.get('bom/tipe/?merk=' + merk).then(function(data) {
            $scope.tipe_kendaraan = data.nama_tipe;
        });
    };
    $scope.getchassis = function(merk, tipe) {
        Data.get('bom/chassis/?merk=' + merk + '&tipe=' + tipe).then(function(data) {
            $scope.form.kd_chassis = data.kode;
        });
    };
    Data.get('bom/model').then(function(data) {
        $scope.model = data.model;
    });
    Data.get('bom/barang').then(function(data) {
        $scope.barang = data.barang;
    });
    Data.get('bom/jabatan').then(function(data) {
        $scope.jabatan = data.jabatan;
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

        Data.get('bom', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.create = function(form, detail) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.detail = {};
        Data.get('bom/kode').then(function(data) {
            $scope.form.kd_bom = data.kode;
        });
    };
    $scope.update = function(kd_bom) {
        Data.get('bom/view/' + kd_bom).then(function(data) {
            $scope.form = data.data;
            $scope.detBom = data.detail;
            console.log($scope.form);
            Data.get('bom/tipe/?merk=' + $scope.form.merk).then(function(data) {
                $scope.tipe_kendaraan = data.nama_tipe;
            });
            $scope.is_create = false;
            $scope.is_edit = true;
            $scope.is_view = false;
            $scope.formtitle = "Edit Data : " + $scope.form.kd_bom;
        });
    };
    $scope.view = function(kd_bom) {
        Data.get('bom/view/' + kd_bom).then(function(data) {
            $scope.form = data.data;
            $scope.detBom = data.detail;
            console.log($scope.form);
            Data.get('bom/tipe/?merk=' + $scope.form.merk).then(function(data) {
                $scope.tipe_kendaraan = data.nama_tipe;
            });
            $scope.is_create = false;
            $scope.is_edit = true;
            $scope.is_view = true;
            $scope.formtitle = "Lihat Data : " + $scope.form.kd_bom;
        });
    };
    $scope.save = function(form, detail) {
//        form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        var data = {
            bom: form,
            detailBom: detail,
        };
//        $scope.uploader.uploadAll();
        var url = ($scope.is_create == true) ? 'bom/create/' : 'bom/update/' + form.kd_barang;
        Data.post(url, data).then(function(result) {
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
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('bom/delete/' + row.kd_bom).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
})
