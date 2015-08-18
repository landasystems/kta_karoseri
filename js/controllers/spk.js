app.controller('spkCtrl', function($scope, Data, toaster) {

    //init data
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
//    $scope.detKerja = [];

    $scope.addDetail = function() {
        var newDet = {
            nm_kerja: '',
        }
        $scope.detKerja.unshift(newDet);
    }
    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detKerja);
        if (comArr.length > 1) {
            $scope.detKerja.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };

    $scope.cariProduk = function($query) {
        if ($query.length >= 3) {
            Data.get('ujimutu/cari', {nama: $query}).then(function(data) {
                $scope.results = data.data;
            });
        }
    }
    Data.post('spk/jabatan').then(function(data) {
        $scope.sJabatan = data.jabatan;
    });
    $scope.getjabatan = function(form) {

        Data.post('spk/kerja/', form.jabatan).then(function(data) {
            $scope.sKerja = data.kerja;
            $scope.detKerja = data.detail;
            console.log(data.detail);
//            $scope.detKerja = [{
//                nm_kerja: '',
//            }];
        });
    };

    $scope.pilih = function(form, $item) {
        $scope.form.merk = $item.merk;
        $scope.form.model = $item.model;
        $scope.form.nm_customer = $item.nm_customer;
        $scope.detKerja = [{
                nm_kerja: '',
            }];
        console.log($scope.detKerja);
//        Data.post('spk/customer/', $item).then(function(data) {
//            $scope.sJabatan = data.jabatan;
//            $scope.detKerja = data.detail;
//            $scope.sKerja = data.kerja;
//            $scope.form.jabatan = data.asu.spk.jabatan;
//
//        });
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
        Data.get('spk', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };
    $scope.excel = function() {
        Data.get('spk', paramRef).then(function(data) {
            window.location = 'api/web/spk/excel';
        });
    }

    $scope.create = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.detKerja = [];
    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
//        console.log($scsope.form);
        $scope.selected(form.id_spk);
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form.id_spk);

    };
    $scope.save = function(form, detail) {
        
        var data = {
            spk: form,
            detailSpk: detail,
        };
         var url = ($scope.is_create == true) ? 'spk/create' : 'spk/update/' + form.id_spk;
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
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('spk/delete/' + row.id_spk).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function(id_spk) {
        Data.get('spk/view/' + id_spk).then(function(data) {
            $scope.form = data.data;
            $scope.form.id_spk = id_spk;
            $scope.detKerja = data.detail;
            $scope.sJabatan = data.jabatan;

        });


    }
    $scope.tagTransform = function(newTag) {
        var item = {
            kd_ker: '',
            nm_kerja: newTag,
            kd_jab: '',
            jenis: '',
            no: '',
        };

        return item;
    };


    $scope.nambahIsi = function(detail, item) {
        detail = item;
    };



})
