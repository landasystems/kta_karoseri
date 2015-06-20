app.controller('jenisbrgCtrl', function ($scope, Data) {
    //init data
    var ctrl = this;
    ctrl.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

    this.callServer = function callServer(tableState) {
        ctrl.isLoading = true;
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

        Data.get('jenisbrg', param).then(function (data) {
            ctrl.displayed = data.data;
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });

        ctrl.isLoading = false;
    };

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('jenisbrg/kode').then(function(data) {
            $scope.form.kd_jenis = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false; 
        $scope.formtitle = "Edit Data : " + form.jenis_brg;
        $scope.form = form;
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.jenis_brg;
        $scope.form = form;
    };
    $scope.save = function (form) {
        $scope.is_edit = false;
        if ($scope.is_create == true) {
            Data.post('jenisbrg/create', form).then(function (result) {

            });
        } else {
            
            Data.post('jenisbrg/update/'+ form.kd_jenis, form).then(function (result) {

            });
        }
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
            Data.delete('jenisbrg/delete/' + row.kd_jenis).then(function (result) {
                ctrl.displayed.splice(ctrl.displayed.indexOf(row), 1);
            });
        }
    };


})
