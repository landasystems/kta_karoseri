app.controller('warnaCtrl', function($scope, Data, toaster) {
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_create = false;
    $scope.is_view = false;


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

        Data.get('warna', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function(form) {
        $scope.is_edit = true;
        $scope.is_create = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
         Data.get('warna/kode').then(function (data) {
            $scope.form.kd_warna= data.kode;
        });
    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_create = false;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.warna;
        $scope.form = form;
        $scope.form.password = '';
    };
    $scope.view = function(form) {
      
        $scope.is_edit = true;
        $scope.is_create = false;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.nama_proyek;
        $scope.form = form;
        $scope.form.password = '';
    };
    $scope.save = function(form) {
        var url = (form.id > 0) ? 'warna/update/' + form.id : 'warna/create';
        Data.post(url, form).then(function(result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
            }
        });
    };
    $scope.cancel = function() {
        if (!$scope.is_view){ //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.is_edit = false;
        $scope.is_view = false;
    };

  
    $scope.delete = function(row) {
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('warna/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})
