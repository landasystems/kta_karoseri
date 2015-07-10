app.controller('claimunitCtrl', function($scope, Data, toaster) {
//init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.jenis_kmp = [];
    $scope.bagian = '-';

    $scope.open1 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };

    $scope.open2 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };
    $scope.kalkuasi = function() {
        $scope.form.total_biaya = (1 * $scope.form.biaya_spd) + (1 * $scope.form.biaya_tk) + (1 * $scope.form.biaya_mat);
    }

    $scope.jenisKmp = function(status, bagian) {
        Data.get('claimunit/jeniskomplain?status=' + status + '&bagian=' + bagian).then(function(data) {
            $scope.jenis_kmp = data.data;
        });
    }

    $scope.wo = {
        minimumInputLength: 3,
        allowClear: false,
        ajax: {
            url: "api/web/claimunit/listwo/",
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

        Data.get('claimunit', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
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
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
        $scope.jenisKmp(form.stat, form.bag)
        $scope.form.kd_jns = form.kd_jns;
        $scope.kalkuasi();
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.jenisKmp(form.stat, form.bag)
        $scope.form.kd_jns = form.kd_jns;
        $scope.kalkuasi();
    };
    $scope.save = function(form) {
        var url = ($scope.is_create == true) ? 'claimunit/create' : 'claimunit/update/' + form.id;
        Data.post(url, form).then(function(result) {
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
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('rubahbentuk/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.isJson = function(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

})
