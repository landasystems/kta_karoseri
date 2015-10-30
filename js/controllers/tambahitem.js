app.controller('tambahItemCtrl', function ($scope, Data, toaster, FileUploader, $modal, $http, keyboardManager) {
    $scope.noWo = [];
    $scope.pilih = {};

    $scope.validasi = function () {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('additionalbom/validasi/', $scope.pilih).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan");
                } else {
                    $scope.is_edit = false;
                    $scope.callServer(tableStateRef); //reload grid ulang
                    toaster.pop('success', "Berhasil", "Data Berhasil Terproses");
                }
            });
        }
    };

    $scope.bukavalidasi = function () {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('additionalbom/bukavalidasi/', $scope.pilih).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan");
                } else {
                    $scope.is_edit = false;
                    $scope.callServer(tableStateRef); //reload grid ulang
                    toaster.pop('success', "Berhasil", "Data Berhasil Terproses");
                }
            });
        }
    };

    $scope.cariWo = function ($query) {
        if ($query.length >= 2) {
            Data.post('additionalbom/cari', {no_wo: $query, selected: $scope.form.no_wo}).then(function (data) {
                $scope.noWo = data.data;
            });
        }
    };

    $scope.cariBom = function ($query) {
        if ($query.length >= 3) {
            Data.get('bom/cari', {nama: $query}).then(function (data) {
                $scope.rBom = data.data;
            });
        }
    };

    $scope.getChassis = function (form, items) {
        form.kd_chassis = items.kd_chassis;
        form.kd_bom = items.kd_bom;
        $scope.selectBom(form.kd_bom);
    };

    Data.get('chassis/merk').then(function (data) {
        $scope.listMerk = data.data;
    });

    $scope.typeChassis = function (merk) {
        Data.get('chassis/tipe?merk=' + merk).then(function (data) {
            $scope.listTipe = data.data;
        });
    }

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
    };

    //init data;
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

    $scope.addDetail = function (detail) {
        var form = {
            id: '',
            kd_jab: '',
            kd_barang: '',
            qty: '',
            ket: '',
        }
        $scope.modal(form);
        $scope.detTambahItem.unshift(form);
    };
    $scope.removeRow = function (detail) {
        var index = -1;
        var comArr = eval($scope.detTambahItem);
        for (var i = 0; i < comArr.length; i++) {
            if (comArr[i] === detail) {
                index = i;
                break;
            }
        }

        if (index === -1) {
            alert("Something gone wrong");
        } else {
            if ($scope.is_create == false) {
                var url = 'additionalbom/deletedetail/';
                var data = {
                    form: detail
                }
                Data.post(url, data).then(function (result) {
                    //
                });
            }
        }

        $scope.detTambahItem.splice(index, 1);
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

    $scope.create = function (form, detail) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tgl_buat = new Date();
        $scope.detTambahItem = [
            {
                id: '',
                kd_jab: '',
                kd_barang: '',
                qty: '',
                ket: '',
            }
        ];
    };

    $scope.update = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.gambar = (form.foto == null) ? [] : form.foto;
        $scope.formtitle = "Lihat Data : " + form.kd_bom;
        Data.get('chassis/tipe?merk=' + form.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected(form.id);
    };

    $scope.view = function (form) {
        $scope.is_copy = false;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.gambar = (form.foto == null) ? [] : form.foto;
        $scope.formtitle = "Lihat Data : " + form.kd_bom;
        Data.get('chassis/tipe?merk=' + form.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected(form.id);
    };

    $scope.save = function (form, detail) {
        var data = {
            tambahItem: form,
            detTambahItem: detail,
        };
        var url = ($scope.is_create == true) ? 'additionalbom/create/' : 'additionalbom/update/' + form.id;
        Data.post(url, data).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_create = false;
                $scope.is_edit = false;
                $scope.barang = [];
                $scope.gambar = [];
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });
    };

    $scope.cancel = function () {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.barang = [];
        $scope.gambar = [];
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.is_create = false;
    };

    $scope.delete = function (row) {
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('additionalbom/delete/' + row.id).then(function (result) {
                $scope.callServer(tableStateRef);
            });
        }
    };

    $scope.selected = function (id) {
        Data.get('additionalbom/view/' + id).then(function (data) {
            $scope.form = data.data;
            $scope.form.tgl_buat = new Date($scope.form.tgl_buat);
            if (jQuery.isEmptyObject(data.detail)) {
                $scope.detTambahItem = [
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
        });
    };

    $scope.selectBom = function (bom) {
        $scope.form = bom;
        Data.get('chassis/tipe?merk=' + bom.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected2(bom.kd_bom);
    };

    $scope.selected2 = function (kd_bom) {
        Data.get('bom/view/' + kd_bom).then(function (data) {
            $scope.form = data.data;
            $scope.form.kd_bom = {
                kd_bom: data.data.kd_bom,
            };
            $scope.form.tgl_buat = new Date();
            if (jQuery.isEmptyObject(data.detail)) {
                $scope.detTambahItem = [
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
        });
    }

    $scope.modal = function (form) {
        var data = form;
        data.tran_additional_bom_id = $scope.form.id;
        data.is_create = $scope.is_create;
        data.kd_bom = $scope.form.kd_bom.kd_bom;
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_tambahitem/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            backdrop: 'static',
            resolve: {
                form: function () {
                    return data;
                }
            }
        });
    };

    keyboardManager.bind('ctrl+s', function () {
        if ($scope.is_edit == true) {
            $scope.save($scope.form, $scope.detTambahItem);
        }
    });

    //============================GAMBAR===========================//
    var uploader = $scope.uploader = new FileUploader({
        url: Data.base + 'additionalbom/upload/?folder=bom',
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
        Data.post('additionalbom/removegambar', {kode: $scope.form.id, nama: namaFoto}).then(function (data) {
            $scope.gambar.splice(paramindex, 1);
        });

        $scope.form.foto = $scope.gambar;
    };

    $scope.modalFoto = function (id, img) {
        var modalInstance = $modal.open({
            template: '<img src="img/bom/' + id + '-350x350-' + img + '" class="img-full" >',
            size: 'md',
        });
    };
    //======================= END GAMBAR ====================//
})

app.controller('modalCtrl', function ($scope, Data, $modalInstance, form) {

    $scope.cariBagian = function ($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/cari', {nama: $query}).then(function (data) {
                $scope.resultsjabatan = data.data;
            });
        }
    };

    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.resultsbarang = data.data;
            });
        }
    };

    $scope.formmodal = form;
    console.log($scope.formmodal);

    $scope.save = function (formmodal) {
        var data = {
            form: formmodal,
        };

        var url = (formmodal.id == '') ? 'additionalbom/createdetail/' : 'additionalbom/updatedetail/';
        Data.post(url, data).then(function (result) {
            $scope.formmodal = result.data;
            $modalInstance.dismiss('cancel');
        });
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});
