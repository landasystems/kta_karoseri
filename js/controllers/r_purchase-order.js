app.controller('returpoCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    
    $scope.displayed = [];
    $scope.paginations = 0;
    $scope.form = {};
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
        paramRef = param;
        Data.get('po/rekap', param).then(function (data) {
            $scope.displayed = data.data;
            $scope.displayedPrint = data.dataPrint;
            $scope.paginations = data.totalItems;
            if(data.totalItems != 0) {
                tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
            }
        });

        $scope.isLoading = false;
    };
    $scope.excel = function () {
        Data.get('po/rekap', paramRef).then(function (data) {
            window.location = 'api/web/po/excel';
        });
    }

//    Data.get('po/listsupplier').then(function (data) {
//        $scope.listsupplier = data.data;
//    });
//    Data.get('po/listbarang').then(function (data) {
//        $scope.listbarang = data.data;
//    });


    $scope.updt_st = function ($id) {
        Data.get('po/updtst/'+$id).then(function (data) {
        });
    }

    $scope.cariSpp = function ($query) {

        if ($query.length >= 3) {
            Data.get('spp/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }
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

        //diskon
        var diskon = $scope.form.nilai_diskon;
        var nilai_diskon = ((diskon / 100) * total);

        //ppn
        if ($scope.form.status_ppn == "0") {
            var nilai_ppn = ((10 / 100) * total);
        } else {
            var nilai_ppn = 0;
        }
        var total_dp = $scope.form.dp;
        var total_seluruh = ((total - nilai_diskon) + nilai_ppn);
        var sisa_bayar = (total_seluruh - total_dp);

        $scope.form.ppn = Math.ceil(nilai_ppn);
        $scope.form.diskon = Math.ceil(nilai_diskon);
        $scope.form.total_dibayar = Math.ceil(total_seluruh);
        $scope.form.sisa_dibayar = Math.ceil(sisa_bayar);

    }



   

//detail
    $scope.detsPo = {
        nota: '',
        kode_barang: '',
        jml: '',
        harga: '',
        diterima: '0',
        ket: '',
        tgl_pengiriman: ''
    };

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


    $scope.setStatus = function () {
        $scope.openedDet = -1;
    };

    $scope.openDet = function ($event, $index) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.openedDet = $index;
    };

  
    $scope.view = function (nota) {

        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + nota;
        $scope.selected(nota);

    };

    $scope.save = function (form, detail) {
        var data = {
            formpo: form,
            details: detail,
        };
//        console.log(data);
        var url = ($scope.is_create == true) ? 'po/create' : 'po/update/' + form.nota;
        Data.post(url, data).then(function (result) {
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
            $scope.form.terbilang = $scope.keKata(data.data.total_dibayar) + ' RUPIAH';
            $scope.detsPo = data.detail;
            $scope.form.dp = (data.data.dp == undefined) ? '0' : data.data.dp;
            $scope.form.ppn = (data.data.ppn == undefined) ? '0' : data.data.ppn;
            $scope.form.bayar = (data.data.bayar == '1') ? '1' : '0';
            $scope.form.diskon = (data.data.diskon == undefined) ? '0' : data.data.diskon;
            $scope.form.status_po = (data.data.spp == '-') ? '0' : '1';
            $scope.form.status_ppn = (data.data.ppn == '0') ? '1' : '0';
            $scope.form.jatuh_tempo = (data.data.jatuh_tempo == undefined) ? '0' : data.data.jatuh_tempo;
            $scope.form.nilai_diskon = (data.data.diskon != undefined) ? ((data.data.diskon / data.data.total) * 100) : '0';

        });
        $scope.subtotal();
    }


})
