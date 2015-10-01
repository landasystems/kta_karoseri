app.controller('bomCtrl', function ($scope, Data, toaster, FileUploader, $stateParams, $modal, keyboardManager) {
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
            var xz = item.size <= 1048576;
            if (!xz) {
                toaster.pop('error', "Ukuran gambar tidak boleh lebih dari 1 MB");
            }
        }
    });
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
    $scope.addDetail = function (detail) {
        $scope.detBom.unshift({
            kd_jab: '',
            kd_barang: '',
            qty: '',
            ket: '',
        })
    };
    $scope.removeRow = function (paramindex) {
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
        $scope.detBom = [
            {
                kd_jab: '',
                kd_barang: '',
                qty: '',
                ket: '',
            }
        ];
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
        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.gambar = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        }
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
                $scope.form.tgl_buat = '';
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
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_bom/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            resolve: {
                form: function () {
                    return form;
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