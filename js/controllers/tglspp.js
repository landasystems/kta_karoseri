app.controller('tglsppCtrl', function($scope, Data, toaster, $modal) {

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
    $scope.asu = {};

    $scope.open1 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.cariWo = function($query) {
        if ($query.length >= 3) {
            Data.get('spprutin/cari', {nama: $query}).then(function(data) {
                $scope.list_spp = data.data;
            });
        }
    };

    $scope.pilih = function(form, $item) {
        Data.post('spprutin/getdetail/', form).then(function(data) {
           $scope.detSpp = data.details;

        });
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
        Data.get('wip', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };
    $scope.excel = function() {
        Data.get('wip', paramRef).then(function(data) {
            window.location = 'api/web/wip/excel';
        });
    }
    $scope.create = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.asu = {};
        $scope.detSpp = {};
        $scope.form.a = new Date();
    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form);
        $scope.form = {};
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form.id);
    };
    $scope.save = function(form, asu) {
        var data = {
            wip: form,
            asu: asu,
        };
        var url = 'spprutin/updatetgl/';
        Data.post(url, data).then(function(result) {
             console.log(result);
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
                $scope.detSpp = result.details;
               
            }
        });
    };
    $scope.cancel = function() {
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function(row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.post('womasuk/delete/', row).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.setStatus = function() {
        $scope.openedDet = -1;
    };
    $scope.addDetail = function() {
        var newDet = {
            id: 0,
            no_wo: '',
            kd_kerja: '',
            plan_start: '',
            plan_finish: '',
            act_start: '',
            act_finish: '',
            keterangan: '',
        }
        $scope.setStatus();
        $scope.detWip.unshift(newDet);
    };
    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detWip);
        if (comArr.length > 1) {
            $scope.detWip.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
    $scope.modal = function(form) {
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_w-inprogress/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            resolve: {
                form: function() {
                    return form;
                }
            }
        });
    };
    $scope.selected = function(form) {
        Data.post('womasuk/select/', form).then(function(data) {
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

                                                                                      