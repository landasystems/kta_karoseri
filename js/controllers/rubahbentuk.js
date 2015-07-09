app.controller('rubahbentukCtrl', function($scope, Data, toaster) {
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

    $scope.wo = {
        minimumInputLength: 3,
        allowClear: false,
        ajax: {
            url: "api/web/rubahbentuk/listwo/",
            dataType: 'json',
            data: function(term) {
                return {
                    kata: term,
                };
            },
            results: function(data, page) {
                return {
                    results: data.data
                };
            }
        },
        formatResult: function(object) {
            return object.no_wo;
        },
        formatSelection: function(object) {
            return object.no_wo;
        },
        id: function(data) {
            return data.no_wo;
        },
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

        Data.get('rubahbentuk', param).then(function(data) {
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
    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.merk;
        $scope.form = form;
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.merk;
        $scope.form = form;
    };
    $scope.save = function(form) {
        var url = ($scope.is_create == true) ? 'chassis/create' : 'chassis/update/' + form.kd_chassis;
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
            Data.delete('chassis/delete/' + row.kd_chassis).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})
