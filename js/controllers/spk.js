app.controller('spkCtrl', function ($scope, Data, toaster) {

    //init data
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.gantiStatus = {};
    $scope.msg = '';
//    $scope.detKerja = [];

    $scope.addDetail = function () {
        var newDet = {
            nm_kerja: '',
        }
        $scope.detKerja.unshift(newDet);
    }



    $scope.bukaPrint = function (form) {
//        console.log(form);
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('spk/bukaprint/', {id_spk: form}).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan");
                } else {
                    toaster.pop('success', "Berhasil", "Data Berhasil Terproses");
                }
            });
        }
    }

    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detKerja);
        if (comArr.length > 1) {
            $scope.detKerja.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
    $scope.updt_st = function ($id) {
        Data.get('spk/updtst/' + $id).then(function (data) {
//            $scope.callServer(tableStateRef);
        });
    }

    $scope.cariProduk = function ($query) {
        if ($query.length >= 3) {
            Data.get('ujimutu/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }
    $scope.cariOrang = function ($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/listkaryawan', {nama: $query}).then(function (data) {
                $scope.resultsKaryawan = data.data;
            });
        }
    }
    Data.post('spk/jabatan').then(function (data) {
        $scope.sJabatan = data.jabatan;
    });
    $scope.getjabatan = function (form) {

        Data.post('spk/kerja/', form).then(function (data) {
            $scope.sKerja = data.kerja;
            $scope.detKerja = data.detail;
//            console.log(data.detail);
//            $scope.detKerja = [{
//                nm_kerja: '',
//            }];
        });
    };

    $scope.pilih = function (form, $item) {
        console.log($item);
        $scope.form.merk = $item.merk;
        $scope.form.model = $item.model;
        $scope.form.nm_customer = $item.nm_customer;
        $scope.form.jenis = $item.jenis;
        $scope.detKerja = [{
                nm_kerja: '',
            }];
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
        Data.get('spk', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };
    $scope.excel = function () {
        Data.get('spk', paramRef).then(function (data) {
            window.location = 'api/web/spk/excel';
        });
    }

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.detKerja = [];
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
//        console.log($scsope.form);
        $scope.selected(form.id_spk);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form.id_spk);

    };
    $scope.save = function (form, detail) {
        if (confirm("Apakah anda yakin mengisi data tersebut ?")) {

            var data = {
                spk: form,
                detailSpk: detail,
            };
            var url = ($scope.is_create == true) ? 'spk/create' : 'spk/update/' + form.id_spk;
            Data.post(url, data).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan", result.errors);
                } else {
                    toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
                    if ($scope.is_create == true) {
                        var popupWin = window.open('', '_blank', 'width=1000,height=700');
                        var elem = document.getElementById('printArea');
                        popupWin.document.open()
                        popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="css/print.css" /></head><body onload="window.print();window.close();">' + elem.innerHTML + '</html>');
                        popupWin.document.close();
                    }
                    $scope.is_create = false;
                    $scope.is_edit = false;
                    $scope.create($scope.form);
                }
            });
        }
    };
    $scope.cancel = function () {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.form = {};
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function (row) {
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('spk/delete/' + row.id_spk).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function (id_spk) {
        Data.get('spk/view/' + id_spk).then(function (data) {
            console.log(data);
            $scope.form = data.data;
            $scope.form.id_spk = id_spk;
            $scope.detKerja = data.detail;
            $scope.sJabatan = data.jabatan;

        });


    }
    $scope.tagTransform = function (newTag) {
        var item = {
            kd_ker: '',
            nm_kerja: newTag,
            kd_jab: '',
            jenis: '',
            no: '',
        };

        return item;
    };


    $scope.nambahIsi = function (detail, item) {
        detail = item;
    };



})
