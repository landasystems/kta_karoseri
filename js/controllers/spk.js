app.controller('spkCtrl', function($scope, Data, toaster) {

    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.detKerja = [{
            'kd_ker': '',
            'nm_kerja': '',
            'kd_jab': '',
            'jenis': ''
        }],
    $scope.addDetail = function() {
        var newDet = {
            kd_ker: '',
            nm_kerja: '',
            kd_jab: '',
            jenis: '',
        }
        $scope.detKerja.push(newDet);
    }
    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detKerja);
        if (comArr.length > 1) {
            $scope.detKerja.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };

    Data.get('spk/nowo').then(function(data) {
        $scope.sNowo = data.wo
    });
    $scope.getcustomer = function(wo) {
//        alert('asjdfhasjdfkas');
        Data.post('spk/customer/', wo).then(function(data) {
            $scope.sCustomer = data.customer;
            $scope.form.nm_customer = data.customer;
            $scope.form.model = data.model;
            $scope.form.jabatan = data.jabatan;

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

        Data.get('spk', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.detail = {};
//        Data.get('spk/kode').then(function(data) {
//            $scope.form.kode = data.kode;
//        });
    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
    };
    $scope.save = function(form, detail) {
        var data = {
            spk: form,
            detailSpk: detail,
        };
        var url = ($scope.is_create == true) ? 'supplier/create' : 'supplier/update/' + form.kd_chassis;
        Data.post(url, form).then(function(result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
        });

        //---------
//        $scope.is_edit = false;
//        if ($scope.is_create == true) {
//            Data.post('chassis/create', form).then(function(result) {
//
//
//            });
//        } else {
//
//            Data.post('chassis/update/' + form.kd_chassis, form).then(function(result) {
//
//            });
//        }
    };
    $scope.cancel = function() {
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('transSpk/delete/' + row.no_wo).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})
