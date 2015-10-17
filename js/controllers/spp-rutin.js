app.controller('sppRutinCtrl', function ($scope, Data, toaster, $modal, keyboardManager) {
    //init data
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.sppDet = [];
    $scope.openedDet = -1;
    $scope.tanggal = [];

    $scope.pilih = {};

    $scope.lock = function () {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('spprutin/lock/', $scope.pilih).then(function (result) {
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

    $scope.unlock = function () {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('spprutin/unlock/', $scope.pilih).then(function (result) {
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
    $scope.setStatus = function () {
        $scope.openedDet = -1;
    };
    $scope.openDet = function ($event, $index) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedDet = $index;
    };
    $scope.requiredPurchase = function () {
        Data.get('spprutin/requiredpurchase').then(function (data) {
            $scope.sppDet = data.data;
        });
    };
    $scope.isiTanggal = function (tanggal) {
        angular.forEach($scope.sppDet, function ($value, $key) {
            if ($value.centang == true) {
                $value.p = new Date(tanggal);

                $value.centang = false;
                $scope.form.p = "";
            }
        })
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
        Data.get('spprutin', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.excel = function (no_spp) {
        Data.get('spprutin/view/' + no_spp).then(function (data) {
            window.location = 'api/web/spprutin/print';
        });
    }

    $scope.print = function (no_spp) {
        Data.get('spprutin/view/' + no_spp).then(function (data) {
            window.open('api/web/spprutin/print?printlap=true');
        });
    }

    $scope.create = function () {
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.form.tgl_trans = new Date();
        $scope.requiredPurchase();
        Data.get('spprutin/kode').then(function (data) {
            $scope.form.no_spp = data.kode;
        });
    };

    $scope.update = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.no_spp;
        $scope.form = form;
        $scope.form.tgl_trans = new Date(form.tgl_trans);
        var start = new Date(form.tgl1);
        var end = new Date(form.tgl2);
        $scope.form.periode = {startDate: start, endDate: end};
        $scope.getDetail(form.no_spp);
    };

    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_spp;
        $scope.form = form;
        $scope.form.tgl_trans = new Date(form.tgl_trans);
        var start = new Date(form.tgl1);
        var end = new Date(form.tgl2);
        $scope.form.periode = {startDate: start, endDate: end};
        $scope.getDetail(form.no_spp);
    };

    keyboardManager.bind('ctrl+p', function () {
        if ($scope.is_edit == true && $scope.is_view == false) {
            $scope.modalTanggal($scope.sppDet);
        }
    });

    $scope.save = function (form, details) {
        var data = {
            form: form,
            details: details
        };
        var url = 'spprutin/update/';
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
        Data.delete('spprutin/delete/' + row.no_spp).then(function (result) {
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
            no_wo: '',
        }
        $scope.setStatus();
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
        Data.get('spprutin/detail/' + id).then(function (data) {
            $scope.sppDet = data.details;
        });
    };

    $scope.modal = function (form) {
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_spp-rutin/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            resolve: {
                form: function () {
                    return form;
                }
            }
        });
    };

    $scope.modalTanggal = function (form) {
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_spp-rutin/modalTanggal.html',
            controller: 'modalTanggalCtrl',
            size: 'md',
            resolve: {
                form: function () {
                    return form;
                }
            }
        });
    };

    $scope.cekAll = function () {
        angular.forEach($scope.sppDet, function ($value, $key) {
            if ($scope.cek == true) {
                $value.centang = true;
            } else {
                $value.centang = false;
            }
        })
    };

    $scope.chckedIndexs = [];
    $scope.checkedIndex = function (detail) {
        if ($scope.chckedIndexs.indexOf(detail) === -1) {
            $scope.chckedIndexs.push(detail);
        }
        else {
            $scope.chckedIndexs.splice($scope.chckedIndexs.indexOf(detail), 1);
        }
    }

    keyboardManager.bind('ctrl+s', function () {
        if ($scope.is_edit == true && $scope.is_view == false) {
            $scope.save($scope.form, $scope.sppDet);
        }
    });

    keyboardManager.bind('delete', function () {
        if ($scope.is_edit == true && $scope.is_view == false) {
            angular.forEach($scope.chckedIndexs, function (value, index) {
                var index = $scope.sppDet.indexOf(value);
                $scope.sppDet.splice($scope.sppDet.indexOf(value), 1);
            });
            $scope.chckedIndexs = [];
        }
    });

});

app.controller('modalCtrl', function ($scope, Data, $modalInstance, form) {

    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };

    $scope.open2 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };

    $scope.formmodal = form;
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});

app.controller('modalTanggalCtrl', function ($scope, Data, $modalInstance, form) {
    $scope.open2 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };

    $scope.detail = form;

    $scope.isiTanggal = function (tanggal) {
        angular.forEach($scope.detail, function ($value, $key) {
            if ($value.centang == true) {
                $value.p = new Date(tanggal);
                $value.centang = false;
            }
        })
    };

    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});
