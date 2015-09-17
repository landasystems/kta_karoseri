app.controller('rubahbentukCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

    $scope.cariWo = function ($query) {
        if ($query.length >= 3) {
            Data.get('wo/wospk', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }

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
        Data.get('rubahbentuk', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.printLapPembuatan = function () {
        Data.get('rubahbentuk', paramRef).then(function (data) {
            window.open('api/web/rubahbentuk/excel?print=true', "", "width=500");
        });
    }

    $scope.exportLapPembuatan = function () {
        Data.get('rubahbentuk', paramRef).then(function (data) {
            window.location = 'api/web/rubahbentuk/excel';
        });
    }

    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.pengajuan = new Date();
        $scope.form.terima = new Date();
        $scope.form.tgl = new Date();
    };

    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.kd_rubah;
        $scope.form = form;
        $scope.selected(form.no_wo);
        $scope.form.tgl = new Date(form.tgl);
        $scope.form.pengajuan = new Date(form.pengajuan);
        $scope.form.terima = new Date(form.terima);
    };

    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.kd_rubah;
        $scope.form = form;
        $scope.selected(form.no_wo);
    };

    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'rubahbentuk/create' : 'rubahbentuk/update/' + form.id;
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.callServer(tableStateRef); //reload grid ulang
                $scope.is_edit = false;
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
                $scope.form = {};
            }
        });
    };

    $scope.cancel = function () {
        $scope.form.no_wo = "";
        $scope.callServer(tableStateRef);
        $scope.is_edit = false;
        $scope.is_view = false;
    };

    $scope.delete = function (row) {
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('rubahbentuk/delete/' + row.id).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

    $scope.selected = function ($query) {
        Data.get('wo/wospk', {nama: $query}).then(function (data) {
            $scope.form.no_wo = data.data[0];
        });
    }

})
