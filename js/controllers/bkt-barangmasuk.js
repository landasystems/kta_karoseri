app.controller('bbmCtrl', function ($scope, Data, toaster, keyboardManager) {
//init data

    var tableStateRef;

    $scope.refresh = function () {
        $scope.is_edit = false;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.detBbm = [];
        $scope.openedDet = -1;
        $scope.err_jml = false;
        $scope.jml_po = 0;
        $scope.pilih = {};
        $scope.form = {};
        $scope.autoSelect = false;
        $scope.errorDetail = '';
        $scope.detBbm = [{
                id: '',
                no_bbm: '',
                barang: '',
                jumlah: '',
                keterangan: '',
                tgl_terima: '',
                no_po: '',
            }];
    }

    $scope.refresh();

    $scope.lock = function () {
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('bbm/lock/', $scope.pilih).then(function (result) {
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
            Data.post('bbm/unlock/', $scope.pilih).then(function (result) {
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
    $scope.kalkulasi = function (jml, jml_po) {
        $scope.jml_po = jml_po;
        var selisih = jml_po - jml;
        if (selisih < 0) {
            toaster.pop('danger', "Error", "Jumlah tidak boleh melebihi jumlah dari PO sebesar " + $scope.jml_po);
            $scope.err_jml = true;
            $scope.detBbm.jumlah = 0;
            $scope.errorDetail = "\n Jumlah tidak boleh melebihi jumlah dari PO sebesar " + $scope.jml_po;
        } else if (jml == 0 && jml != '') {
            toaster.pop('danger', "Error", "Jumlah tidak boleh kosong " + $scope.jml_po);
            $scope.err_jml = true;
            $scope.detBbm.jumlah = 0;
            $scope.errorDetail = "\n Jumlah tidak boleh melebihi jumlah dari PO sebesar " + $scope.jml_po;
        } else {
            $scope.err_jml = false;
        }
    }
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
    $scope.cariPo = function ($query) {
        if ($query.length >= 3) {
            Data.get('po/cari', {nama: $query}).then(function (data) {
                $scope.listPo = data.data;
            });
        }
    };
    $scope.cariBarang = function ($query, $po) {
        $scope.results = [];
        if (typeof $scope.form.po != "undefined" && $scope.is_create == true) {
            Data.post('bbm/caribarang2', {barang: $query, no_po: $po, listBarang: $scope.detBbm}).then(function (data) {
                if ($scope.is_create == false) {
                    angular.forEach(data.data, function ($value, $key) {
                        $scope.results.push($value);
                        $scope.results[$key]['sisa_ambil'] = $value
                    })
                } else {
                    $scope.results = data.data;
                }
            });
        } else if ($query.length >= 3) {
            Data.post('bbm/caribarang2', {barang: $query, no_po: $po, listBarang: $scope.detBbm}).then(function (data) {
                if ($scope.is_create == false) {
                    angular.forEach(data.data, function ($value, $key) {
                        $scope.results.push($value);
                        $scope.results[$key]['sisa_ambil'] = $value
                    })
                } else {
                    $scope.results = data.data;
                }
            });
        }
    };
    $scope.getPo = function (form) {
        $scope.form.nm_supplier = form.nama_supplier;
        $scope.form.kd_supplier = form.kd_supplier;
        $scope.cariBarang('', form.nota);
    };
    $scope.cariSupplier = function ($query) {
        if ($query.length >= 3) {
            Data.get('supplier/cari', {nama: $query}).then(function (data) {
                $scope.listSupplier = data.data;
            });
        }
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

        Data.get('bbm', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.create = function (form) {
        $scope.refresh();
        $scope.autoSelect = false;
        $scope.$broadcast('first');
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form.tgl_nota = new Date();
        Data.get('bbm/kode', form).then(function (data) {
            $scope.form.no_bbm = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.no_bbm;
        $scope.form = form;
        $scope.form.tgl_nota = new Date(form.tgl_nota);
        $scope.getDetail(form.no_bbm);
    };
    $scope.view = function (form) {
        $scope.is_edit = true;
        $scope.is_false = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_bbm;
        $scope.form = form;
        $scope.getDetail(form.no_bbm);
    };
    $scope.save = function (form, detBbm) {
        if ($scope.err_jml == false) {
            var data = {
                form: form,
                detBbm: detBbm
            };
            var url = ($scope.is_create == true) ? 'bbm/create/' : 'bbm/update/' + form.no_bbm;
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
        } else {
            toaster.pop('danger', "Error", $scope.errorDetail);
        }
    };
    $scope.cancel = function () {
        $scope.refresh();
        $scope.callServer(tableStateRef); //reload grid ulang
    };
    $scope.delete = function (row) {
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.delete('bbm/delete/' + row.no_bbm).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.addDetail = function () {
        $scope.autoSelect = true;
        if ($scope.err_jml == false) {
            var newDet = {
                id: '0',
                kd_barang: '',
                jumlah: '',
                tgl_terima: '',
                no_wo: '',
            }
            $scope.setStatus();
            $scope.detBbm.unshift(newDet);
        } else {
            toaster.pop('danger', "Error", $scope.errorDetail);
        }
    };
    $scope.removeRow = function (paramindex) {
        if ($scope.err_jml == false) {
            var comArr = eval($scope.detBbm);
            if (comArr.length > 1) {
                $scope.detBbm.splice(paramindex, 1);
            } else {
                alert("Something gone wrong");
            }
        } else {
            toaster.pop('danger', "Error", "Jumlah tidak boleh melebihi jumlah dari PO sebesar " + $scope.jml_po);
        }
    };
    $scope.getDetail = function (id) {
        Data.get('bbm/view/' + id).then(function (data) {
            $scope.form = data.data;
            $scope.form.nm_supplier = data.sup.nama_supplier;
            $scope.form.alamat_supplier = data.sup.alamat;
            $scope.detBbm = [];
            angular.forEach(data.details, function ($value, $key) {
                $scope.detBbm.push($value);
                $scope.detBbm[$key]['tgl_terima'] = new Date($value.tgl_terima);
            })
        });
    };
    $scope.excel = function (id) {
        window.location = 'api/web/bbm/exceldet/' + id;
    };
    keyboardManager.bind('ctrl+s', function () {
        if ($scope.is_edit == true) {
            $scope.save($scope.form, $scope.detBbm);
        }
    });
});
