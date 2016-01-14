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

    $scope.lock = function (form) {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('po/lock/', {id: form}).then(function (result) {
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

    $scope.unlock = function (form) {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('po/unlock/', {id: form}).then(function (result) {
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

    $scope.bukaPrint = function (form) {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('po/bukaprint/', {nota: form}).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan");
                } else {
                    toaster.pop('success', "Berhasil", "Data Berhasil Terproses");
                }
            });
        }
    };

    $scope.updt_st = function ($id) {
        Data.get('po/updtst/' + $id).then(function (data) {
            $scope.form.status = 1;
        });
    };

    $scope.cariSpp = function ($query) {

        if ($query.length >= 3) {
            Data.get('spprutin/cari', {nama: $query}).then(function (data) {
                $scope.resultsspp = data.data;
            });
        }
    };
    $scope.cariSuppiler = function ($query) {

        if ($query.length >= 3) {
            Data.get('supplier/cari', {nama: $query}).then(function (data) {
                $scope.resultssupplier = data.data;
            });
        }
    };

    $scope.lis = function (status_po) {
        if (status_po == 0) {
            $scope.form.listspp = undefined;
        }
    }

    $scope.cariBarang = function ($query1, $query2, $query3) {
        if ($query3 != 0) {
            if ($query1.length >= 3) {
                Data.get('po/brgspp', {namabrg: $query1, nospp: $query2}).then(function (data) {
                    $scope.resultsbrg = data.data;
                });
            } else if (typeof $scope.form.listspp != "undefined") {
                Data.get('po/brgspp', {namabrg: $query1, nospp: $query2}).then(function (data) {
                    $scope.resultsbrg = data.data;
                });
            }
        } else {
            if ($query1.length >= 3) {
                Data.get('po/brgspp', {namabrg: $query1, nospp: ''}).then(function (data) {
                    $scope.resultsbrg = data.data;
                });
            }
        }
//        console.log($scope.resultsbrg);
    };

    $scope.pilih = function (detail, $item) {
        detail.harga = $item.harga;
        detail.jml = $item.jml;
        detail.satuan = $item.satuan;
        $scope.subtotal();
    };
    $scope.pilihspp = function (detsPo, $item) {
        Data.get('po/cari', {nama: $item}).then(function (data) {
            detsPo = data.data;
        });
    };
    $scope.subtotal = function () {
        var total = 0;
        var sub_total = 0;

        angular.forEach($scope.detsPo, function (detail) {
            var jml = (detail.jml) ? parseFloat(detail.jml) : 0;
            var hrg = (detail.harga) ? parseFloat(detail.harga) : 0;
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

    };



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


    ////
    var daftarAngka = new Array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan");


    $scope.terbilang = function (nilai) {
        var temp = '';

        var hasilBagi, sisaBagi;

        var batas = 3;

        var maxBagian = 5;

        var gradeNilai = new Array("", "ribu", "juta", "milyar", "triliun");

        nilai = $scope.hapusNolDiDepan(nilai);
        var nilaiTemp = $scope.ubahStringKeArray(batas, maxBagian, nilai);

        var j = nilai.length;

        var banyakBagian = (j % batas) == 0 ? (j / batas) : Math.round(j / batas + 0.5);
        var h = 0;
        for (var i = banyakBagian - 1; i >= 0; i--) {
            var nilaiSementara = parseInt(nilaiTemp[h]);
            if (nilaiSementara == 1 && i == 1) {
                temp += "seribu ";
            }
            else {
                temp += $scope.ubahRatusanKeHuruf(nilaiTemp[h]) + " ";

                if (nilaiTemp[h] != "000") {
                    temp += gradeNilai[i] + " ";
                }
            }
            h++;
        }
        return temp;
    }

    $scope.ubahStringKeArray = function (batas, maxBagian, kata) {

        var temp = new Array(maxBagian);
        var j = kata.length;

        var banyakBagian = (j % batas) == 0 ? (j / batas) : Math.round(j / batas + 0.5);
        for (var i = banyakBagian - 1; i >= 0; i--) {
            var k = j - batas;
            if (k < 0)
                k = 0;
            temp[i] = kata.substring(k, j);
            j = k;
            if (j == 0)
                break;
        }
        return temp;
    }

    $scope.ubahRatusanKeHuruf = function (nilai) {

        var batas = 2;

        var maxBagian = 2;
        var temp = $scope.ubahStringKeArray(batas, maxBagian, nilai);
        var j = nilai.length;
        var hasil = "";

        var banyakBagian = (j % batas) == 0 ? (j / batas) : Math.round(j / batas + 0.5);
        for (var i = 0; i < banyakBagian; i++) {

            if (temp[i].length > 1) {

                if (temp[i].charAt(0) == '1') {
                    if (temp[i].charAt(1) == '1') {
                        hasil += "sebelas";
                    }
                    else if (temp[i].charAt(1) == '0') {
                        hasil += "sepuluh";
                    }
                    else
                        hasil += daftarAngka[temp[i].charAt(1) - '0'] + " belas ";
                }

                else if (temp[i].charAt(0) == '0') {
                    hasil += daftarAngka[temp[i].charAt(1) - '0'];
                }

                else
                    hasil += daftarAngka[temp[i].charAt(0) - '0'] + " puluh " + daftarAngka[temp[i].charAt(1) - '0'];
            }
            else {

                if (i == 0 && banyakBagian != 1) {
                    if (temp[i].charAt(0) == '1')
                        hasil += " seratus ";
                    else if (temp[i].charAt(0) == '0')
                        hasil += " ";
                    else
                        hasil += daftarAngka[parseInt(temp[i])] + " ratus ";
                }

                else
                    hasil += daftarAngka[parseInt(temp[i])];
            }
        }
        return hasil;
    }
    $scope.hapusNolDiDepan = function (nilai) {
        while (nilai.indexOf("0") == 0) {
            nilai = nilai.substring(1, nilai.length);
        }
        return nilai;
    }

    $scope.getcode = function (dat){
        if($scope.is_edit == true){
            Data.get('po/kode', {nama: dat}).then(function (data) {
            $scope.form.nota = data.kode;
        });
        }
    }

//button
    $scope.create = function (form) {
        $scope.is_edit = true;
        $scope.is_print = false;
        $scope.is_view = false;
        $scope.lebar = "200px";
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
//        $scope.form.dikirim_ke = 'PT KARYA TUGAS ANDA';

    };


    $scope.update = function (row) {
        $scope.is_print = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.lebar = "102%";
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + row.nota
        $scope.form.tanggal = new Date(row.tanggal);

        $scope.selected(row.nota);

    };

    $scope.view = function (row) {
        $scope.is_print = true;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.lebar = "102%";
        $scope.formtitle = "Lihat Data : " + row.nota;
        $scope.form.tanggal = new Date(row.tanggal);
        $scope.selected(row.nota);

    };

    $scope.save = function (form, detail) {
        if (confirm("Apakah Anda yakin mengisi data tersebut ?")) {
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
                    $scope.view(result.data);
                    toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
                }
            });
        }

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
        Data.post('po/view/',{id: id}).then(function (data) {
            $scope.form = data.data;
            $scope.status = data.print;
            $scope.msg = data.msg;
            $scope.form.terbilang = $scope.terbilang(data.data.total_dibayar) + ' RUPIAH';
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

    };


})
