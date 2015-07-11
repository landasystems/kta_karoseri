app.controller('poCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;

    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;

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

        Data.get('po', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    Data.get('po/listsupplier').then(function (data) {
        $scope.listsupplier = data.data;
    });

    $scope.cariSuppiler = function ($query) {

        if ($query.length >= 3) {
            Data.get('supplier/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.cariBarang = function ($query) {

        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }


    $scope.addDetail = function () {
        var newDet = [{
                nota: '',
                kode_barang: '',
                jml: '',
                harga: '',
                diterima: '0',
                ket: '',
                tgl_pengiriman: ''
            }]
        $scope.detsPo.unshift(newDet);

    };

//detail
    $scope.detsPo = [
        {
            nota: '',
            kode_barang: '',
            jml: '',
            harga: '',
            diterima: '0',
            ket: '',
            tgl_pengiriman: ''
        }];

//remove
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detsPo);

        if (comArr.length > 1) {
            $scope.detsPo.splice(paramindex, 1);
            $scope.subtotal();
        } else {
            alert("Something gone wrong");
        }
    };

//datepicker
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


//button
    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('po/kode').then(function (data) {
            $scope.form.nota = data.kode;
        });
        $scope.form.tanggal = moment().format('DD-MM-YYYY');

    };
    $scope.update = function (form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no;
        $scope.form = form;
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no;
        $scope.form = form;
    };
    $scope.save = function (form) {
        var url = ($scope.is_create == true) ? 'po/create' : 'po/update/' + form.nota;
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                $scope.callServer(tableStateRef); //reload grid ulang
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
        });

    };
    $scope.cancel = function () {
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('po/delete/' + row.nota).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

    $scope.selected = function (id) {
        Data.get('po/view/' + id).then(function (data) {
            $scope.form = data.data;
            $scope.detsmasuk = data.detail;

        });
        $scope.subtotal();
    }


})
