app.controller('wipCtrl', function ($scope, Data, toaster, $modal) {

    //init data
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_create = false;
    $scope.detWip = [];
    $scope.form = {};

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.cariWo = function ($query) {
        if ($query.length >= 3) {
            Data.get('wip/cari', {no_wo: $query}).then(function (data) {
                $scope.listWo = data.data;
            });
        }
    };

    $scope.pilih = function (form, $item) {
        Data.post('wip/getnowo/', $item).then(function (data) {
            console.log(data);
            var newDet = [{
                    id: 0,
                    no_wo: '',
                    kd_kerja: '',
                    plan_start: '',
                    plan_finish: '',
                    act_start: '',
                    act_finish: '',
                    ket: '',
                }];
            $scope.detWip = (data.detail != null) ? data.detail : newDet;
            form.umur = data.umur;

        });

        form.tgl_terima = $item.tgl_terima;
        form.jenis = $item.jenis;
        form.model = $item.model;
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
        Data.get('wip', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };
    $scope.excel = function () {
        Data.get('wip', paramRef).then(function (data) {
            window.location = 'api/web/wip/excel';
        });
    }
    $scope.print = function () {
        Data.get('wip', paramRef).then(function (data) {
            window.open('api/web/wip/excel?print=true', "", "width=500");
        });
    }
    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.detWip = {};
    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form);
        $scope.form = {};
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form.id);
    };
    $scope.save = function (form, detWip) {
        var data = {
            wip: form,
            detWip: detWip,
        };
        var url = 'wip/update/';
        Data.post(url, data).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
        });
    };
    $scope.cancel = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function (row) {
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.post('womasuk/delete/', row).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.setStatus = function () {
        $scope.openedDet = -1;
    };
    $scope.addDetail = function () {
        var newDet = {
            id: 0,
            no_wo: '',
            kd_kerja: '',
            plan_start: '',
            plan_finish: '',
            act_start: '',
            act_finish: '',
            ket: '',
        }
        $scope.setStatus();
        $scope.detWip.unshift(newDet);
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detWip);
        if (comArr.length > 1) {
            $scope.detWip.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
    $scope.modal = function (form) {
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_w-inprogress/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            resolve: {
                form: function () {
                    return form;
                }
            }
        });
    };
    $scope.selected = function (form) {
        Data.post('womasuk/select/', form).then(function (data) {
            $scope.form = data.data;
            $scope.eks = data.eksterior[0];
            $scope.inter = data.interior[0];
            $scope.form.warna = data.det.warna;
            $scope.form.no_spk = '234';
            $scope.form.customer = data.det.customer;
            $scope.form.sales = data.det.sales;
            $scope.form.pemilik = data.det.pemilik;
            $scope.form.model_chassis = data.det.model_chassis;
            $scope.form.merk = data.det.merk;
            $scope.form.tipe = data.det.tipe;
            $scope.form.model = data.det.model;
            $scope.form.no_rangka = data.det.no_rangka;
            $scope.form.no_mesin = data.det.no_mesin;
            $scope.form.jenis = data.det.jenis;
            $scope.form.jenis = data.det.jenis;
            $scope.form.no_spk = data.data.no_spk.as;
        });
    }


});
app.controller('modalCtrl', function ($scope, Data, $modalInstance, form) {

    $scope.cariProses = function ($query) {
        if ($query.length >= 3) {
            Data.get('wip/proses', {proses: $query}).then(function (data) {
                $scope.listproses = data.data;
            });
        }
    };
    $scope.cariPemborong = function ($query) {
        if ($query.length >= 3) {
            Data.get('wip/karyawan', {karyawan: $query}).then(function (data) {
                $scope.listkarywan = data.data;
            });
        }
    };

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.open2 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };
    $scope.open3 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened3 = true;
    };
    $scope.open4 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened4 = true;
    };

    $scope.formmodal = form;
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});

                                                                                      