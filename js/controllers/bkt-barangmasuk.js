app.controller('bbmCtrl', function ($scope, Data, toaster) {
    //init data
    
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.detBbm = [];
    $scope.openedDet = -1;
//    Data.get('bstk/nowo').then(function (data) {
//        $scope.list_wo = data.list_wo;
//    });
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.setStatus = function () {
        $scope.openedDet = -1;
    };
    $scope.openDet = function ($event, $index) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedDet = $index;
    };
    $scope.cariWo = function ($query) {
        if ($query.length >= 3) {
            Data.get('wo/cari', {no_wo: $query}).then(function (data) {
                $scope.listWo = data.data;
            });
        }
    };
    $scope.cariPo = function ($query) {
        if ($query.length >= 3) {
            Data.get('po/cari', {nama: $query}).then(function (data) {
                $scope.listPo = data.data;
            });
        }
    };
    $scope.cariSupplier = function ($query) {
        if ($query.length >= 3) {
            Data.get('supplier/cari', {nama: $query}).then(function (data) {
                $scope.listSupplier = data.data;
            });
        }
    };
    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
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

        Data.get('bbm', param).then(function (data) {
            $scope.displayed = data.data;
//            $scope.displayed.tgl_terima = new Date(data.data.tgl_terima);
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.detBbm = [{
                id: '',
                no_bbm: '',
                barang: [
                    
                ],
                jumlah: '',
                keterangan: '',
                tgl_terima: '',
                no_po: '',
            }];
    };
    $scope.update = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.no_bbm;
        $scope.form = form;
        $scope.getDetail(form.no_bbm);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_bbm;
        $scope.form = form;
        $scope.getDetail(form.no_bbm);
    };
    $scope.save = function (form, detBbm) {
        var data = {
            form: form,
            detBbm: detBbm
        };
        var url = (form.no_bbm !== undefined) ? 'bbm/update/'+form.no_bbm : 'bbm/create';
        Data.post(url, data).then(function (result) {
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

    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('spp/delete/' + row.no_spp).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.addDetail = function () {
        var newDet = {
            id: '0',
            kd_barang: '',
            jumlah: '',
            tgl_terima: '',
            no_wo: '',
        }
        $scope.setStatus();
        $scope.detBbm.unshift(newDet);
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detBbm);
        if (comArr.length > 1) {
            $scope.detBbm.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
    $scope.getDetail = function (id) {
        Data.get('bbm/view/' + id).then(function (data) {
            $scope.detBbm = data.details;
//            $scope.detBbm.barang = data.details;
            $scope.form.wo = data.wo;
            $scope.form.supplier = data.sup;
        });
    }
});
