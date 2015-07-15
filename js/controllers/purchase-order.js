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

    $scope.pilih = function (detail, $item) {
        detail.harga = $item.harga;
        detail.satuan = $item.satuan;

    }

    $scope.subtotal = function () {
        var total = 0;
        var sub_total = 0;
        
        angular.forEach($scope.detsPo, function (detail) {
            var jml = (detail.jml) ? parseInt(detail.jml) : 0;
            var hrg = (detail.harga) ? parseInt(detail.harga) : 0;
            sub_total = (jml * hrg);
            detail.jumlah = sub_total;
            total += sub_total;
        })
        $scope.form.total = total;
        
        var diskon = $scope.form.diskon;
        var nilai_diskon = ((diskon / 100) * total);
//        console.log(nilai_diskon);
        $scope.form.hasil_diskon = total - nilai_diskon;
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
        $scope.detsPo = [{}]
        Data.get('po/kode').then(function (data) {
            $scope.form.nota = data.kode;
        });
        $scope.form.tanggal = moment().format('DD-MM-YYYY');
        $scope.form.status_po = '1';
        $scope.form.status_ppn = '0';
        $scope.form.ppn = '0';

    };
    $scope.update = function (nota) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + $scope.form.nota;
        $scope.form = form;
        $scope.form.status_po = ($scope.form.spp == '-') ? '0' : '1';
        $scope.form.status_ppn = ($scope.form.ppn == '0') ? '1' : '0';
        
//        console.log(form);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no;
        $scope.form = form;
        $scope.form.status_po = ($scope.form.spp == '-') ? '0' : '1';
        $scope.form.status_ppn = ($scope.form.ppn == '0') ? '1' : '0';
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
