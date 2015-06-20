app.controller('chassisCtrl', function ($scope, Data) {
    //init data
    var ctrl = this;
    ctrl.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;

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

        Data.get('chassis', param).then(function (data) {
            ctrl.displayed = data.data;
            tableState.pagination.numberOfPages = Math.round(data.totalItems / limit);
        });

        ctrl.isLoading = false;
    };

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false; 
        $scope.formtitle = "Edit Data : " + form.merk;
        $scope.form = form;
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.merk;
        $scope.form = form;
    };
    $scope.save = function (form) {
        $scope.is_edit = false;
        if (form.id > 0) {
            Data.post('chassis/update/'+ form.kd_chassis, form).then(function (result) {

            });
        } else {
            Data.post('chassis/create', form).then(function (result) {

            });
        }
    };
    $scope.cancel = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('chassis/delete/' + row.kd_chassis).then(function (result) {
                ctrl.displayed.splice(ctrl.displayed.indexOf(row), 1);
            });
        }
    };


})
