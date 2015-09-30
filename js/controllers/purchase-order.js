app.controller('poCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;

    $scope.displayed = [];
    $scope.form = {};
    $scope.is_edit = false;
    $scope.is_print = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.gantiStatus = {};
    $scope.msg = '';

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


    $scope.bukaPrint = function (form) {
//        console.log(form);
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('po/bukaprint/', {nota: form}).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan");
                } else {
                    toaster.pop('success', "Berhasil", "Data Berhasil Terproses");
                }
            });
        }
    }

    $scope.updt_st = function ($id) {
        Data.get('po/updtst/' + $id).then(function (data) {
            $scope.form.status = 1;
        });
    }

    $scope.cariSpp = function ($query) {

        if ($query.length >= 3) {
            Data.get('spprutin/cari', {nama: $query}).then(function (data) {
                $scope.resultsspp = data.data;
            });
        }
    }
    $scope.cariSuppiler = function ($query) {

        if ($query.length >= 3) {
            Data.get('supplier/cari', {nama: $query}).then(function (data) {
                $scope.resultssupplier = data.data;
            });
        }
    }

    $scope.cariBarang = function ($query1, $query2) {
        if (typeof $scope.form.listspp != "undefined") {
            Data.get('po/brgspp', {namabrg: $query1, nospp: $query2}).then(function (data) {
                $scope.resultsbrg = data.data;
            });
        } else if ($query1.length >= 3) {
            Data.get('po/brgspp', {namabrg: $query1, nospp: $query2}).then(function (data) {
                $scope.resultsbrg = data.data;
            });
        }
//        console.log($scope.resultsbrg);
    }

    $scope.pilih = function (detail, $item) {
        detail.harga = $item.harga;
        detail.jml = $item.jml;
        detail.satuan = $item.satuan;
        $scope.subtotal();
    }
    $scope.pilihspp = function (detsPo, $item) {
        Data.get('po/cari', {nama: $item}).then(function (data) {
            detsPo = data.data;
        });
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



    $scope.addDetail = function () {
        var newDet = {
            nota: '',
            kode_barang: '',
            jml: '',
            harga: '',
            diterima: '',
            ket: '',
            tgl_pengiriman: ''
        }
        $scope.detsPo.unshift(newDet);

    };

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

    var satuan = {0: "nol", 1: "satu", 2: "dua", 3: "tiga", 4: "empat", 5: "lima", 6: "enam", 7: "tujuh", 8: "delapan", 9: "sembilan"};
    var belasan = {10: "sepuluh", 11: "sebelas", 12: "dua belas", 13: "tiga belas", 14: "empat belas", 15: "lima belas", 16: "enam belas", 17: "tujuh belas", 18: "delapan belas", 19: "sembilan belas"};
    var puluhan = {2: "dua puluh", 3: "tiga puluh", 4: "empat puluh", 5: "lima puluh", 6: "enam puluh", 7: "tujuh puluh", 8: "delapan puluh", 9: "sembilan puluh"};
    var sekala = [
        {name: "sptiliun", size: 24},
        {name: "sextiliun", size: 21},
        {name: "quintiliun", size: 18},
        {name: "quadriliun", size: 15},
        {name: "triliun", size: 12},
        {name: "milyar", size: 9},
        {name: "juta", size: 6},
        {name: "ribu", size: 3},
        {name: "ratus", size: 2}
    ];

    $scope.keKata = function (num) {
        var parts = [], minusStr = "";
        var satuantr = num.toString();

        if (satuantr.length < 1) {
            return "";
        }

        if (satuantr[0] == " ") {
            minusStr = "minus ";
            satuantr = satuantr.substring(1);
        }

        for (var i = 0; i < sekala.length; i++) {
            var scale = sekala[i];
            if (satuantr.length > scale.size) {
                var mag = satuantr.length - scale.size;
                parts.push($scope.keKata(satuantr.substring(0, mag)) + " " + scale.name);
                satuantr = satuantr.substring(mag).replace(/^[0]+/, '');
            }
        }

        num = parseInt(satuantr, 10);

        if (num >= 20) {
            var puluhantr = puluhan[Math.floor(num / 10)];
            if (num % 10 !== 0) {
                puluhantr += " " + satuan[num % 10];
            }
            parts.push(puluhantr);
        } else if (num >= 10) {
            parts.push(belasan[num]);
        } else if (num >= 0) {
            parts.push(satuan[num]);
        }

        var lastPart = parts.pop();
        return minusStr + (parts.length > 0 ? parts.join(", ") + "  " : "") + lastPart;
    }

//button
    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_print = false;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        $scope.detsPo = [{}];
        Data.get('po/kode').then(function (data) {
            $scope.form.nota = data.kode;
        });
        $scope.form.tanggal = new Date();
        $scope.form.dp = '0';
//        $scope.form.diskon = 0;
        $scope.form.nilai_diskon = 0;
        $scope.form.status_po = '1';
        $scope.form.status_ppn = '0';
        $scope.form.ppn = '0';
        $scope.form.dikirim_ke = 'PT KARYA TUGAS ANDA';

    };


    $scope.update = function (row) {
        $scope.is_print = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + row.nota
        $scope.form.tanggal = new Date(row.tanggal);

        $scope.selected(row.nota);

    };

    $scope.view = function (row) {
        $scope.is_print = true;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + row.nota;
        $scope.form.tanggal = new Date(row.tanggal);
        $scope.selected(row.nota);

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
                $scope.view(result.data.nota);
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
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('po/delete/' + row.nota).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };

    $scope.selected = function (id) {
        Data.get('po/view/' + id).then(function (data) {
            $scope.form = data.data;
            $scope.status = data.print;
            $scope.msg = data.msg;
            $scope.form.terbilang = $scope.keKata(data.data.total_dibayar) + ' RUPIAH';
            $scope.detsPo = [];
            angular.forEach(data.detail, function ($value, $key) {
                $scope.detsPo.push($value);
                $scope.detsPo[$key]['data_barang']['tgl_pengiriman'] = new Date($value.tgl_pengiriman);
            })

            $scope.form.dp = (data.data.dp == undefined) ? '0' : data.data.dp;
            $scope.form.ppn = (data.data.ppn == undefined) ? '0' : data.data.ppn;
            $scope.form.bayar = (data.data.bayar == '1') ? '1' : '0';
            $scope.form.diskon = (data.data.diskon == undefined) ? '0' : data.data.diskon;
            $scope.form.status_po = (data.data.spp == '-') ? '0' : '1';
            $scope.form.status_ppn = (data.data.ppn == '0') ? '1' : '0';
            $scope.form.jatuh_tempo = (data.data.jatuh_tempo == undefined) ? '0' : data.data.jatuh_tempo;
            $scope.form.nilai_diskon = (data.data.diskon != undefined) ? ((data.data.diskon / data.data.total) * 100) : '0';
            $scope.subtotal();
        });

    }


})
