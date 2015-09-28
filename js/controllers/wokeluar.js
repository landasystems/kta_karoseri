app.controller('wokeluarCtrl', function($scope, Data, toaster) {
  
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_create = false;

    $scope.open1 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
     $scope.cariNowo = function($query) {
        if ($query.length >= 3) {
            Data.get('wokeluar/nowo', {nama: $query}).then(function(data) {
                $scope.list_nowo = data.data;
            });
        }
    };$scope.getnowo = function (wo) {
        Data.post('wokeluar/getnowo/', wo).then(function (data) {
            $scope.form = data.nowo;

        });
    };

    $scope.pilih = function(form, $item) {
        Data.post('wokeluar/getnowo/', $item).then(function (data) {
            $scope.form = data.data;
//               $scope.list_nowo = data.data;
               console.log(data);

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

        Data.get('wokeluar', param).then(function(data) {
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
         $scope.eks = {};
        $scope.inter = {};

    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_wo;
        $scope.form = form;
         $scope.form.tgl_keluar = new Date(form.tgl_keluar);
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
    $scope.save = function(form) {
       
        var url =  'wokeluar/update/';
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
//        alert(row);
        if (confirm("apakah anda yakin ?")) {
            Data.post('wokeluar/delete/',row).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
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
            console.log(data.data);
            

        });
    }


})
                                                                                      