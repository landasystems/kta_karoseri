app.controller('bomCtrl', function($scope, Data, toaster, FileUploader) {
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

    $scope.merk = {
        minimumInputLength: 3,
        allowClear: false,
        ajax: {
            url: "api/web/bom/merk/",
            dataType: 'json',
            data: function(term) {
                return {
                    kata: term,
                };
            },
            results: function(data, page) {
                return {
                    results: data.merk
                };
            }
        },
        formatResult: function(object) {
            return object.merk;
        },
        formatSelection: function(object) {
            return object.merk;
        },
        id: function(data) {
            return data.merk
        },
        initSelection : function(element, callback) {
            var obj = {id: 1, text: 'whatever value'};
            callback(obj);
        },
    };

    $scope.model = {
        minimumInputLength: 3,
        allowClear: true,
        initSelection: function(el, fn) {
        },
        ajax: {
            url: "api/web/bom/model/",
            dataType: 'json',
            data: function(term) {
                return {
                    kata: term,
                };
            },
            results: function(data, page) {
                return {
                    results: data.model
                };
            }
        },
        formatResult: function(object) {
            return object.model;
        },
        formatSelection: function(object) {
            return object.model;
        },
        id: function(data) {
            return data.kd_model
        }
    };
    $scope.jabatan = {
        minimumInputLength: 3,
        allowClear: true,
        initSelection: function(el, fn) {
        },
        ajax: {
            url: "api/web/bom/jabatan/",
            dataType: 'json',
            data: function(term) {
                return {
                    kata: term,
                };
            },
            results: function(data, page) {
                return {
                    results: data.jabatan,
                };
            }
        },
        formatResult: function(object) {
            return object.jabatan;
        },
        formatSelection: function(object) {
            return object.jabatan;
        },
        id: function(data) {
            return data.id_jabatan
        }
    };
    $scope.barang = {
        minimumInputLength: 3,
        allowClear: true,
        initSelection: function(el, fn) {
        },
        ajax: {
            url: "api/web/bom/barang/",
            dataType: 'json',
            data: function(term) {
                return {
                    kata: term,
                };
            },
            results: function(data, page) {
                return {
                    results: data.barang,
                };
            }
        },
        formatResult: function(object) {
            return object.nm_barang;
        },
        formatSelection: function(object) {
            return object.nm_barang;
        },
        id: function(data) {
            return data.kd_barang;
        }
    };
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
    ];
    $scope.addDetail = function() {
        var newDet = {
            bagian: '',
            kd_barang: '',
            nama_barang: '',
            qty: '',
            keterangan: '',
        }
        $scope.detBom.push(newDet);
    };
    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detBom);
        if (comArr.length > 1) {
            $scope.detBom.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
    $scope.getTipe = function(merk) {
        Data.get('bom/tipe/?merk=' + merk).then(function(data) {
            $scope.tipe = data.data;
        });
    };
    $scope.getchassis = function(merk, tipe) {
        Data.get('bom/chassis/?merk=' + merk + '&tipe=' + tipe).then(function(data) {
            $scope.form.kd_chassis = data.kode;
        });
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
        $scope.merk_ken = 'Merk';
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
//        var cekFoto = form.filefoto.length();
//        if (cekFoto > 0) {
//            form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
//            $scope.uploader.uploadAll();
//        }
        form.model = form.kd_model;
        detail.kd_jab = detail.kd_jab;
        detail.kd_barang = detail.kd_barang;
        $scope.form.model = form.kd_model;
        var data = {
            bom: form,
            detailBom: detail,
        };
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
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }

        $scope.detBom = {};
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
