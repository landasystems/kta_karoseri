app.controller('tambahItemCtrl', function ($scope, Data, toaster, FileUploader, $modal, $http) {
    var kode_unik = new Date().getUTCMilliseconds() + "" + (Math.floor(Math.random() * (20 - 10 + 1)) + 10);
    var uploader = $scope.uploader = new FileUploader({
        url: 'img/upload.php?folder=bom&kode=' + kode_unik,
        queueLimit: 1,
        removeAfterUpload: true,
    });
    uploader.filters.push({
        name: 'imageFilter',
        fn: function (item) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
    });
    $scope.cariBagian = function ($query) {
        if ($query.length >= 3) {
            Data.get('bom/cari', {nama: $query}).then(function (data) {
                $scope.kdBom = data.data;
            });
        }
    };
    $scope.cariWo = function ($query) {
        if ($query.length >= 3) {
            Data.get('wo/cari', {no_wo: $query}).then(function (data) {
                $scope.noWo = data.data;
            });
        }
    };

    $scope.getChassis = function (form, items) {
        form.kd_chassis = items.kd_chassis;
        form.kd_bom = items.kd_bom;
    };
    $scope.getWo = function (form, items) {
        form.no_wo = items.no_wo;
    };

    Data.get('chassis/merk').then(function (data) {
        $scope.listMerk = data.data;
    });

    $scope.typeChassis = function (merk) {
        Data.get('chassis/tipe?merk=' + merk).then(function (data) {
            $scope.listTipe = data.data;
        });
    }

    $scope.getchassis = function (merk, tipe) {
        Data.get('additionalbom/chassis/?merk=' + merk + '&tipe=' + tipe).then(function (data) {
            $scope.form.kd_chassis = data.kode;
        });
    };

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.cariModel = function ($query) {
        if ($query.length >= 3) {
            Data.get('modelkendaraan/listmodel', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
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

    $scope.addDetail = function (detail) {
        $scope.detTambahItem.unshift({
            kd_jab: '',
            kd_barang: '',
            qty: '',
            ket: '',
        })
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detTambahItem);
        if (comArr.length > 1) {
            $scope.detTambahItem.splice(paramindex, 1);
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
        Data.get('additionalbom', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.excel = function () {
        Data.get('additionalbom', paramRef).then(function (data) {
            window.location = 'api/web/additionalbom/excel';
        });
    };

    $scope.create = function (form, detail) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tgl_buat = new Date();
        $scope.detTambahItem = [
            {
                kd_jab: '',
                kd_barang: '',
                qty: '',
                ket: '',
            }
        ];
//        Data.get('additionalbom/kode').then(function(data) {
//            $scope.form.kd_bom = data.kode;
//        });
    };
    $scope.update = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.form = form;
        $scope.formtitle = "Edit Data : " + $scope.form.kd_bom;
        $scope.selected(form.id);
    };
    $scope.view = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.form = form;
        $scope.formtitle = "Lihat Data : " + $scope.form.kd_bom;
        $scope.selected(form.id);
    };
    $scope.save = function (form, detail) {
        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.gambar = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        } else {
//            form.gambar = '';
        }

        var data = {
            tambahItem: form,
            detTambahItem: detail,
        };
        var url = ($scope.is_create == true) ? 'additionalbom/create/' : 'additionalbom/update/' + form.id;
        Data.post(url, data).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
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
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('additionalbom/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function (id) {
        Data.get('additionalbom/view/' + id).then(function (data) {
//            $scope.typeChassis($scope.form.merk);
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
                $scope.detTambahItem = data.detail;
            }
//            $scope.form.tipe = $scope.form.tipe;
        });
    }


    $scope.modal = function (form) {
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_tambahitem/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            resolve: {
                form: function () {
                    return form;
                }
            }
        });
    };

})

app.controller('modalCtrl', function ($scope, Data, $modalInstance, form) {

    $scope.cariBagian = function ($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/cari', {nama: $query}).then(function (data) {
                $scope.resultsjabatan = data.data;
            });
        }
    }
    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.resultsbarang = data.data;
            });
        }
    }
    $scope.formmodal = form;
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});
