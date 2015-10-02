app.controller('sppNonRutinCtrl', function ($scope, Data, toaster, $modal) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.sppDet = [];
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
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
        paramRef = param;
        Data.get('sppnonrutin', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function (no_spp) {
        Data.get('sppnonrutin/view/' + no_spp).then(function (data) {
            window.location = 'api/web/sppnonrutin/print';
        });
    }

    $scope.print = function (no_spp) {
        Data.get('sppnonrutin/view/' + no_spp).then(function (data) {
            window.open('api/web/sppnonrutin/print?printlap=true');
        });
    }

    $scope.create = function (form) {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tgl_trans = new Date();
        Data.get('sppnonrutin/kode').then(function (data) {
            $scope.form.no_spp = data.kode;
        });
        $scope.sppDet = [{
                id: '',
                no_spp: '',
                kd_barang: '',
                saldo: '',
                qty: '',
                ket: '',
                p: '',
                a: '',
                stat_spp: '',
                no_wo: '',
            }];
    };
    $scope.update = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.no_spp;
        $scope.form = form;
        var start = new Date(form.tgl1);
        var end = new Date(form.tgl2);
        $scope.form.periode = {startDate: start, endDate: end};
        $scope.getDetail(form.no_spp);
        $scope.form.tgl_trans = new Date(form.tgl_trans);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_spp;
        $scope.form = form;
        var start = new Date(form.tgl1);
        var end = new Date(form.tgl2);
        $scope.form.periode = {startDate: start, endDate: end};
        $scope.getDetail(form.no_spp);
    };
    $scope.save = function (form, details) {
        var data = {
            form: form,
            details: details
        };
        var url = (form.no_spp == undefined) ? 'sppnonrutin/create' : 'sppnonrutin/update/' + form.no_spp;
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
        $scope.is_create = false;
        $scope.is_edit = false;
        $scope.is_view = false;
    };

    $scope.delete = function (row) {
        Data.delete('sppnonrutin/delete/' + row.no_spp).then(function (result) {
            $scope.displayed.splice($scope.displayed.indexOf(row), 1);
        });
    };
    $scope.addDetail = function () {
        var newDet = {
            id: 0,
            no_spp: '',
            kd_barang: '',
            saldo: '',
            qty: '',
            ket: '',
            p: '',
            a: '',
            stat_spp: '',
            no_wo: [],
        }
        $scope.sppDet.unshift(newDet);
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.sppDet);
        if (comArr.length > 1) {
            $scope.sppDet.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
    $scope.getDetail = function (id) {
        Data.get('sppnonrutin/detail/' + id).then(function (data) {
            $scope.sppDet = data.details;
        });
    };
    $scope.modal = function (sppDet, detail) {
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_spp-nonrutin/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            resolve: {
                form: function () {
                    var data = {
                        sppDet: sppDet,
                        detail: detail,
                        is_create: $scope.is_create,
                    };
                    return data;

                }
            }
        });
    };
});

app.controller('modalCtrl', function ($scope, Data, $modalInstance, form) {

    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };
    $scope.listWo = [];
    $scope.cariWo = function ($query) {
        if ($query.length >= 3) {
            Data.get('wo/cari', {no_wo: $query}).then(function (data) {
                $scope.listWo = data.data;
            });
        }
    };

    $scope.open2 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };

    $scope.formmodal = form.detail;
    $scope.is_create = form.is_create;
    if($scope.is_create == true){
        $scope.formmodal.p = new Date();
    }else{
        $scope.formmodal.p = new Date($scope.formmodal.p);
    }
//    $scope.woMasuk = [];
//    $scope.woMasuk = form.no_wo;
//    $scope.woSelected = function (formmodal, woMasuk, items) {
//        var mongo = form.sppDet;
//        var index = mongo.indexOf(form.detail);
//        var data = {
//            barang: formmodal.barang,
//            qty: formmodal.qty,
//            ket: formmodal.ket,
//            p: formmodal.p,
//            no_wo: items.no_wo
//        };
//        if (woMasuk.length == 1) {
//            mongo[index] = data;
//        } else {
//            mongo.unshift(data);
//        }
//    };
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});
