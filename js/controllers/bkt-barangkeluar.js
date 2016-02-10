app.controller('bbkCtrl', function ($scope, Data, toaster, $modal, keyboardManager) {
//init data
    var tableStateRef;
    $scope.detailstok = function (sisa, stok) {
        $scope.sisa_pengambilan = sisa;
        $scope.stok_sekarang = stok;
    }

    $scope.tgl_Print = new Date();

    $scope.refresh = function () {
        $scope.jenis_kmp = [];
        $scope.bagian = '-';
        $scope.tgl_cetak = new Date();
        $scope.is_print = 0;
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.is_copy = false;
        $scope.autoSelect = false;
        $scope.displayed = [];
        $scope.gantiStatus = {};
        $scope.form = {};
        $scope.form.kat_bbk = 'produksi';
        $scope.err_pengambilan = false;
        $scope.sisa_pengambilan = 0;
        $scope.stok_sekarang = 0;
        $scope.results = [];
        $scope.resultriwayat = [];
        $scope.resultsbarang = [];
        $scope.detailBbk = [{
                kd_barang: '',
                jml: '',
                ket: '',
            }];
        $scope.detailstok(0, 0);
        $scope.noWoasli = '';
        $scope.halamanPrint = 0;
    };

    $scope.refresh();

    $scope.lock = function (form) {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('bbk/lock/', {id: form}).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan");
                } else {
                    $scope.is_edit = false;
                    $scope.callServer(tableStateRef); //reload grid ulang
                    toaster.pop('success', "Berhasil", "Data Berhasil Terproses");
                }
            });
        }
    }

    $scope.unlock = function (form) {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('bbk/unlock/', {id: form}).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan");
                } else {
                    $scope.is_edit = false;
                    $scope.callServer(tableStateRef); //reload grid ulang
                    toaster.pop('success', "Berhasil", "Data Berhasil Terproses");
                }
            });
        }
    }

    $scope.bukaPrint = function (form) {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('bbk/bukaprint/', {no_bbk: form}).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan");
                } else {
                    toaster.pop('success', "Berhasil", "Data Berhasil Terproses");
                    $scope.callServer(tableStateRef); //reload grid ulang
                }
            });
        }
    }

    $scope.simpanPrint = function (no_bbk) {
        Data.get('bbk/print', {no_bbk: no_bbk}).then(function (data) {
            $scope.is_print = 1;
        });
    }

//    $scope.modal = function (form) {
//        var modalInstance = $modal.open({
//            templateUrl: 'tpl/t_bkt-barangkeluar/modal.html',
//            controller: 'modalCtrl',
//            size: 'lg',
//            resolve: {
//                form: function () {
//                    return form;
//                }}
//        });
//    }

    $scope.kalkulasi2 = function (indek) {
        if ($scope.form.kat_bbk == 'produksi') {
            var jml = ($scope.detailBbk[indek]['jml']) ? $scope.detailBbk[indek]['jml'] : 0;

            var tmpSisa = $scope.detailBbk[indek]['sisaAmbil'];
            var tmpStok = $scope.detailBbk[indek]['stok_sekarang'];

            if ((jml != '' || jml > 0)) {
                if (tmpSisa < 0) {
                    $scope.err_pengambilan = true;
                    $scope.detailBbk[indek]['error_field'] = true;
                    toaster.pop('error', $scope.detailBbk[indek]['kd_barang']['nm_barang'] + ' Sisa pengambilan bahan telah habis');
                } else if (tmpStok < 0) {
                    $scope.err_pengambilan = true;
                    $scope.detailBbk[indek]['error_field'] = true;
                    toaster.pop('error', $scope.detailBbk[indek]['kd_barang']['nm_barang'] + ' Stok Bahan Telah habis');
                } else if ((tmpSisa - jml) >= 0 && (tmpStok - jml) >= 0) {
                    $scope.err_pengambilan = false;
                    $scope.detailBbk[indek]['error_field'] = false;
                    $scope.detailBbk[indek]['kd_barang']['sisa_pengambilan'] = tmpSisa - jml;
                    $scope.detailBbk[indek]['kd_barang']['stok_sekarang'] = tmpStok - jml;
                } else {
                    $scope.err_pengambilan = true;
                    $scope.detailBbk[indek]['error_field'] = true;
                    toaster.pop('error', $scope.detailBbk[indek]['kd_barang']['nm_barang'] + " Jumlah tidak boleh melebihi sisa pengambilan dan stok barang");
                }
            } else {
                $scope.err_pengambilan = false;
                $scope.detailBbk[indek]['error_field'] = false;
                $scope.detailBbk[indek]['kd_barang']['sisa_pengambilan'] = tmpSisa;
                $scope.detailBbk[indek]['kd_barang']['stok_sekarang'] = tmpStok;
            }


            angular.forEach($scope.detailBbk, function ($value, $key) {
                if ($scope.err_pengambilan == true) {
                    if ($value.error_field == true) {
                        $value.error_kalkulasi = false;
                    } else {
                        $value.error_kalkulasi = true;
                    }
                } else {
                    $value.error_kalkulasi = false;
                    $value.error_field == false;
                }
            });
        } else {
            var jml = ($scope.detailBbk[indek]['jml']) ? $scope.detailBbk[indek]['jml'] : 0;

            var tmpStok = $scope.detailBbk[indek]['kd_barang']['stok_barang'];

            $scope.detailBbk[indek]['kd_barang']['stok_sekarang'] = tmpStok;

            if ((jml != '' || jml > 0)) {
                if (tmpStok < 0) {
                    $scope.err_pengambilan = true;
                    toaster.pop('error', 'Stok Bahan Telah habis bahan telah habis');
                } else if ((tmpStok - jml) >= 0) {
                    $scope.err_pengambilan = false;
                    $scope.detailBbk[indek]['kd_barang']['stok_sekarang'] = tmpStok - jml;
                } else {
                    $scope.err_pengambilan = true;
                    toaster.pop('error', "Jumlah tidak boleh melebihi stok barang");
                }
            } else {
                $scope.err_pengambilan = true;
            }
        }
    }

    $scope.kalkulasi = function (sisa, stok, jml_keluar) {
        if (jml_keluar > 0) {
            $scope.err_pengambilan = false;
            if (typeof $scope.form.no_wo != "undefined") {
                var sSisa = sisa - jml_keluar;
                var sStok = stok - jml_keluar;
                if (sisa == 0) {
                    $scope.err_pengambilan = true;
                    toaster.pop('error', "Sisa pengambilan bahan telah habis");
                } else if (stok == 0) {
                    $scope.err_pengambilan = true;
                    toaster.pop('error', "Stok bahan telah habis");
                } else if (sSisa >= 0 && sStok >= 0) {
                    $scope.err_pengambilan = false;
                    $scope.sisa_pengambilan = sisa - jml_keluar;
                    $scope.stok_sekarang = stok - jml_keluar;
                    ($scope.sisa_pengambilan > 0) ? $scope.detailBbk.jml = $scope.detailBbk.jml : $scope.detailBbk.jml = 0;
                    ($scope.sisa_pengambilan >= 0) ? $scope.sisa_pengambilan = $scope.sisa_pengambilan : $scope.sisa_pengambilan = 0;
                } else {
                    $scope.err_pengambilan = true;
                    toaster.pop('error', "Jumlah tidak boleh melebihi sisa pengambilan bahan");
                }
            } else {
                $scope.sisa_pengambilan = 0;
                $scope.stok_sekarang = stok - jml_keluar;
                if ($scope.stok_sekarang < 0) {
                    toaster.pop('error', "Jumlah tidak boleh melebihi stok sekarang");
                    $scope.err_pengambilan = true;
                } else {
                    $scope.err_pengambilan = false;
                }
            }
        } else {
            toaster.pop('error', "Jumlah tidak boleh ada yang kosong");
            $scope.err_pengambilan = true;
        }
    }

    $scope.cariWo = function ($query) {
        Data.get('wo/wospk', {nama: $query}).then(function (data) {
            $scope.results = data.data;
        });
    }

    $scope.cariJabatan = function ($query) {
        Data.get('jabatan/cari', {nama: $query}).then(function (data) {
            $scope.resultsjabatan = data.data;
        });
    }

    $scope.cariJabatan2 = function (no_wo, nama) {
        Data.get('jabatan/cari2', {no_wo: no_wo, nama: nama, kat_bbk: $scope.form.kat_bbk}).then(function (data) {
            $scope.resultsjabatan = data.data;
        });

        if ($scope.is_create == true && $scope.form.no_wo != '') {
            $scope.kalkulasiCopy();
        }
    }

    $scope.cariKaryawan = function ($query) {
        Data.get('jabatan/listkaryawanabsent', {nama: $query}).then(function (data) {
            $scope.resultskaryawan = data.data;
        });
    }

    $scope.cariKaryawanPerJabatan = function ($jabatan, $query) {
        Data.get('jabatan/listkaryawanabsentjabatan', {jabatan: $jabatan, nama: $query}).then(function (data) {
            $scope.resultskaryawan = data.data;
        });
    }

    $scope.resultriwayat = [];
    $scope.riwayatAmbil = function (no_wo, kd_jab) {
        if (typeof $scope.form.no_wo != "undefined" && typeof $scope.form.kd_jab != "undefined") {
            Data.post('bbk/riwayatambil', {no_wo: no_wo, kd_jab: kd_jab}).then(function (data) {
                $scope.resultriwayat = data.data;
            });
        }
    }

    $scope.listBarang = function ($query, no_wo, kd_jab) {
        if (typeof no_wo != "undefined" && ($scope.noWoasli == no_wo.no_wo) && $scope.is_create == true) {
            toaster.pop('error', "Masukkan nomor wo yang lain");
            $scope.form.no_wo = '';
        } else {
            //============== jika tambah =============//
            if (typeof $scope.form.no_wo != "undefined" && typeof $scope.form.kd_jab != "undefined" && $scope.is_create == true && $scope.is_copy == false) {
                Data.post('bbk/listbarang', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: $scope.detailBbk}).then(function (data) {
                    $scope.resultsbarang = data.data;
                    $scope.resultsbarang.stok = data.data.stok_sekarang;
                });
                if ($scope.is_create == true) {
                    $scope.riwayatAmbil(no_wo, kd_jab);
                }
            }
            //=============== jika copy bbk ================//
            else if (typeof $scope.form.no_wo != "undefined" && $scope.form.no_wo != '' && typeof $scope.form.kd_jab != "undefined" && $scope.is_create == true && $scope.is_copy == true) {
                Data.post('bbk/listbarang', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: [{}]}).then(function (data) {
                    angular.forEach(data.data, function ($value, $key) {
                        $scope.resultsbarang.push($value);
                        angular.forEach($scope.detailBbk, function ($value2, $key2) {
                            if ($value2.kd_barang.kd_barang == $value.kd_barang) {
                                $scope.detailBbk[$key2]['kd_barang'] = $value;
                                $scope.kalkulasi($value.sisa_pengambilan, $value.stok_sekarang, $value2.jml);
                                if ($scope.err_pengambilan == true) {
                                    $value2.jml = 0;
                                }
                            }
                        });
                    });
                });
                if ($scope.is_create == true) {
                    $scope.riwayatAmbil(no_wo, kd_jab);
                }
            }
            //================ jika no wo kosong ===============//
            else if ($query.length >= 2) {
                Data.post('bbk/listbarang', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: $scope.detailBbk}).then(function (data) {
                    $scope.resultsbarang = data.data;
                });
            }
        }
    }

    $scope.listBarang2 = function ($query, no_wo, kd_jab) {
        if (typeof no_wo != "undefined" && ($scope.noWoasli == no_wo.no_wo)) {
            toaster.pop('error', "Masukkan nomor wo yang lain");
            $scope.form.no_wo = '';
        } else {
            //============== jika tambah =============//
            if (typeof $scope.form.no_wo != "undefined" && typeof $scope.form.kd_jab != "undefined" && $scope.is_create == true && $scope.is_copy == false) {
                Data.post('bbk/listbarang2', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: $scope.detailBbk}).then(function (data) {
                    $scope.detailBbk = [];
                    angular.forEach(data.data, function ($value2, $key2) {
                        var barang = {
                            kd_barang: $value2,
                            sisaAmbil: ($value2.sisa_pengambilan).toString(),
                            stok_sekarang: ($value2.stok_sekarang).toString(),
                            error_kalkulasi: false,
                            error_field: false,
                            jml: '',
                            ket: '',
                            satuan: $value2.satuan,
                        }
                        $scope.detailBbk.push(barang);
                    })
                });

                if ($scope.is_create == true) {
                    $scope.riwayatAmbil(no_wo, kd_jab);
                }


            }
            //=============== jika copy bbk ================//
            else if (typeof $scope.form.no_wo != "undefined" && $scope.form.no_wo != '' && typeof $scope.form.kd_jab != "undefined" && $scope.is_copy == true) {
                var Detail = $scope.detailBbk;
                delete $scope.detailBbk;
                var Det = [];
                Data.post('bbk/listbarang2', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: Detail, copy_bbk: "ya"}).then(function (data) {
                    angular.forEach(data.data, function ($value, $key) {
                        $scope.resultsbarang.push($value);
                        angular.forEach(Detail, function ($value2, $key2) {
                            if ($key == $key2) {
                                var barang = {
                                    kd_barang: $value,
                                    sisaAmbil: $value.sisa_pengambilan,
                                    stok_sekarang: $value.stok_sekarang,
                                    error_kalkulasi: false,
                                    error_field: false,
                                    jml: $value2.jml,
                                    jmlKeluar: $value2.jml,
                                    ket: $value2.ket,
                                    satuan: $value2.satuan,
                                }
                                Det.push(barang);
                            }
                        });
                    });
                    $scope.detailBbk = Det;
                    $scope.kalkulasiCopy();

                });

                if ($scope.is_create == true) {
                    $scope.riwayatAmbil(no_wo, kd_jab);
                }
            }
            //================ jika no wo kosong ===============//
            else if ($query.length >= 2) {
                Data.post('bbk/listbarang2', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: $scope.detailBbk}).then(function (data) {
                    $scope.resultsbarang = data.data;
                });
            }
        }
    }

    $scope.addDetail = function () {
        $scope.autoSelect = true;
        if ($scope.err_pengambilan == false || $scope.detailBbk.length == 0) {
            $scope.detailBbk.unshift({
                kd_barang: '',
                jml: '',
                ket: '',
            });
        } else {
            toaster.pop('error', "Pastikan semua detail bbk terisi dengan benar");
        }
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detailBbk);
        $scope.detailBbk.splice(paramindex, 1);
        angular.forEach($scope.detailBbk, function ($value, $key) {
            $scope.kalkulasi($value.kd_barang.sisa_pengambilan, $value.kd_barang.stok_sekarang, $value.jml);
        });
    };

    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    
    $scope.copyData = function (bbk, kd_bbk) {
        $scope.form = bbk;
        $scope.form.tanggal = new Date();
        $scope.selected(bbk.no_bbk, kd_bbk);
    };
    
    $scope.cariBbk = function ($query) {
        if ($query.length >= 3) {
            Data.get('bbk/listbbk', {nama: $query}).then(function (data) {
                $scope.resultBbk = data.data;
            });
        }
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

        Data.get('bbk', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.copy = function (form, detail) {
        $scope.refresh();
        $scope.is_copy = true;
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Salin Data";
        Data.get('bbk/kode').then(function (data) {
            $scope.form.no_bbk = data.kode;
        });
        Data.get('pengguna/profile').then(function (data) {
            $scope.form.petugas = data.data.nama;
        });
    };

    $scope.create = function () {
        $scope.refresh();
        $scope.autoSelect = false;
        $scope.$broadcast('first');
        $scope.formtitle = "Form Tambah Data";
        $scope.err_pengambilan = false;
        $scope.sisa_pengambilan = 0;
        $scope.stok_sekarang = 0;
        Data.get('pengguna/profile').then(function (data) {
            $scope.form.petugas = data.data.nama;
        });
        Data.get('bbk/kode').then(function (data) {
            $scope.form.no_bbk = data.kode;
        });
        $scope.form.tanggal = new Date();
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_copy = false;
        $scope.is_create = true;
    };
    $scope.update = function (form) {
        $scope.autoSelect = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_copy = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.no_bbk;
        $scope.form = form;
        $scope.form.tanggal = new Date(form.tanggal);
        $scope.selected(form.no_bbk, '');
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.is_create = false;
        $scope.formtitle = "Lihat Data : " + form.no_bbk;
        $scope.form = form;
        $scope.form.tanggal = new Date(form.tanggal);
        $scope.selected(form.no_bbk, '');
    };

    $scope.save = function (form, detail, versi) {
        if ($scope.err_pengambilan == false) {
            if (form.kat_bbk == 'produksi' && typeof $scope.form.no_wo == '') {
                toaster.pop('error', "Terjadi Kesalahan", 'no wo tidak boleh kosong');
            } else if (form.kat_bbk == 'umum' && typeof form.no_surat == 'undefined') {
                toaster.pop('error', "Terjadi Kesalahan", 'no surat tidak boleh kosong');
            } else {
                $scope.detailBbk = [];
                angular.forEach(detail, function ($value, $key) {
                    if ($value.jml > 0) {
                        $scope.detailBbk.push($value);
                    }
                });

                $scope.detPrint($scope.detailBbk);

                var data = {
                    bbk: form,
                    detailBbk: detail, };
                var url = ($scope.is_create == true) ? 'bbk/create' : 'bbk/update/' + form.no_bbk;
                Data.post(url, data).then(function (result) {
                    if (result.status == 0) {
                        var error = '';
                        angular.forEach(result.errors, function ($value, $key) {
                            error = error + $value + "\n";
                        });
                        toaster.pop('error', "Terjadi Kesalahan", error);
                    } else {
                        toaster.pop('success', "Berhasil", "Data berhasil tersimpan");

                        if ($scope.is_create == true) {
                            var popupWin = window.open('', '_blank', 'width=1000,height=700');
                            var elem = document.getElementById('printArea');
                            popupWin.document.open()
                            popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="css/print.css" /></head><body onload="window.print();window.close();">' + elem.innerHTML + '</html>');
                            popupWin.document.close();
                        }
                        $scope.refresh();
                        $scope.is_create = false;
                        $scope.is_edit = false;
                        $scope.create($scope.form);
                    }
                });
            }
        } else {
            toaster.pop('error', "Semua data harus benar");
        }
    };
    $scope.umum = function () {
        $scope.create();
        $scope.form.kat_bbk = 'umum';
    };
    $scope.produksi = function () {
        $scope.create();
        $scope.form.kat_bbk = 'produksi';
    };
    $scope.cancel = function () {
        $scope.callServer(tableStateRef); //reload grid ulang
        $scope.is_copy = false;
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.err_pengambilan = false;
        $scope.refresh();
    };
    $scope.delete = function (row) {
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('bbk/delete/' + row.no_bbk).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function (id, id_baru) {
        Data.get('bbk/view/' + id).then(function (data) {
            $scope.form = data.data;

            if ($scope.form.no_wo.no_wo != '-' && $scope.form.no_wo.no_wo != '') {
                $scope.form.kat_bbk = 'produksi';
            } else {
                $scope.form.kat_bbk = 'umum';
            }

            $scope.is_print = $scope.form.status;
            if (id_baru != '') {
                $scope.form.no_bbk = id_baru;
                $scope.form.tanggal = new Date();
                Data.get('pengguna/profile').then(function (data) {
                    $scope.form.petugas = data.data.nama;
                });
                $scope.noWoasli = $scope.form.no_wo.no_wo;
                $scope.form.no_wo = '';
            } else {
                $scope.riwayatAmbil($scope.form.no_wo, $scope.form.kd_jab);
            }

            if (jQuery.isEmptyObject(data.detail)) {
                $scope.detailBbk = [{
                        kd_barang: '',
                        jml: '',
                        ket: '',
                    }];
            } else {
                $scope.detailBbk = [];
                angular.forEach(data.detail, function ($value, $key) {
                    if ($value.jml > 0) {
                        var barang = {
                            kd_barang: $value,
                            sisaAmbil: $value.sisa_pengambilan,
                            stok_sekarang: $value.stok_sekarang,
                            error_kalkulasi: false,
                            error_field: false,
                            jml: $value.jml,
                            jmlKeluar: $value.jml,
                            ket: $value.ket,
                            satuan: $value.satuan,
                        }

                        $scope.detailBbk.push(barang);
                    }
                });
            }
            console.log($scope.detailBbk + " <- ASLI");

            $scope.detPrint($scope.detailBbk);
        });
    };

    $scope.kalkulasiCopy = function () {
        if ($scope.is_copy == true) {
            angular.forEach($scope.detailBbk, function ($value, $key) {
                $scope.kalkulasi2($key);
            });
        }
    }

    $scope.detPrint = function (detail) {
        $scope.halamanPrint = Math.ceil(detail.length / 8);
        $scope.detailBbkPrint = [];
        var index = 0;
        var no = 1;
        for (i = 0; i < $scope.halamanPrint; i++) {
            var newDet = [];
            for (a = 1; a <= 8; a++) {
                if (typeof detail[index] != "undefined") {
                    detail[index]['no'] = no;
                    newDet.push(detail[index]);
                    $scope.detailBbkPrint[i] = newDet;
                    no++;
                }
                index++;
            }
        }
    }
    
    keyboardManager.bind('ctrl+s', function () {
        if ($scope.is_create == true) {
            $scope.save($scope.form, $scope.detailBbk);
        }
    });
});

//app.controller('modalCtrl', function ($scope, Data, $modalInstance, form, toaster) {
//
//    $scope.form = {
//        no_wo: '', kd_kerja: '',
//        kd_barang: '',
//        tgl: '',
//        jml: '',
//        ket: '',
//    }
//
//    $scope.form.tgl = new Date();
//    $scope.open1 = function ($event) {
//        $event.preventDefault();
//        $event.stopPropagation();
//        $scope.opened1 = true;
//    };
//
//    $scope.cariWo = function ($query) {
//        if ($query.length >= 3) {
//            Data.get('wo/wospk', {nama: $query}).then(function (data) {
//                $scope.results = data.data;
//            });
//        }
//    }
//
//    $scope.cariBagian = function ($query) {
//        if ($query.length >= 3) {
//            Data.get('jabatan/cari', {nama: $query}).then(function (data) {
//                $scope.resultsjabatan = data.data;
//            });
//        }
//    }
//
//    $scope.listBarang = function ($query, no_wo, kd_jab) {
//        if (typeof $scope.form.no_wo != "undefined" && typeof $scope.form.kd_kerja != "undefined") {
//            Data.post('bbk/listbarang', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: $scope.detailBbk}).then(function (data) {
//                $scope.resultsbarang = data.data;
//            });
//        } else if ($query.length >= 2) {
//            Data.post('bbk/listbarang', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: $scope.detailBbk}).then(function (data) {
//                $scope.resultsbarang = data.data;
//            });
//        }
//    }
//    $scope.simpan = function (form) {
//        var url = 'bbk/pengecualian';
//        Data.post(url, form).then(function (result) {
//            if (result.status == 0) {
//                toaster.pop('error', "Terjadi Kesalahan", result.errors);
//            } else {
//                $scope.is_edit = false;
//                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
//            }
//        });
//        $modalInstance.dismiss('cancel');
//    };
//    $scope.cancel = function () {
//        $modalInstance.dismiss('cancel');
//    };
//})
