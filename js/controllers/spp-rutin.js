app.controller('sppRutinCtrl', function ($scope, Data, toaster, $modal) {
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.sppDet = [];
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
    $scope.requiredPurchase = function(form){
      Data.get('spprutin/requiredpurchase',form).then(function(data){
          $scope.sppDet.barang = data.data;
          var a = 1;
          for(i = 1;i <= data.count; i++){
              $scope.sppDet[i] = data.data[i];
              a++;
          }
//          $scope.sppDet.qty = data.data.qty;
//          $scope.sppDet.ket = data.data.ket;
          console.log($scope.sppDet);
      });
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

        Data.get('spprutin', param).then(function (data) {
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
        $scope.requiredPurchase(form);
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
        var url = (form.no_spp == undefined) ? 'spprutin/create' : 'spprutin/update/' + form.no_spp;
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
            Data.delete('spprutin/delete/' + row.no_spp).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
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
});

app.controller('modalCtrl', function ($scope, Data, $modalInstance, form) {

    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };
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

    $scope.formmodal = form;
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
});
