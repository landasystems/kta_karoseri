app.controller('bbkCtrl', function ($scope, Data, toaster, $modal, keyboardManager) {
//init data
    var tableStateRef;
    $scope.detailstok = function (sisa, stok) {
        $scope.sisa_pengambilan = sisa;
        $scope.stok_sekarang = stok;
    }

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

    $scope.modal = function (form) {
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_bkt-barangkeluar/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            resolve: {
                form: function () {
                    return form;
                }}
        });
    }
    $scope.kalkulasi = function (sisa, stok, jml_keluar) {
        console.log(sisa);
        console.log(stok);
        console.log(jml_keluar);
        console.log($scope.detailBbk);
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
            $scope.err_pengambilan = false;
            $scope.sisa_pengambilan = 0;
            $scope.stok_sekarang = stok - jml_keluar;
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

    $scope.cariKaryawan = function ($query) {
        Data.get('jabatan/listkaryawanabsent', {nama: $query}).then(function (data) {
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
        if (typeof no_wo != "undefined" && ($scope.noWoasli == no_wo.no_wo)) {
            toaster.pop('error', "Masukkan nomor wo yang lain");
            $scope.form.no_wo = '';
        } else {
            //============== jika tambah =============//
            if (typeof $scope.form.no_wo != "undefined" && typeof $scope.form.kd_jab != "undefined" && $scope.is_create == true && $scope.is_copy == false) {
                Data.post('bbk/listbarang', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: $scope.detailBbk}).then(function (data) {
                    $scope.resultsbarang = data.data;
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

    $scope.addDetail = function () {
        $scope.autoSelect = true;
        if ($scope.err_pengambilan == false || $scope.detailBbk.length == 0) {
            $scope.detailBbk.unshift({
                kd_barang: '',
                jml: '',
                ket: '',
            });
        } else {
            toaster.pop('error', "Sisa pengambilan bahan telah habis");
        }
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detailBbk);
        $scope.detailBbk.splice(paramindex, 1);
        angular.forEach($scope.detailBbk, function ($value, $key) {
            $scope.kalkulasi($value.kd_barang.sisa_pengambilan, $value.kd_barang.stok_sekarang, $value.jml);
        });
        console.log($scope.detailBbk);
    };
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.copyData = function (bbk, kd_bbk) {
        $scope.form = bbk;
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

    $scope.create = function (form) {
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

    $scope.save = function (form, detail) {
        if ($scope.err_pengambilan == false && form.penerima != '') {
            var data = {
                bbk: form,
                detailBbk: detail,
            };
            var url = ($scope.is_create == true) ? 'bbk/create' : 'bbk/update/' + form.no_bbk;
            Data.post(url, data).then(function (result) {
                if (result.status == 0) {
                    toaster.pop('error', "Terjadi Kesalahan", result.errors);
                } else {
                    toaster.pop('success', "Berhasil", "Data berhasil tersimpan");
                    if ($scope.is_create == true) {
                        var popupWin = window.open('', '_blank', 'width=1000,height=700');
                        var elem = document.getElementById('printArea');
                        popupWin.document.open()
                        popupWin.document.write('<html><head><link rel="stylesheet" type="text/css" href="css/print.css" /></head><body onload="window.print();window.close();">' + elem.innerHTML + '</html>');
                        popupWin.document.close();
                    }
                    $scope.is_create = false;
                    $scope.is_edit = false;
                    $scope.create($scope.form);
                }
            });
        } else if (form.penerima == '') {
            toaster.pop('error', "Penerima tidak boleh kosong");
        } else {
            toaster.pop('error', "Semua data harus benar");
        }
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
            $scope.is_print = $scope.form.status;
            if (id_baru != '') {
                $scope.form.no_bbk = id_baru;
                $scope.form.tanggal = new Date();
                Data.get('pengguna/profile').then(function (data) {
                    $scope.form.petugas = data.data.nama;
                });
                $scope.noWoasli = $scope.form.no_wo.no_wo;
                console.log($scope.noWoasli);
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
                $scope.detailBbk = data.detail;
            }
        });
    };
    keyboardManager.bind('ctrl+s', function () {
        if ($scope.is_edit == true) {
            $scope.save($scope.form, $scope.detailBbk);
        }
    });
});
app.controller('modalCtrl', function ($scope, Data, $modalInstance, form, toaster) {

    $scope.form = {
        no_wo: '',
        kd_kerja: '',
        kd_barang: '',
        tgl: '',
        jml: '',
        ket: '',
    }

    $scope.form.tgl = new Date();
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.cariWo = function ($query) {
        if ($query.length >= 3) {
            Data.get('wo/wospk', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.cariBagian = function ($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/cari', {nama: $query}).then(function (data) {
                $scope.resultsjabatan = data.data;
            });
        }
    }

    $scope.listBarang = function ($query, no_wo, kd_jab) {
        if (typeof $scope.form.no_wo != "undefined" && typeof $scope.form.kd_kerja != "undefined") {
            Data.post('bbk/listbarang', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: $scope.detailBbk}).then(function (data) {
                $scope.resultsbarang = data.data;
            });
        } else if ($query.length >= 2) {
            Data.post('bbk/listbarang', {nama: $query, no_wo: no_wo, kd_jab: kd_jab, listBarang: $scope.detailBbk}).then(function (data) {
                $scope.resultsbarang = data.data;
            });
        }
    }
    $scope.simpan = function (form) {
        var url = 'bbk/pengecualian';
        Data.post(url, form).then(function (result) {
            if (result.status == 0) {
                toaster.pop('error', "Terjadi Kesalahan", result.errors);
            } else {
                $scope.is_edit = false;
                toaster.pop('success', "Berhasil", "Data berhasil tersimpan")
            }
        });
        $modalInstance.dismiss('cancel');
    };
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
})
