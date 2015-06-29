app.controller('customerCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

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

        Data.get('customer', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
//        Data.get('custmer/kode').then(function(data) {
//            $scope.form.kd_cust = data.kode;
//        });
    };
    $scope.update = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false; 
        $scope.formtitle = "Edit Data : " + form.kd_cust;
        $scope.form = form;
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.kd_cust;
        $scope.form = form;
    };
    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'customer/create' : 'customer/update/'+ form.kd_cust;
         Data.post(url, form).then(function (result) {   
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
        $scope.is_edit = false;
        $scope.is_view = false;
    };

//    $scope.trash = function (row) {
//        if (confirm("Apa anda yakin akan MENGHAPUS item ini ?")) {
//            row.is_deleted = 1;
//            Data.post('jenisbrg/update/' + row.id, row).then(function (result) {
//                ctrl.displayed.splice(ctrl.displayed.indexOf(row), 1);
//            });
//        }
//    };
//    $scope.restore = function (row) {
//        if (confirm("Apa anda yakin akan MERESTORE item ini ?")) {
//            row.is_deleted = 0;
//            Data.post('jenisbrg/update/' + row.id, row).then(function (result) {
//                ctrl.displayed.splice(ctrl.displayed.indexOf(row), 1);
//            });
//        }
//    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('customer/delete/' + row.kd_cust).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})