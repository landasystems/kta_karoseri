app.controller('bomCtrl', function ($scope, Data, toaster, FileUploader, $stateParams, $modal, keyboardManager) {
    Data.get('chassis/merk').then(function (data) {
        $scope.listMerk = data.data;
    });

    $scope.typeChassis = function (merk) {
        Data.get('chassis/tipe?merk=' + merk).then(function (data) {
            $scope.listTipe = data.data;
        });
    };
    $scope.getchassis = function (merk, tipe) {
        Data.get('bom/chassis/?merk=' + merk + '&tipe=' + tipe).then(function (data) {
            $scope.form.kd_chassis = data.kode;
            $scope.form.jenis = data.jenis;
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
    };
    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.resultsbarang = data.data;
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
    $scope.is_copy = false;
    $scope.cariBom = function ($query) {
        if ($query.length >= 3) {
            Data.get('bom/cari', {nama: $query}).then(function (data) {
                $scope.rBom = data.data;
            });
        }
    };
    $scope.addDetail = function () {
        var form = {
            id: '',
            kd_jab: '',
            kd_barang: '',
            qty: '',
            ket: '',
        }
        $scope.modal(form);
        $scope.detBom.unshift(form);
    };
    $scope.removeRow = function (detail) {
        var index = -1;
        var comArr = eval($scope.detBom);
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
                var url = 'bom/deletedetail/';
                var data = {
                    form: detail
                }
                Data.post(url, data).then(function (result) {
                    //
                });
            }
        }

        $scope.detBom.splice(index, 1);
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
        Data.get('bom', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.printTrans = function (id) {
        Data.get('bom/view/' + id).then(function (data) {
            window.open('api/web/bom/exceltrans?print=true', "", "width=500");
        });
    }

    $scope.excelTrans = function (id) {
        Data.get('bom/view/' + id).then(function (data) {
            window.location = 'api/web/bom/exceltrans';
        });
    }

    $scope.create = function (form, detail) {
        $scope.is_copy = false;
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('bom/kode').then(function (data) {
            $scope.form.kd_bom = data.kode;
        });
        $scope.form.tgl_buat = new Date();
        $scope.detBom = [];
    };
    $scope.copy = function (form, detail) {
        $scope.is_copy = true;
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Salin Data";
        $scope.form = {};
        $scope.detBom = [
            {
                kd_jab: '',
                kd_barang: '',
                qty: '',
                ket: '',
            }
        ];
        Data.get('bom/kode').then(function (data) {
            $scope.form.kd_bom = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_copy = false;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.gambar = (form.foto == null) ? [] : form.foto;
        $scope.formtitle = "Edit Data : " + form.kd_bom;
        Data.get('chassis/tipe?merk=' + form.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected(form.kd_bom, '');
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
        $scope.selected(form.kd_bom, '');
    };
    $scope.copyData = function (bom, kd_bom) {
        $scope.form = bom;
        Data.get('chassis/tipe?merk=' + bom.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected(bom.kd_bom, kd_bom);
    };
    $scope.save = function (form, detail) {
//        if (typeof form.foto != 'undefined' && form.foto.length > 0) {
            var data = {
                bom: form,
                detailBom: detail,
            };
            var url = ($scope.is_create == true) ? 'bom/create/' : 'bom/update/' + form.kd_bom;
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
//        } else {
//            toaster.pop('error', "Mohon upload gambar unit terlebih dahulu.");
//        }
    };
    $scope.cancel = function () {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.barang = [];
        $scope.gambar = [];
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function (row) {
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('bom/delete/' + row.kd_bom).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function (id, kd_bom_baru) {
        Data.get('bom/view/' + id).then(function (data) {

            $scope.form = data.data;
            $scope.form.tgl_buat = new Date($scope.form.tgl_buat);
            if (kd_bom_baru != '') {
                $scope.form.kd_bom = kd_bom_baru;
                $scope.form.tgl_buat = new Date();
            }

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
        });
    }

    $scope.modal = function (form) {
        var data = form;
        data.kd_bom = $scope.form.kd_bom;
        data.is_create = $scope.is_create;
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_bom/modal.html',
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

    if ($stateParams.form != null) { //pengecekan jika ada pencarian, dilempar ke view
        $scope.view($stateParams.form);
    }

    keyboardManager.bind('ctrl+s', function () {
        if ($scope.is_edit == true) {
            $scope.save($scope.form, $scope.detBom);
        }
    });

    //============================GAMBAR===========================//
    var uploader = $scope.uploader = new FileUploader({
        url: Data.base + 'bom/upload/?folder=bom',
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
            kode: $scope.form.kd_bom,
        });
    };

    $scope.removeFoto = function (paramindex, namaFoto) {
        var comArr = eval($scope.gambar);
        Data.post('bom/removegambar', {kode: $scope.form.kd_bom, nama: namaFoto}).then(function (data) {
            $scope.gambar.splice(paramindex, 1);
        });

        $scope.form.foto = $scope.gambar;
    };

    $scope.modalFoto = function (kd_bom, img) {
        var modalInstance = $modal.open({
            template: '<img src="img/bom/' + kd_bom + '-350x350-' + img + '" class="img-full" >',
            size: 'md',
        });
    };
});

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

    $scope.save = function (formmodal) {
        var data = {
            form: formmodal,
        };

        var url = (formmodal.id == '') ? 'bom/createdetail/' : 'bom/updatedetail/';
        Data.post(url, data).then(function (result) {
            $scope.formmodal = result.data;
            $modalInstance.dismiss('cancel');
        });
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
})

app.controller('rekapBomCtrl', function ($scope, Data) {
    //init data;
    var paramRef;
    $scope.tableStateRef = '';
    $scope.jenis = '';
    $scope.is_show = false;
    $scope.no_wo = '';
    $scope.wo = [{}];
    $scope.form = {};
    $scope.rekap = function () {
        $scope.jenis = 'rekap';
        $scope.is_show = false;
    }

    $scope.rekapRealisasiWo = function () {
        $scope.jenis = 'realisasi_wo';
        $scope.is_show = false;
    }

    $scope.rekapRealisasiModel = function () {
        $scope.jenis = 'realisasi_model';
        $scope.is_show = false;
    }

    $scope.callServer = function callServer(tableState) {
        $scope.tableStateRef = tableState;
        $scope.isLoading = true;
        var offset = tableState.pagination.start || 0;
        var limit = tableState.pagination.number || 10;
        var param = {offset: offset, limit: limit};
        if (tableState.sort.predicate) {
            param['sort'] = tableState.sort.predicate;
        }
        if (tableState.search.predicateObject) {
            param['filter'] = tableState.search.predicateObject;
        }
        paramRef = param;
        Data.get('bom/rekap', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.excelRekap = function () {
        Data.get('bom/rekap', paramRef).then(function (data) {
            window.location = 'api/web/bom/excel';
        });
    }
    $scope.printRekap = function () {
        Data.get('bom/rekap', paramRef).then(function (data) {
            window.open('api/web/bom/excel?print=true', "", "width=500");
        });
    }
    $scope.cariWo = function ($query) {
        if ($query.length >= 3) {
            Data.get('wo/wospk', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };
    $scope.r_bomWoSrc = [];
    $scope.r_bomWo = [];
    $scope.tmpBomWo = function (form) {
        var data = form;
        Data.post('bom/rekaprealisasiwo', data).then(function (data) {
            $scope.r_bomWoSrc = [];
            angular.forEach(data.data, function ($value, $key) {
                $scope.r_bomWoSrc.push($value);
            });
        });
    };
    $scope.excelRekapRealisasiWo = function () {
        var data = $scope.form;
        Data.post('bom/rekaprealisasiwo', data).then(function (data) {
            window.location = 'api/web/bom/excelrealisasiwo';
        });
    }
    $scope.printRekapRealisasiWo = function () {
        var data = $scope.form;
        Data.post('bom/rekaprealisasiwo', data).then(function (data) {
            window.open('api/web/bom/excelrealisasiwo?print=true', "", "width=500");
        });
    }

    //realisasi model

    Data.get('chassis/merk').then(function (data) {
        $scope.listMerk = data.data;
    });
    $scope.typeChassis = function (merk) {
        Data.get('chassis/tipe?merk=' + merk).then(function (data) {
            $scope.listTipe = data.data;
        });
    };
    $scope.getchassis = function (merk, tipe) {
        Data.get('bom/chassis/?merk=' + merk + '&tipe=' + tipe).then(function (data) {
            $scope.form.kd_chassis = data.kode;
            $scope.form.jenis = data.jenis;
        });
    };
    $scope.getNowo = function (kd_chassis, model) {
        var data = {
            kd_chassis: kd_chassis,
            model: model,
        }
        Data.get('bom/womodel', data).then(function (data) {
            $scope.wo = data.data;
        });
    };
    $scope.cariModel = function ($query) {
        if ($query.length >= 3) {
            Data.get('modelkendaraan/listmodel', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };
    $scope.r_bomModelSrc = [];
    $scope.r_bomModel = [];
    $scope.tmpBomModel = function (form) {
        var data = form;
        Data.post('bom/rekaprealisasimodel', data).then(function (data) {
            $scope.r_bomModelSrc = [];
            angular.forEach(data.data, function ($value, $key) {
                $scope.r_bomModelSrc.push($value);
            });
        });
    };
    $scope.excelRekapRealisasiModel = function () {
        var data = $scope.form;
        Data.post('bom/rekaprealisasimodel', data).then(function (data) {
            window.location = 'api/web/bom/excelrealisasimodel';
        });
    };
    $scope.printRekapRealisasiModel = function () {
        var data = $scope.form;
        Data.post('bom/rekaprealisasimodel', data).then(function (data) {
            window.open('api/web/bom/excelrealisasimodel?print=true', "", "width=500");
        });
    };
})