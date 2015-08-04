app.controller('bomCtrl', function($scope, Data, toaster, FileUploader, $modal, $http) {
    var kode_unik = new Date().getUTCMilliseconds() + "" + (Math.floor(Math.random() * (20 - 10 + 1)) + 10);
    var uploader = $scope.uploader = new FileUploader({
        url: 'img/upload.php?folder=bom&kode=' + kode_unik,
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

    Data.get('chassis/merk').then(function(data) {
        $scope.listMerk = data.data;
    });

    $scope.typeChassis = function(merk) {
        Data.get('chassis/tipe?merk=' + merk).then(function(data) {
            $scope.listTipe = data.data;
        });
    }

    $scope.getchassis = function(merk, tipe) {
        Data.get('bom/chassis/?merk=' + merk + '&tipe=' + tipe).then(function(data) {
            $scope.form.kd_chassis = data.kode;
        });
    };

    $scope.open1 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.cariModel = function($query) {
        if ($query.length >= 3) {
            Data.get('modelkendaraan/listmodel', {nama: $query}).then(function(data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.cariBarang = function($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function(data) {
                $scope.resultsbarang = data.data;
            });
        }
    }

    //init data;
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

    $scope.addDetail = function(detail) {
        $scope.detBom.unshift({
            kd_jab: '',
            kd_barang: '',
            qty: '',
            ket: '',
        })
    };
    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detBom);
        if (comArr.length > 1) {
            $scope.detBom.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
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
        Data.get('bom', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.excel = function() {
        Data.get('bom', paramRef).then(function(data) {
            window.location = 'api/web/bom/excel';
        });
    }
    $scope.create = function(form, detail) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.detBom = [
            {
                kd_jab: '',
                kd_barang: '',
                qty: '',
                ket: '',
            }
        ];
        Data.get('bom/kode').then(function(data) {
            $scope.form.kd_bom = data.kode;
        });
    };
    $scope.update = function(form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form = form;
        $scope.formtitle = "Edit Data : " + $scope.form.kd_bom;
        $scope.selected(form.kd_bom);
    };
    $scope.view = function(form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.form = form;
        $scope.formtitle = "Lihat Data : " + $scope.form.kd_bom;
        $scope.selected(form.kd_bom);
    };
    $scope.save = function(form, detail) {
        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.gambar = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        } else {
//            form.gambar = '';
        }

        var data = {
            bom: form,
            detailBom: detail,
        };
        var url = ($scope.is_create == true) ? 'bom/create/' : 'bom/update/' + form.kd_bom;
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
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
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
    $scope.selected = function(id) {
        Data.get('bom/view/' + id).then(function(data) {
            $scope.form = data.data;
            $scope.typeChassis($scope.form.merk);
            if (jQuery.isEmptyObject(data.detail)) {
                $scope.detBom = [
                    {
                        kd_jab: '',
                        kd_barang: '',
                        qty: '',
                        ket: '',
                    }
                ];
            } else {
                $scope.detBom = data.detail;
            }
            $scope.form.tipe = $scope.form.tipe;
        });
    }


    $scope.modal = function(form) {
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_bom/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            resolve: {
                form: function() {
                    return form;
                }
            }
        });
    };

})

app.controller('modalCtrl', function($scope, Data, $modalInstance, form) {

    $scope.cariBagian = function($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/cari', {nama: $query}).then(function(data) {
                $scope.resultsjabatan = data.data;
            });
        }
    }
    $scope.cariBarang = function($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function(data) {
                $scope.resultsbarang = data.data;
            });
        }
    }
    $scope.formmodal = form;
    $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
    };
});
