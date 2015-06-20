app.controller('barangCtrl', function($scope, Data) {
    //init data;
    var ctrl = this;
    ctrl.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

    Data.get('barang/jenis').then(function(data) {
        ctrl.jenis_brg = data.jenis_brg;
    });

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

        Data.get('barang', param).then(function(data) {
            ctrl.displayed = data.data;
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });

        ctrl.isLoading = false;
    };

    $scope.create = function(form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('barang/kode').then(function(data) {
            $scope.form.kd_barang = data.kode;
        });
    };
    $scope.update = function(form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.nm_barang;
        $scope.form = form;
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nm_barang;
        $scope.form = form;
    };
    $scope.save = function(form) {
//        $scope.is_edit = false;
        if ($scope.is_create == true) { 
            //skrip tambah
            Data.post('barang/create', form).then(function(result) {

            });
        } else {
            //skrip 
            Data.post('barang/update/' + form.kd_barang, form).then(function(result) {

            });
        }
    };
    $scope.cancel = function() {
        $scope.is_edit = false;
        $scope.is_view = false;
    };

//    $scope.trash = function(row) {
//        if (confirm("Apa anda yakin akan MENGHAPUS item ini ?")) {
////            row.is_deleted = 1;
//            Data.post('barang/update/' + row.id, row).then(function(result) {
//                ctrl.displayed.splice(ctrl.displayed.indexOf(row), 1);
//            });
//        }
//    };
//    $scope.restore = function(row) {
//        if (confirm("Apa anda yakin akan MERESTORE item ini ?")) {
////            row.is_deleted = 0;
//            Data.post('barang/update/' + row.id, row).then(function(result) {
//                ctrl.displayed.splice(ctrl.displayed.indexOf(row), 1);
//            });
//        }
//    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('barang/delete/' + row.kd_barang).then(function(result) {
                ctrl.displayed.splice(ctrl.displayed.indexOf(row), 1);
            });
        }
    };


})
