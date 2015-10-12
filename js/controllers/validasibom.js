app.controller('validasibomCtrl', function ($scope, Data, toaster, $modal) {
    //init data;
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.form = {};

    $scope.view = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.gambar = (form.foto == null) ? [] : form.foto;
        $scope.formtitle = "Lihat Data : " + form.kd_bom;
        Data.get('chassis/tipe?merk=' + form.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected(form.kd_bom, '');
    };

    $scope.callServer = function callServer(tableState) {
        tableStateRef = tableState;
        $scope.isLoading = true;
        var offset = tableState.pagination.start || 0;
        var limit = tableState.pagination.number || 20;
        var param = {offset: offset, limit: limit};

        if (tableState.sort.predicate) {
            param['sort'] = tableState.sort.predicate;
            param['order'] = tableState.sort.reverse;
        }
        if (tableState.search.predicateObject) {
            param['filter'] = tableState.search.predicateObject;
        }
        paramRef = param;
        Data.get('validasibom', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    Data.get('chassis/merk').then(function (data) {
        $scope.listMerk = data.data;
    });

    $scope.printTrans = function (id) {
        Data.get('bom/view/' + id).then(function (data) {
            window.open('api/web/bom/exceltrans?print=true', "", "width=500");
        });
    }

    $scope.excelTrans = function (id) {
        Data.get('bom/view/' + id).then(function (data) {
            window.location = 'api/web/bom/exceltrans';
        });
    }

    $scope.typeChassis = function (merk) {
        Data.get('chassis/tipe?merk=' + merk).then(function (data) {
            $scope.listTipe = data.data;
        });
    };

    $scope.save = function (form) {
        if ($scope.is_edit == false) {
            var data = form;
        } else {
            var data = {
                bom: form,
            }
        }
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('validasibom/create/', data).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan");
                } else {
                    $scope.is_edit = false;
                    $scope.callServer(tableStateRef); //reload grid ulang
                    toaster.pop('success', "Berhasil", "Data Berhasil Terproses");
                }
            });
        }
    };

    $scope.cancel = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.form = {};
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
    };

    $scope.selected = function (id, kd_bom_baru) {
        Data.get('bom/view/' + id).then(function (data) {

            $scope.form = data.data;
            $scope.form.tgl_buat = new Date($scope.form.tgl_buat);
            if (kd_bom_baru != '') {
                $scope.form.kd_bom = kd_bom_baru;
                $scope.form.tgl_buat = new Date();
            }

            if (jQuery.isEmptyObject(data.detail)) {
                $scope.detBom = [
                    {
                        kd_jab: '',
                        kd_barang: '',
                        qty: '',
                        ket: '',
                    }
                ];
            } else {
                $scope.detBom = data.detail;
            }
        });
    }
})
