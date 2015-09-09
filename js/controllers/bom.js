app.controller('bomCtrl', function ($scope, Data, toaster, FileUploader, $stateParams, $modal) {
    var kode_unik = new Date().getUTCMilliseconds() + "" + (Math.floor(Math.random() * (20 - 10 + 1)) + 10);
    var uploader = $scope.uploader = new FileUploader({
        url: 'img/upload.php?folder=bom&kode=' + kode_unik,
        queueLimit: 1,
        removeAfterUpload: true,
    });
    uploader.filters.push({
        name: 'imageFilter',
        fn: function (item) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
    });
    Data.get('chassis/merk').then(function (data) {
        $scope.listMerk = data.data;
    });
    $scope.typeChassis = function (merk) {
        Data.get('chassis/tipe?merk=' + merk).then(function (data) {
            $scope.listTipe = data.data;
        });
    };
    $scope.getchassis = function (merk, tipe) {
        Data.get('bom/chassis/?merk=' + merk + '&tipe=' + tipe).then(function (data) {
            $scope.form.kd_chassis = data.kode;
            $scope.form.jenis = data.jenis;
        });
    };
    $scope.open1 = function ($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.cariModel = function ($query) {
        if ($query.length >= 3) {
            Data.get('modelkendaraan/listmodel', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };
    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.resultsbarang = data.data;
            });
        }
    };
    //init data;
    var tableStateRef;
    var paramRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_copy = false;
    $scope.cariBom = function ($query) {
        if ($query.length >= 3) {
            Data.get('bom/cari', {nama: $query}).then(function (data) {
                $scope.rBom = data.data;
            });
        }
    };
    $scope.addDetail = function (detail) {
        $scope.detBom.unshift({
            kd_jab: '',
            kd_barang: '',
            qty: '',
            ket: '',
        })
    };
    $scope.removeRow = function (paramindex) {
        var comArr = eval($scope.detBom);
        if (comArr.length > 1) {
            $scope.detBom.splice(paramindex, 1);
        } else {
            alert("Something gone wrong");
        }
    };
    $scope.callServer = function callServer(tableState) {
        console.log(tableState);
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
        Data.get('bom', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.excel = function () {
        Data.get('bom', paramRef).then(function (data) {
            window.location = 'api/web/bom/excel';
        });
    }

    $scope.excel = function () {
        Data.get('bom', paramRef).then(function (data) {
            window.location = 'api/web/bom/excel';
        });
    }

    $scope.excelTrans = function (id) {
        Data.get('bom/view/' + id).then(function (data) {
            window.location = 'api/web/bom/exceltrans';
        });
    }

    $scope.create = function (form, detail) {
        $scope.is_copy = false;
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
        Data.get('bom/kode').then(function (data) {
            $scope.form.kd_bom = data.kode;
        });
        $scope.form.tgl_buat = new Date();
        $scope.detBom = [
            {
                kd_jab: '',
                kd_barang: '',
                qty: '',
                ket: '',
            }
        ];
    };
    $scope.copy = function (form, detail) {
        $scope.is_copy = true;
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Salin Data";
        $scope.form = {};
        $scope.detBom = [
            {
                kd_jab: '',
                kd_barang: '',
                qty: '',
                ket: '',
            }
        ];
        Data.get('bom/kode').then(function (data) {
            $scope.form.kd_bom = data.kode;
        });
    };
    $scope.update = function (form) {
        $scope.is_copy = false;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Edit Data : " + form.kd_bom;
        Data.get('chassis/tipe?merk=' + form.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected(form.kd_bom, '');
    };
    $scope.view = function (form) {
        $scope.is_copy = false;
        $scope.is_create = false;
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.kd_bom;
        Data.get('chassis/tipe?merk=' + form.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected(form.kd_bom, '');
    };
    $scope.copyData = function (bom, kd_bom) {
        $scope.form = bom;
        Data.get('chassis/tipe?merk=' + bom.merk).then(function (data) {
            $scope.listTipe = data.data;
        });
        $scope.selected(bom.kd_bom, kd_bom);
    };
    $scope.save = function (form, detail) {
        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.gambar = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        }
        var data = {
            bom: form,
            detailBom: detail,
        };
        var url = ($scope.is_create == true) ? 'bom/create/' : 'bom/update/' + form.kd_bom;
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
        if (!$scope.is_view) { //hanya waktu edit cancel, di load table lagi
            $scope.callServer(tableStateRef);
        }
        $scope.is_edit = false;
        $scope.is_view = false;
    };
    $scope.delete = function (row) {
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('bom/delete/' + row.kd_bom).then(function (result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function (id, kd_bom_baru) {
        Data.get('bom/view/' + id).then(function (data) {

            $scope.form = data.data;
            if (kd_bom_baru != '') {
                $scope.form.kd_bom = kd_bom_baru;
                $scope.form.tgl_buat = '';
            }

            if (jQuery.isEmptyObject(data.detail)) {
                $scope.detBom = [
                    {
                        kd_jab: '',
                        kd_barang: '',
                        qty: '',
                        ket: '',
                    }
                ];
            } else {
                $scope.detBom = data.detail;
            }
        });
    }
    $scope.modal = function (form) {
        var modalInstance = $modal.open({
            templateUrl: 'tpl/t_bom/modal.html',
            controller: 'modalCtrl',
            size: 'lg',
            resolve: {
                form: function () {
                    return form;
                }
            }
        });
    };
    if ($stateParams.form != null) { //pengecekan jika ada pencarian, dilempar ke view
        $scope.view($stateParams.form);
    }
})

app.controller('modalCtrl', function ($scope, Data, $modalInstance, form) {

    $scope.cariBagian = function ($query) {
        if ($query.length >= 3) {
            Data.get('jabatan/cari', {nama: $query}).then(function (data) {
                $scope.resultsjabatan = data.data;
            });
        }
    }

    $scope.cariBarang = function ($query) {
        if ($query.length >= 3) {
            Data.get('barang/cari', {barang: $query}).then(function (data) {
                $scope.resultsbarang = data.data;
            });
        }
    }

    $scope.formmodal = form;
    console.log(form);
    $scope.cancel = function () {
        $modalInstance.dismiss('cancel');
    };
})

app.controller('rekapBomCtrl', function ($scope, Data) {
    //init data;
    var paramRef;
    $scope.tableStateRef = '';
    $scope.jenis = '';
    $scope.is_show = false;
    $scope.no_wo = '';
    $scope.r_bomModel = [];
    $scope.form = {};
    $scope.rekap = function () {
        $scope.jenis = 'rekap';
        $scope.is_show = false;
    }

    $scope.rekapRealisasiWo = function () {
        $scope.jenis = 'realisasi_wo';
        $scope.is_show = false;
    }

    $scope.rekapRealisasiModel = function () {
        $scope.jenis = 'realisasi_model';
        $scope.is_show = false;
    }

    $scope.callServer = function callServer(tableState) {
        $scope.tableStateRef = tableState;
        $scope.isLoading = true;
        var offset = tableState.pagination.start || 0;
        var limit = tableState.pagination.number || 10;
        var param = {offset: offset, limit: limit};
        if (tableState.sort.predicate) {
            param['sort'] = tableState.sort.predicate;
        }
        if (tableState.search.predicateObject) {
            param['filter'] = tableState.search.predicateObject;
        }
        paramRef = param;
        Data.get('bom/rekap', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.excelRekap = function () {
        Data.get('bom/rekap', paramRef).then(function (data) {
            window.location = 'api/web/bom/excel';
        });
    }

    $scope.callServer2 = function callServer(tableState) {
        $scope.tableStateRef = tableState;
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
        Data.get('bom/rekaprealisasiwo', param).then(function (data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };
    $scope.excelRekapRealisasiWo = function () {
        Data.get('bom/rekaprealisasiwo', paramRef).then(function (data) {
            window.location = 'api/web/bom/excelrealisasiwo';
        });
    }

    $scope.excelRekapRealisasiModel = function () {
        Data.get('bom/rekaprealisasiwo', paramRef).then(function (data) {
            window.location = 'api/web/bom/excelrealisasiwo';
        });
    };
    $scope.wo = [{
        }];
    Data.get('chassis/merk').then(function (data) {
        $scope.listMerk = data.data;
    });
    $scope.typeChassis = function (merk) {
        Data.get('chassis/tipe?merk=' + merk).then(function (data) {
            $scope.listTipe = data.data;
        });
    };
    $scope.getchassis = function (merk, tipe) {
        Data.get('bom/chassis/?merk=' + merk + '&tipe=' + tipe).then(function (data) {
            $scope.form.kd_chassis = data.kode;
            $scope.form.jenis = data.jenis;
        });
    };
    $scope.getNowo = function (kd_chassis, model) {
        var data = {
            kd_chassis: kd_chassis,
            model: model,
        }
        Data.get('bom/womodel', data).then(function (data) {
            $scope.wo = data.data;
        });
    };
    $scope.cariModel = function ($query) {
        if ($query.length >= 3) {
            Data.get('modelkendaraan/listmodel', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    };

//    $scope.tmp = [];
    $scope.r_bomModel = {
        "status": 1,
        "data": {
            "100004": {
                "no_wo": "NV-214010",
                "kd_barang": "100004",
                "nm_barang": "ANAK KUNCI + KUNCI",
                "satuan": "SET",
                "harga": "30000",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "200004": {
                "no_wo": "NV-214010",
                "kd_barang": "200004",
                "nm_barang": "ANTI KARAT\/RUSTGARD TA 02",
                "satuan": "KGR",
                "harga": "27900",
                "qty": "7",
                "jml_keluar": "7",
                "ket": "-",
                "jabatan": "ANTI KARAT OPERATOR"
            },
            "700003": {
                "no_wo": "NV-214010",
                "kd_barang": "700003",
                "nm_barang": "AS PEN SOCK BEKER PANJANG",
                "satuan": "PCS",
                "harga": "0",
                "qty": "4",
                "jml_keluar": "4",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "100010": {
                "no_wo": "NV-214010",
                "kd_barang": "100010",
                "nm_barang": "BANSBACH STANDARD 60 CM \/ 750 N",
                "satuan": "PCS",
                "harga": "130000",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "300002": {
                "no_wo": "NV-214010",
                "kd_barang": "300002",
                "nm_barang": "BATU FLEXIBLE AC 60 \/ 4\"",
                "satuan": "PCS",
                "harga": "4600",
                "qty": "0.25",
                "jml_keluar": " ",
                "ket": "Bumper belakang fiber",
                "jabatan": "FIBER OPERATOR"
            },
            "300003": {
                "no_wo": "NV-214010",
                "kd_barang": "300003",
                "nm_barang": "BATU GERINDA 4\" X 6 A24R",
                "satuan": "PCS",
                "harga": "3900",
                "qty": "1",
                "jml_keluar": "6",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700021": {
                "no_wo": "NV-214010",
                "kd_barang": "700021",
                "nm_barang": "BAUT MUR 6 X 10",
                "satuan": "PCS",
                "harga": "125",
                "qty": "20",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "700025": {
                "no_wo": "NV-214010",
                "kd_barang": "700025",
                "nm_barang": "BAUT MUR 6 X 20 JF",
                "satuan": "PCS",
                "harga": "1",
                "qty": "40",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "700035": {
                "no_wo": "NV-214010",
                "kd_barang": "700035",
                "nm_barang": "BAUT MUR 8 X 15",
                "satuan": "PCS",
                "harga": "270",
                "qty": "16",
                "jml_keluar": "16",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "700043": {
                "no_wo": "NV-214010",
                "kd_barang": "700043",
                "nm_barang": "BAUT MUR 8 X 60 JF",
                "satuan": "PCS",
                "harga": "424",
                "qty": "40",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "700046": {
                "no_wo": "NV-214010",
                "kd_barang": "700046",
                "nm_barang": "BAUT MUR BAJA F 1\/2 X 1 1\/2 JH",
                "satuan": "PCS",
                "harga": "3750",
                "qty": "15",
                "jml_keluar": "15",
                "ket": "-",
                "jabatan": "LANTAI OPERATOR"
            },
            "700053": {
                "no_wo": "NV-214010",
                "kd_barang": "700053",
                "nm_barang": "BETON EISER 6 MM X 12",
                "satuan": "LJR",
                "harga": "27500",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "700058": {
                "no_wo": "NV-214010",
                "kd_barang": "700058",
                "nm_barang": "BRACKET SANDARAN ELF",
                "satuan": "SET",
                "harga": "13500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "700059": {
                "no_wo": "NV-214010",
                "kd_barang": "700059",
                "nm_barang": "BRACKET SOCK BECKER",
                "satuan": "PCS",
                "harga": "8000",
                "qty": "4",
                "jml_keluar": "4",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "200017": {
                "no_wo": "NV-214010",
                "kd_barang": "200017",
                "nm_barang": "DEMPUL ST PUTTY P200 + HARD",
                "satuan": "KGR",
                "harga": "24166",
                "qty": "18",
                "jml_keluar": "18",
                "ket": "-",
                "jabatan": "DEMPUL OPERATOR"
            },
            "200018": {
                "no_wo": "NV-214010",
                "kd_barang": "200018",
                "nm_barang": "DEMPUL ST PUTTY P300",
                "satuan": "KGR",
                "harga": "30000",
                "qty": "1.5",
                "jml_keluar": 48,
                "ket": "-",
                "jabatan": "FIBER OPERATOR"
            },
            "100014": {
                "no_wo": "NV-214010",
                "kd_barang": "100014",
                "nm_barang": "DOOR CH MATA 2\/DOOR CHEKER HS 5117S",
                "satuan": "PCS",
                "harga": "17500",
                "qty": "3",
                "jml_keluar": "3",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "100015": {
                "no_wo": "NV-214010",
                "kd_barang": "100015",
                "nm_barang": "DOOR LOCK BAGASI HATCHBACK + STEKER",
                "satuan": "PCS",
                "harga": "70500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "100016": {
                "no_wo": "NV-214010",
                "kd_barang": "100016",
                "nm_barang": "DOOR LOCK L-300 LH HS 5034 + STEKER",
                "satuan": "PCS",
                "harga": "68500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "200022": {
                "no_wo": "NV-214010",
                "kd_barang": "200022",
                "nm_barang": "EPOXY FILLER GREY C580+HARD",
                "satuan": "LTR",
                "harga": "50400",
                "qty": "1",
                "jml_keluar": "5",
                "ket": "-",
                "jabatan": "FIBER OPERATOR"
            },
            "100050": {
                "no_wo": "NV-214010",
                "kd_barang": "100050",
                "nm_barang": "HANDLE BAGASI HATCHBACK+KUNCI",
                "satuan": "PCS",
                "harga": "49000",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "100052": {
                "no_wo": "NV-214010",
                "kd_barang": "100052",
                "nm_barang": "HANDLE DALAM ISUZU PANTHER HS 2256 LH",
                "satuan": "PCS",
                "harga": "19500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "100058": {
                "no_wo": "NV-214010",
                "kd_barang": "100058",
                "nm_barang": "HANDLE LUAR ISUZU ELF LH HS 3192",
                "satuan": "PCS",
                "harga": "46000",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "100062": {
                "no_wo": "NV-214010",
                "kd_barang": "100062",
                "nm_barang": "HANDLE PULL ISUZU HS 2255 HITAM",
                "satuan": "PCS",
                "harga": "9500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "500007": {
                "no_wo": "NV-214010",
                "kd_barang": "500007",
                "nm_barang": "HDLG NON SPON LIGHT GREY A SW F6XJ OLG11",
                "satuan": "MTR",
                "harga": "21500",
                "qty": "12",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "500035": {
                "no_wo": "NV-214010",
                "kd_barang": "500035",
                "nm_barang": "HOT STAMPING",
                "satuan": "SET",
                "harga": "0",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "300018": {
                "no_wo": "NV-214010",
                "kd_barang": "300018",
                "nm_barang": "ISOLASI KERTAS 18 MM \/ 3\/4\"",
                "satuan": "ROL",
                "harga": "1650",
                "qty": "1",
                "jml_keluar": 24,
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "300019": {
                "no_wo": "NV-214010",
                "kd_barang": "300019",
                "nm_barang": "ISOLASI KERTAS 24 MM \/ 1\"",
                "satuan": "ROL",
                "harga": "2700",
                "qty": "2",
                "jml_keluar": 12,
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "600001": {
                "no_wo": "NV-214010",
                "kd_barang": "600001",
                "nm_barang": "KACA BELAKANG NEW LIMAX",
                "satuan": "LBR",
                "harga": "450000",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "700082": {
                "no_wo": "NV-214010",
                "kd_barang": "700082",
                "nm_barang": "KANAL UNP 6,5 X 4 MM X 6 M",
                "satuan": "LJR",
                "harga": "187500",
                "qty": "0.16",
                "jml_keluar": " ",
                "ket": "unp 6.5 7.5 cm x 12 = 90 cm",
                "jabatan": "SUB ASSY & COMPONENT  COORDINATOR"
            },
            "100072": {
                "no_wo": "NV-214010",
                "kd_barang": "100072",
                "nm_barang": "KANCINGAN KLIP DEK PLASTIK GTL",
                "satuan": "SET",
                "harga": "900",
                "qty": "70",
                "jml_keluar": "70",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "100074": {
                "no_wo": "NV-214010",
                "kd_barang": "100074",
                "nm_barang": "KARET JEPIT ENGKEL",
                "satuan": "MTR",
                "harga": "8800",
                "qty": "20",
                "jml_keluar": "20",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "100078": {
                "no_wo": "NV-214010",
                "kd_barang": "100078",
                "nm_barang": "KARET PROTEKTOR HAM I (IRC)",
                "satuan": "MTR",
                "harga": "17050",
                "qty": "10",
                "jml_keluar": "7.5",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "100080": {
                "no_wo": "NV-214010",
                "kd_barang": "100080",
                "nm_barang": "KARET PROTEKTOR HTM (IRC)",
                "satuan": "MTR",
                "harga": "18700",
                "qty": "7",
                "jml_keluar": "5",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "100081": {
                "no_wo": "NV-214010",
                "kd_barang": "100081",
                "nm_barang": "KARET S KUDA",
                "satuan": "MTR",
                "harga": "21000",
                "qty": "5",
                "jml_keluar": "5",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "300031": {
                "no_wo": "NV-214010",
                "kd_barang": "300031",
                "nm_barang": "KARET VANBELT 4\"",
                "satuan": "MTR",
                "harga": "27000",
                "qty": "0.5",
                "jml_keluar": "0.5",
                "ket": "-",
                "jabatan": "LANTAI OPERATOR"
            },
            "500011": {
                "no_wo": "NV-214010",
                "kd_barang": "500011",
                "nm_barang": "KARPET AS 1 MM X 137 CM X 25 M",
                "satuan": "MTR",
                "harga": "38500",
                "qty": "7",
                "jml_keluar": "7",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "300032": {
                "no_wo": "NV-214010",
                "kd_barang": "300032",
                "nm_barang": "KAWAT LAS CIGWELD AUTOCRAFT LW 1-6 0.8 MM",
                "satuan": "ROL",
                "harga": "322500",
                "qty": "5",
                "jml_keluar": 0.4,
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "300034": {
                "no_wo": "NV-214010",
                "kd_barang": "300034",
                "nm_barang": "KAWAT LAS RB CIGWELD SMOOTHCRAFT 3.2 MM",
                "satuan": "KGR",
                "harga": "19500",
                "qty": "2",
                "jml_keluar": "3",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "100088": {
                "no_wo": "NV-214010",
                "kd_barang": "100088",
                "nm_barang": "KAWAT MAINAN",
                "satuan": "PCS",
                "harga": "10500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "100089": {
                "no_wo": "NV-214010",
                "kd_barang": "100089",
                "nm_barang": "KAWAT TANPA PEER",
                "satuan": "PCS",
                "harga": "4500",
                "qty": "3",
                "jml_keluar": "3",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "300035": {
                "no_wo": "NV-214010",
                "kd_barang": "300035",
                "nm_barang": "KERTAS GOSOK FLYING WHEEL NO. 3",
                "satuan": "LBR",
                "harga": "3500",
                "qty": "1",
                "jml_keluar": "7",
                "ket": "Bumper belakang fiber",
                "jabatan": "FIBER OPERATOR"
            },
            "300036": {
                "no_wo": "NV-214010",
                "kd_barang": "300036",
                "nm_barang": "KERTAS GOSOK INAX NO. 1000",
                "satuan": "LBR",
                "harga": "2300",
                "qty": "3",
                "jml_keluar": 17,
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "300037": {
                "no_wo": "NV-214010",
                "kd_barang": "300037",
                "nm_barang": "KERTAS GOSOK INAX NO. 120",
                "satuan": "LBR",
                "harga": "2300",
                "qty": "1",
                "jml_keluar": 25,
                "ket": "Bumper belakang fiber",
                "jabatan": "FIBER OPERATOR"
            },
            "300040": {
                "no_wo": "NV-214010",
                "kd_barang": "300040",
                "nm_barang": "KERTAS GOSOK INAX NO. 240",
                "satuan": "LBR",
                "harga": "2300",
                "qty": "2",
                "jml_keluar": 22,
                "ket": "-",
                "jabatan": "FIBER OPERATOR"
            },
            "300041": {
                "no_wo": "NV-214010",
                "kd_barang": "300041",
                "nm_barang": "KERTAS GOSOK INAX NO. 280",
                "satuan": "LBR",
                "harga": "2300",
                "qty": "2",
                "jml_keluar": 22,
                "ket": "-",
                "jabatan": "FIBER OPERATOR"
            },
            "300042": {
                "no_wo": "NV-214010",
                "kd_barang": "300042",
                "nm_barang": "KERTAS GOSOK INAX NO. 400",
                "satuan": "LBR",
                "harga": "2300",
                "qty": "3",
                "jml_keluar": 22,
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "300043": {
                "no_wo": "NV-214010",
                "kd_barang": "300043",
                "nm_barang": "KERTAS GOSOK INAX NO. 600",
                "satuan": "LBR",
                "harga": "2300",
                "qty": "2",
                "jml_keluar": 17,
                "ket": "-",
                "jabatan": "PAINTING OPERATOR"
            },
            "100090": {
                "no_wo": "NV-214010",
                "kd_barang": "100090",
                "nm_barang": "KEY HOLE PLASTIK HS 6331",
                "satuan": "PCS",
                "harga": "12500",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "100094": {
                "no_wo": "NV-214010",
                "kd_barang": "100094",
                "nm_barang": "KUNCI TUTUP SOLAR DOWN",
                "satuan": "PCS",
                "harga": "0",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "300055": {
                "no_wo": "NV-214010",
                "kd_barang": "300055",
                "nm_barang": "LEM RAJAWALI",
                "satuan": "LTR",
                "harga": "34375",
                "qty": "0.1",
                "jml_keluar": "9",
                "ket": "-",
                "jabatan": "PRIMER OPERATOR"
            },
            "700091": {
                "no_wo": "NV-214010",
                "kd_barang": "700091",
                "nm_barang": "LIST GRILL KECIL",
                "satuan": "LJR",
                "harga": "36000",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "700094": {
                "no_wo": "NV-214010",
                "kd_barang": "700094",
                "nm_barang": "LIST L 1 X 2",
                "satuan": "LJR",
                "harga": "22000",
                "qty": "0.5",
                "jml_keluar": "0.5",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "700095": {
                "no_wo": "NV-214010",
                "kd_barang": "700095",
                "nm_barang": "LIST PANCING",
                "satuan": "LJR",
                "harga": "36000",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "300052": {
                "no_wo": "NV-214010",
                "kd_barang": "300052",
                "nm_barang": "MAJUN KATUN WARNA ( 1 KG = 20 LBR )",
                "satuan": "KGR",
                "harga": "7000",
                "qty": "1",
                "jml_keluar": 1.5,
                "ket": "-",
                "jabatan": "PRIMER OPERATOR"
            },
            "700098": {
                "no_wo": "NV-214010",
                "kd_barang": "700098",
                "nm_barang": "MATA BOR 3 MM",
                "satuan": "PCS",
                "harga": "9300",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700099": {
                "no_wo": "NV-214010",
                "kd_barang": "700099",
                "nm_barang": "MATA BOR 3.5 MM",
                "satuan": "PCS",
                "harga": "10500",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "200036": {
                "no_wo": "NV-214010",
                "kd_barang": "200036",
                "nm_barang": "MINYAK TANAH",
                "satuan": "LTR",
                "harga": "14000",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "ANTI KARAT OPERATOR"
            },
            "500015": {
                "no_wo": "NV-214010",
                "kd_barang": "500015",
                "nm_barang": "OSCAR VITEX RL80\/OLG11 0.7 MM X 137 CM LIGHT GREY",
                "satuan": "MTR",
                "harga": "24500",
                "qty": "4",
                "jml_keluar": "4",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "700131": {
                "no_wo": "NV-214010",
                "kd_barang": "700131",
                "nm_barang": "PIPA KNALPOT ISUZU ELF 50 CM",
                "satuan": "PCS",
                "harga": "80000",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "LANTAI OPERATOR"
            },
            "100112": {
                "no_wo": "NV-214010",
                "kd_barang": "100112",
                "nm_barang": "PLASTIK KLIP DOOR LOCK",
                "satuan": "PCS",
                "harga": "1000",
                "qty": "4",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "300093": {
                "no_wo": "NV-214010",
                "kd_barang": "300093",
                "nm_barang": "PLASTIK MICA 0.1 MM",
                "satuan": "MTR",
                "harga": "3100",
                "qty": "4",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700159": {
                "no_wo": "NV-214010",
                "kd_barang": "700159",
                "nm_barang": "PLAT HITAM 1 MM X 1210 X 2400 MM",
                "satuan": "LBR",
                "harga": "230000",
                "qty": "3.5",
                "jml_keluar": 3.5,
                "ket": "tutup mesin, r accu, trap",
                "jabatan": "SUB ASSY & COMPONENT  COORDINATOR"
            },
            "700160": {
                "no_wo": "NV-214010",
                "kd_barang": "700160",
                "nm_barang": "PLAT HITAM 1.2 MM X 1210 X 2400 MM",
                "satuan": "LBR",
                "harga": "240000",
                "qty": "10",
                "jml_keluar": "10",
                "ket": "untuk kap + plat smp",
                "jabatan": "SUB ASSY & COMPONENT  COORDINATOR"
            },
            "700162": {
                "no_wo": "NV-214010",
                "kd_barang": "700162",
                "nm_barang": "PLAT HITAM 1.8 MM X 1210 X 2400 MM",
                "satuan": "LBR",
                "harga": "325000",
                "qty": "4.25",
                "jml_keluar": 3.75,
                "ket": "untuk lantai + omega floor",
                "jabatan": "SUB ASSY & COMPONENT  COORDINATOR"
            },
            "700166": {
                "no_wo": "NV-214010",
                "kd_barang": "700166",
                "nm_barang": "PLAT PUTIH 0,7 MM X 1210 X 2400 MM",
                "satuan": "LBR",
                "harga": "186000",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "SUB ASSY & COMPONENT  COORDINATOR"
            },
            "700172": {
                "no_wo": "NV-214010",
                "kd_barang": "700172",
                "nm_barang": "RING PEER WL 1\/2",
                "satuan": "PCS",
                "harga": "100",
                "qty": "15",
                "jml_keluar": "15",
                "ket": "-",
                "jabatan": "LANTAI OPERATOR"
            },
            "700176": {
                "no_wo": "NV-214010",
                "kd_barang": "700176",
                "nm_barang": "RING PLAT WP 1\/2",
                "satuan": "PCS",
                "harga": "185",
                "qty": "15",
                "jml_keluar": "15",
                "ket": "-",
                "jabatan": "LANTAI OPERATOR"
            },
            "700178": {
                "no_wo": "NV-214010",
                "kd_barang": "700178",
                "nm_barang": "RING PLAT WP 6 X 13",
                "satuan": "PCS",
                "harga": "35",
                "qty": "40",
                "jml_keluar": "8",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "700179": {
                "no_wo": "NV-214010",
                "kd_barang": "700179",
                "nm_barang": "RING PLAT WP 8 X 17",
                "satuan": "PCS",
                "harga": "150",
                "qty": "10",
                "jml_keluar": "16",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "300109": {
                "no_wo": "NV-214010",
                "kd_barang": "300109",
                "nm_barang": "SEALANT SIMSON 7008 BLACK (290 ML)",
                "satuan": "TUB",
                "harga": "55000",
                "qty": "1",
                "jml_keluar": 5,
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "300074": {
                "no_wo": "NV-214010",
                "kd_barang": "300074",
                "nm_barang": "SEALANT SIMSON 7008 BLACK (600 ML)",
                "satuan": "SSG",
                "harga": "130000",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "800007": {
                "no_wo": "NV-214010",
                "kd_barang": "800007",
                "nm_barang": "SEAT DOUBLE+HEADREST TANAM",
                "satuan": "SET",
                "harga": "688000",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "800005": {
                "no_wo": "NV-214010",
                "kd_barang": "800005",
                "nm_barang": "SEAT DRIVER+ASISTEN RECOVER",
                "satuan": "SET",
                "harga": "258000",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "800009": {
                "no_wo": "NV-214010",
                "kd_barang": "800009",
                "nm_barang": "SEAT QUARTER+HEADREST TANAM",
                "satuan": "SET",
                "harga": "1357000",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "800008": {
                "no_wo": "NV-214010",
                "kd_barang": "800008",
                "nm_barang": "SEAT SINGLE+HEADREST TANAM",
                "satuan": "SET",
                "harga": "344000",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "800006": {
                "no_wo": "NV-214010",
                "kd_barang": "800006",
                "nm_barang": "SEAT TRIPLE+HEADREST TANAM",
                "satuan": "SET",
                "harga": "1034450",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "700185": {
                "no_wo": "NV-214010",
                "kd_barang": "700185",
                "nm_barang": "SEC.73364 LIST TRAP 6.00 M CA3",
                "satuan": "LJR",
                "harga": "87000",
                "qty": "0.33",
                "jml_keluar": "0.5",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "700190": {
                "no_wo": "NV-214010",
                "kd_barang": "700190",
                "nm_barang": "SIKU 25 X 25 X 1.8 MM X 6 M",
                "satuan": "LJR",
                "harga": "37500",
                "qty": "3",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "LANTAI OPERATOR"
            },
            "700193": {
                "no_wo": "NV-214010",
                "kd_barang": "700193",
                "nm_barang": "SIKU 40 X 40 X 3 MM X 6 M",
                "satuan": "LJR",
                "harga": "65000",
                "qty": "2.5",
                "jml_keluar": "2.5",
                "ket": " untuk b accu+floor",
                "jabatan": "LANTAI OPERATOR"
            },
            "700199": {
                "no_wo": "NV-214010",
                "kd_barang": "700199",
                "nm_barang": "SNAP PIN\/SPLIT PEN SHOCK BECKER",
                "satuan": "PCS",
                "harga": "1000",
                "qty": "4",
                "jml_keluar": "4",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "500020": {
                "no_wo": "NV-214010",
                "kd_barang": "500020",
                "nm_barang": "SPON DW 0.5 CM X 100 X 200",
                "satuan": "LBR",
                "harga": "10692.5",
                "qty": "5",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "500021": {
                "no_wo": "NV-214010",
                "kd_barang": "500021",
                "nm_barang": "SPON DW 1 CM X 100 X 200",
                "satuan": "LBR",
                "harga": "19470",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "500023": {
                "no_wo": "NV-214010",
                "kd_barang": "500023",
                "nm_barang": "SPON EVA 10 MM 120 X 230 CM HD 40",
                "satuan": "LBR",
                "harga": "80000",
                "qty": "2",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "500024": {
                "no_wo": "NV-214010",
                "kd_barang": "500024",
                "nm_barang": "SPON EVA 3 MM 120 X 230 CM HD 40",
                "satuan": "LBR",
                "harga": "21000",
                "qty": "3",
                "jml_keluar": "3",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "100128": {
                "no_wo": "NV-214010",
                "kd_barang": "100128",
                "nm_barang": "STABILUS STANDARD 60 CM \/ 600 N",
                "satuan": "PCS",
                "harga": "82000",
                "qty": "2",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "100129": {
                "no_wo": "NV-214010",
                "kd_barang": "100129",
                "nm_barang": "STANG KUNCI GRILL HS 6327 H",
                "satuan": "PCS",
                "harga": "17000",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KUNCI OPERATOR"
            },
            "100132": {
                "no_wo": "NV-214010",
                "kd_barang": "100132",
                "nm_barang": "STIKER \"BRAVO\"",
                "satuan": "PCS",
                "harga": "4200",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "100133": {
                "no_wo": "NV-214010",
                "kd_barang": "100133",
                "nm_barang": "STIKER \"TUGASANDA.COM\"",
                "satuan": "PCS",
                "harga": "13500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "100134": {
                "no_wo": "NV-214010",
                "kd_barang": "100134",
                "nm_barang": "STIKER HARMONY",
                "satuan": "PCS",
                "harga": "4700",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "100138": {
                "no_wo": "NV-214010",
                "kd_barang": "100138",
                "nm_barang": "STIKER TA PANJANG",
                "satuan": "PCS",
                "harga": "6650",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "100140": {
                "no_wo": "NV-214010",
                "kd_barang": "100140",
                "nm_barang": "STIKER TA TRANSPARANT ( K )",
                "satuan": "PCS",
                "harga": "5250",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "700216": {
                "no_wo": "NV-214010",
                "kd_barang": "700216",
                "nm_barang": "TAPPING SCREW 6 X 1 V LION \/ JF",
                "satuan": "PCS",
                "harga": "53",
                "qty": "50",
                "jml_keluar": "50",
                "ket": "-",
                "jabatan": "KACA OPERATOR"
            },
            "700217": {
                "no_wo": "NV-214010",
                "kd_barang": "700217",
                "nm_barang": "TAPPING SCREW 6 X 1\/2 V LION \/ JF",
                "satuan": "PCS",
                "harga": "34",
                "qty": "50",
                "jml_keluar": "50",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "200053": {
                "no_wo": "NV-214010",
                "kd_barang": "200053",
                "nm_barang": "THINER BC 30 %",
                "satuan": "LTR",
                "harga": "35000",
                "qty": "6",
                "jml_keluar": 9,
                "ket": "-",
                "jabatan": "DEMPUL OPERATOR"
            },
            "200055": {
                "no_wo": "NV-214010",
                "kd_barang": "200055",
                "nm_barang": "THINER PU\/BAKAR (HI-GLOSS)",
                "satuan": "LTR",
                "harga": "15500",
                "qty": "2",
                "jml_keluar": 10,
                "ket": "-",
                "jabatan": "FIBER OPERATOR"
            },
            "500030": {
                "no_wo": "NV-214010",
                "kd_barang": "500030",
                "nm_barang": "TRIPLEK 4 MM X 1220 X 2400",
                "satuan": "LBR",
                "harga": "61500",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "PERLENGKAPAN OPERATOR"
            },
            "700081": {
                "no_wo": "NV-214010",
                "kd_barang": "700081",
                "nm_barang": "UNP 50 X 3 MM X 6 M",
                "satuan": "LJR",
                "harga": "115000",
                "qty": "2.5",
                "jml_keluar": "2.5",
                "ket": "-",
                "jabatan": "LANTAI OPERATOR"
            },
            "300096": {
                "no_wo": "NV-214010",
                "kd_barang": "300096",
                "nm_barang": "KANTONG KRESEK",
                "satuan": "PCS",
                "harga": "0",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "STRIP OFF OPERATOR"
            },
            "300095": {
                "no_wo": "NV-214010",
                "kd_barang": "300095",
                "nm_barang": "KANTONG PLASTIK 1 KG",
                "satuan": "PAK",
                "harga": "6300",
                "qty": "2",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "STRIP OFF OPERATOR"
            },
            "700164": {
                "no_wo": "NV-214010",
                "kd_barang": "700164",
                "nm_barang": "PLAT HITAM 3 MM X 1210 X 2400",
                "satuan": "LBR",
                "harga": "625000",
                "qty": "0.01",
                "jml_keluar": " ",
                "ket": "untuk pangkon chassis",
                "jabatan": "SUB ASSY & COMPONENT  COORDINATOR"
            },
            "300001": {
                "no_wo": "NV-214010",
                "kd_barang": "300001",
                "nm_barang": "ACETYLINE",
                "satuan": "TBG",
                "harga": "257500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "700001": {
                "no_wo": "NV-214010",
                "kd_barang": "700001",
                "nm_barang": "ANGIN-ANGIN BAGASI BUS",
                "satuan": "PCS",
                "harga": "63500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "300004": {
                "no_wo": "NV-214010",
                "kd_barang": "300004",
                "nm_barang": "BATU GERINDA 6\"  X 6 A24R",
                "satuan": "PCS",
                "harga": "9600",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "700016": {
                "no_wo": "NV-214010",
                "kd_barang": "700016",
                "nm_barang": "BAUT MUR 10 X 25 (1.25)",
                "satuan": "PCS",
                "harga": "530",
                "qty": "45",
                "jml_keluar": "45",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "700022": {
                "no_wo": "NV-214010",
                "kd_barang": "700022",
                "nm_barang": "BAUT MUR 6 X 15",
                "satuan": "PCS",
                "harga": "119",
                "qty": "8",
                "jml_keluar": "8",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "300007": {
                "no_wo": "NV-214010",
                "kd_barang": "300007",
                "nm_barang": "CO-2",
                "satuan": "TBG",
                "harga": "153500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "700063": {
                "no_wo": "NV-214010",
                "kd_barang": "700063",
                "nm_barang": "ENGSEL FILTER R\/L NEW",
                "satuan": "SET",
                "harga": "16000",
                "qty": "4",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "700064": {
                "no_wo": "NV-214010",
                "kd_barang": "700064",
                "nm_barang": "ENGSEL HATCH BACK KBU",
                "satuan": "PCS",
                "harga": "35000",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "700076": {
                "no_wo": "NV-214010",
                "kd_barang": "700076",
                "nm_barang": "ENGSEL SUZUKI + STOPER",
                "satuan": "PCS",
                "harga": "48500",
                "qty": "2",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "700077": {
                "no_wo": "NV-214010",
                "kd_barang": "700077",
                "nm_barang": "ENGSEL SUZUKI NON STOPER",
                "satuan": "PCS",
                "harga": "48500",
                "qty": "2",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "700086": {
                "no_wo": "NV-214010",
                "kd_barang": "700086",
                "nm_barang": "KUNCI GRILL HS 6327",
                "satuan": "PCS",
                "harga": "17000",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "300061": {
                "no_wo": "NV-214010",
                "kd_barang": "300061",
                "nm_barang": "OKSIGEN (O-2)",
                "satuan": "TBG",
                "harga": "42500",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "700134": {
                "no_wo": "NV-214010",
                "kd_barang": "700134",
                "nm_barang": "PIPA KOTAK 20 X 20 X 1.8 MM X 6 M",
                "satuan": "LJR",
                "harga": "70000",
                "qty": "0.125",
                "jml_keluar": "0.2",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "300094": {
                "no_wo": "NV-214010",
                "kd_barang": "300094",
                "nm_barang": "SEALANT SIMSON 7001 GREY (600ML)",
                "satuan": "SSG",
                "harga": "122500",
                "qty": "0.5",
                "jml_keluar": 2.5,
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700235": {
                "no_wo": "NV-214010",
                "kd_barang": "700235",
                "nm_barang": "TUTUP SOLAR HS 6319 B",
                "satuan": "PCS",
                "harga": "84000",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "BODY WELDING OPERATOR"
            },
            "200008": {
                "no_wo": "NV-214010",
                "kd_barang": "200008",
                "nm_barang": "CAT ZINCHCROMATE GREEN\/ MENI",
                "satuan": "KGR",
                "harga": "27500",
                "qty": "2",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PRIMER OPERATOR"
            },
            "200021": {
                "no_wo": "NV-214010",
                "kd_barang": "200021",
                "nm_barang": "EPOXY FILLER BLUE + HARD",
                "satuan": "LTR",
                "harga": "62500",
                "qty": "6",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PRIMER OPERATOR"
            },
            "300063": {
                "no_wo": "NV-214010",
                "kd_barang": "300063",
                "nm_barang": "PLASTIK MICA 0,07 MM X 137 X 50",
                "satuan": "MTR",
                "harga": "2440",
                "qty": "1.5",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PRIMER OPERATOR"
            },
            "300083": {
                "no_wo": "NV-214010",
                "kd_barang": "300083",
                "nm_barang": "SIKAT MANGKOK",
                "satuan": "PCS",
                "harga": "19000",
                "qty": "2",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PRIMER OPERATOR"
            },
            "200052": {
                "no_wo": "NV-214010",
                "kd_barang": "200052",
                "nm_barang": "THINER A SPESIAL",
                "satuan": "LTR",
                "harga": "13750",
                "qty": "3",
                "jml_keluar": 7,
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "200002": {
                "no_wo": "NV-214010",
                "kd_barang": "200002",
                "nm_barang": "ACTIVATOR XK 206",
                "satuan": "LTR",
                "harga": "225000",
                "qty": "0.15",
                "jml_keluar": 1.62,
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "200012": {
                "no_wo": "NV-214010",
                "kd_barang": "200012",
                "nm_barang": "CLEAR CROMA 3800 S",
                "satuan": "LTR",
                "harga": "225000",
                "qty": "0.5",
                "jml_keluar": 5.5,
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "200026": {
                "no_wo": "NV-214010",
                "kd_barang": "200026",
                "nm_barang": "HARDENER V 250 CATINA",
                "satuan": "LTR",
                "harga": "166320",
                "qty": "0.25",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "200027": {
                "no_wo": "NV-214010",
                "kd_barang": "200027",
                "nm_barang": "HIGH TEMP.THINNER XB 387",
                "satuan": "LTR",
                "harga": "66000",
                "qty": "1.2",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "PAINTING OPERATOR"
            },
            "300046": {
                "no_wo": "NV-214010",
                "kd_barang": "300046",
                "nm_barang": "KORAN (1KG=40LBR)",
                "satuan": "LBR",
                "harga": "100",
                "qty": "20",
                "jml_keluar": 50,
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "300051": {
                "no_wo": "NV-214010",
                "kd_barang": "300051",
                "nm_barang": "MAJUN KATUN PUTIH (1 KG = 8 LBR)",
                "satuan": "KGR",
                "harga": "19000",
                "qty": "1",
                "jml_keluar": "0.5",
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "200043": {
                "no_wo": "NV-214010",
                "kd_barang": "200043",
                "nm_barang": "PU THINER 708 CATINA",
                "satuan": "LTR",
                "harga": "46655",
                "qty": "0.15",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "200047": {
                "no_wo": "NV-214010",
                "kd_barang": "200047",
                "nm_barang": "SHADOW SILVER MET D600.000801 DP",
                "satuan": "LTR",
                "harga": "190000",
                "qty": "0.5",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PAINTING OPERATOR"
            },
            "200066": {
                "no_wo": "NV-214010",
                "kd_barang": "200066",
                "nm_barang": "SILVER MET TADAB9-0690 DP (EX. CP.0404)",
                "satuan": "LTR",
                "harga": "190000",
                "qty": "5",
                "jml_keluar": "5",
                "ket": "-",
                "jabatan": "PAINTING OPERATOR"
            },
            "200019": {
                "no_wo": "NV-214010",
                "kd_barang": "200019",
                "nm_barang": "TACK CLOTH \/ TAKRAK",
                "satuan": "LBR",
                "harga": "10000",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "PAINTING OPERATOR"
            },
            "200064": {
                "no_wo": "NV-214010",
                "kd_barang": "200064",
                "nm_barang": "THINER AB 385",
                "satuan": "LTR",
                "harga": "52000",
                "qty": "4",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "PAINTING OPERATOR"
            },
            "200054": {
                "no_wo": "NV-214010",
                "kd_barang": "200054",
                "nm_barang": "THINER DX-34 0605.DX34",
                "satuan": "LTR",
                "harga": "57200",
                "qty": "4",
                "jml_keluar": "4",
                "ket": "-",
                "jabatan": "PAINTING OPERATOR"
            },
            "200058": {
                "no_wo": "NV-214010",
                "kd_barang": "200058",
                "nm_barang": "VISION CLEAR BSR CATINA",
                "satuan": "LTR",
                "harga": "127829",
                "qty": "0.5",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "200023": {
                "no_wo": "NV-214010",
                "kd_barang": "200023",
                "nm_barang": "FAST COMPOUND",
                "satuan": "KGR",
                "harga": "185000",
                "qty": "0.1",
                "jml_keluar": "0.1",
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "300020": {
                "no_wo": "NV-214010",
                "kd_barang": "300020",
                "nm_barang": "ISOLASI KERTAS 6 MM",
                "satuan": "ROL",
                "harga": "1725",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "300029": {
                "no_wo": "NV-214010",
                "kd_barang": "300029",
                "nm_barang": "KALENG KOSONG\/SEREP",
                "satuan": "PCS",
                "harga": "2000",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "300039": {
                "no_wo": "NV-214010",
                "kd_barang": "300039",
                "nm_barang": "KERTAS GOSOK INAX NO. 1500",
                "satuan": "LBR",
                "harga": "2600",
                "qty": "3",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "200028": {
                "no_wo": "NV-214010",
                "kd_barang": "200028",
                "nm_barang": "LACQUER BLACK DOFF",
                "satuan": "KGR",
                "harga": "55000",
                "qty": "0.5",
                "jml_keluar": "0.5",
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "200049": {
                "no_wo": "NV-214010",
                "kd_barang": "200049",
                "nm_barang": "SOLAR",
                "satuan": "LTR",
                "harga": "6900",
                "qty": "15",
                "jml_keluar": "5",
                "ket": "-",
                "jabatan": "TOUCH UP OPERATOR"
            },
            "": {
                "no_wo": "NV-214010",
                "kd_barang": null,
                "nm_barang": null,
                "satuan": null,
                "harga": null,
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700026": {
                "no_wo": "NV-214010",
                "kd_barang": "700026",
                "nm_barang": "BAUT MUR 6 X 25",
                "satuan": "PCS",
                "harga": "139",
                "qty": "10",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700038": {
                "no_wo": "NV-214010",
                "kd_barang": "700038",
                "nm_barang": "BAUT MUR 8 X 30",
                "satuan": "PCS",
                "harga": "340",
                "qty": "10",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "100030": {
                "no_wo": "NV-214010",
                "kd_barang": "100030",
                "nm_barang": "DOP ENGKLE KECIL 12 V FLOSER",
                "satuan": "PCS",
                "harga": "9500",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400004": {
                "no_wo": "NV-214010",
                "kd_barang": "400004",
                "nm_barang": "HEATSTRING\/SLANG BAKAR 10 MM",
                "satuan": "MTR",
                "harga": "7000",
                "qty": "0.5",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400005": {
                "no_wo": "NV-214010",
                "kd_barang": "400005",
                "nm_barang": "HEATSTRING\/SLANG BAKAR 4 MM",
                "satuan": "MTR",
                "harga": "4000",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "300024": {
                "no_wo": "NV-214010",
                "kd_barang": "300024",
                "nm_barang": "ISOLASI PLASTIK HITAM",
                "satuan": "ROL",
                "harga": "6500",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400040": {
                "no_wo": "NV-214010",
                "kd_barang": "400040",
                "nm_barang": "KABEL ACCU 10 MM \/ 3,75 M",
                "satuan": "PCS",
                "harga": "285000",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400009": {
                "no_wo": "NV-214010",
                "kd_barang": "400009",
                "nm_barang": "KABEL ENGKEL\/ NYAF 0.75 MM",
                "satuan": "MTR",
                "harga": "1400",
                "qty": "2",
                "jml_keluar": 150,
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400010": {
                "no_wo": "NV-214010",
                "kd_barang": "400010",
                "nm_barang": "KABEL ENGKEL\/NYAF 1.5 MM",
                "satuan": "MTR",
                "harga": "2700",
                "qty": "50",
                "jml_keluar": "50",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "100077": {
                "no_wo": "NV-214010",
                "kd_barang": "100077",
                "nm_barang": "KARET PELIPIT VESPA",
                "satuan": "MTR",
                "harga": "1200",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "100086": {
                "no_wo": "NV-214010",
                "kd_barang": "100086",
                "nm_barang": "KARET TUTUP LUBANG NO. 5",
                "satuan": "PCS",
                "harga": "850",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "100150": {
                "no_wo": "NV-214010",
                "kd_barang": "100150",
                "nm_barang": "KARET TUTUP LUBANG SLENGER",
                "satuan": "PCS",
                "harga": "1000",
                "qty": "2",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "300049": {
                "no_wo": "NV-214010",
                "kd_barang": "300049",
                "nm_barang": "KUAS 2 1\/2\"",
                "satuan": "PCS",
                "harga": "5000",
                "qty": "2",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "FIBER OPERATOR"
            },
            "100098": {
                "no_wo": "NV-214010",
                "kd_barang": "100098",
                "nm_barang": "LAMPU PLATE NO KEONG",
                "satuan": "PCS",
                "harga": "5000",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "100100": {
                "no_wo": "NV-214010",
                "kd_barang": "100100",
                "nm_barang": "LED ROLL BIRU @ 5 M \/ 12 V",
                "satuan": "ROL",
                "harga": "230000",
                "qty": "0.2",
                "jml_keluar": "0.2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "100101": {
                "no_wo": "NV-214010",
                "kd_barang": "100101",
                "nm_barang": "LED ROLL PUTIH @ 5 M \/ 12 V",
                "satuan": "ROL",
                "harga": "230000",
                "qty": "0.12",
                "jml_keluar": "1",
                "ket": "Untuk modif omega",
                "jabatan": "MEKANIK OPERATOR"
            },
            "300060": {
                "no_wo": "NV-214010",
                "kd_barang": "300060",
                "nm_barang": "MUD GUARD ( 70 X 110 )",
                "satuan": "LBR",
                "harga": "15000",
                "qty": "0.5",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700114": {
                "no_wo": "NV-214010",
                "kd_barang": "700114",
                "nm_barang": "MUR RIVET 6 MM\/NUT SHEET M-6",
                "satuan": "PCS",
                "harga": "1100",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400013": {
                "no_wo": "NV-214010",
                "kd_barang": "400013",
                "nm_barang": "PLASTIK SKUN BULAT",
                "satuan": "PCS",
                "harga": "500",
                "qty": "20",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400014": {
                "no_wo": "NV-214010",
                "kd_barang": "400014",
                "nm_barang": "PLASTIK SKUN GEPENG",
                "satuan": "PCS",
                "harga": "250",
                "qty": "20",
                "jml_keluar": "20",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "100113": {
                "no_wo": "NV-214010",
                "kd_barang": "100113",
                "nm_barang": "REFF REARGUARD EXCELENT MATA KUCING",
                "satuan": "PCS",
                "harga": "6750",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700175": {
                "no_wo": "NV-214010",
                "kd_barang": "700175",
                "nm_barang": "RING PEER WL 8",
                "satuan": "PCS",
                "harga": "35",
                "qty": "50",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700177": {
                "no_wo": "NV-214010",
                "kd_barang": "700177",
                "nm_barang": "RING PLAT WP 10 X 23",
                "satuan": "PCS",
                "harga": "190",
                "qty": "10",
                "jml_keluar": "45",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "100120": {
                "no_wo": "NV-214010",
                "kd_barang": "100120",
                "nm_barang": "ROOM LAMP OMEGA HS 1270",
                "satuan": "PCS",
                "harga": "54000",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400019": {
                "no_wo": "NV-214010",
                "kd_barang": "400019",
                "nm_barang": "SEKRING GELAS 15 A AH FLOSER",
                "satuan": "PCS",
                "harga": "3000",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "300076": {
                "no_wo": "NV-214010",
                "kd_barang": "300076",
                "nm_barang": "SELONGSONG KABEL 3\/16\"",
                "satuan": "MTR",
                "harga": "960",
                "qty": "10",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "300078": {
                "no_wo": "NV-214010",
                "kd_barang": "300078",
                "nm_barang": "SELONGSONG KABEL 3\/8\"",
                "satuan": "MTR",
                "harga": "1650",
                "qty": "20",
                "jml_keluar": "20",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "200048": {
                "no_wo": "NV-214010",
                "kd_barang": "200048",
                "nm_barang": "SILICON CAIR",
                "satuan": "BTL",
                "harga": "43500",
                "qty": "0.2",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400022": {
                "no_wo": "NV-214010",
                "kd_barang": "400022",
                "nm_barang": "SKAKEL PIANO HIJAU",
                "satuan": "PCS",
                "harga": "6500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400023": {
                "no_wo": "NV-214010",
                "kd_barang": "400023",
                "nm_barang": "SKAKEL PIANO MERAH",
                "satuan": "PCS",
                "harga": "6500",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400025": {
                "no_wo": "NV-214010",
                "kd_barang": "400025",
                "nm_barang": "SKUN BULAT (+)",
                "satuan": "PCS",
                "harga": "1500",
                "qty": "20",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400024": {
                "no_wo": "NV-214010",
                "kd_barang": "400024",
                "nm_barang": "SKUN BULAT (-)",
                "satuan": "PCS",
                "harga": "1250",
                "qty": "20",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400027": {
                "no_wo": "NV-214010",
                "kd_barang": "400027",
                "nm_barang": "SKUN GEPENG ( + ) \/ L UTILUX",
                "satuan": "PCS",
                "harga": "925",
                "qty": "40",
                "jml_keluar": "40",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400026": {
                "no_wo": "NV-214010",
                "kd_barang": "400026",
                "nm_barang": "SKUN GEPENG ( - ) \/ P UTILUX",
                "satuan": "PCS",
                "harga": "1000",
                "qty": "40",
                "jml_keluar": "40",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400029": {
                "no_wo": "NV-214010",
                "kd_barang": "400029",
                "nm_barang": "SKUN MASSA 6 MM",
                "satuan": "PCS",
                "harga": "1200",
                "qty": "10",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400038": {
                "no_wo": "NV-214010",
                "kd_barang": "400038",
                "nm_barang": "SOCKET ISI 6 ( + \/ L )",
                "satuan": "PCS",
                "harga": "950",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400037": {
                "no_wo": "NV-214010",
                "kd_barang": "400037",
                "nm_barang": "SOCKET ISI 6 ( - \/ P )",
                "satuan": "PCS",
                "harga": "950",
                "qty": "2",
                "jml_keluar": "2",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "100152": {
                "no_wo": "NV-214010",
                "kd_barang": "100152",
                "nm_barang": "STOP LAMP HARMONY R\/L",
                "satuan": "SET",
                "harga": "1150000",
                "qty": "1",
                "jml_keluar": "1",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "300090": {
                "no_wo": "NV-214010",
                "kd_barang": "300090",
                "nm_barang": "TALI KABEL\/PENGIKAT KABEL 20 CM",
                "satuan": "PCS",
                "harga": "300",
                "qty": "40",
                "jml_keluar": "40",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700249": {
                "no_wo": "NV-214010",
                "kd_barang": "700249",
                "nm_barang": "TAPPING SCREW 5 X 20",
                "satuan": "PCS",
                "harga": "0",
                "qty": "40",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700218": {
                "no_wo": "NV-214010",
                "kd_barang": "700218",
                "nm_barang": "TAPPING SCREW 6 X 3\/4 V LION \/ JF",
                "satuan": "PCS",
                "harga": "42",
                "qty": "200",
                "jml_keluar": 120,
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700224": {
                "no_wo": "NV-214010",
                "kd_barang": "700224",
                "nm_barang": "TAPPING SCREW 8 X 3\/4 V LION \/ JF",
                "satuan": "PCS",
                "harga": "62",
                "qty": "40",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700225": {
                "no_wo": "NV-214010",
                "kd_barang": "700225",
                "nm_barang": "TAPPING SCREW TSAT 4 X 12 OCP HITAM",
                "satuan": "PCS",
                "harga": "145",
                "qty": "50",
                "jml_keluar": "20",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "700226": {
                "no_wo": "NV-214010",
                "kd_barang": "700226",
                "nm_barang": "TAPPING SCREW TSAT 4 X 20 OCP HITAM",
                "satuan": "PCS",
                "harga": "160",
                "qty": "50",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "400039": {
                "no_wo": "NV-214010",
                "kd_barang": "400039",
                "nm_barang": "TEMPAT SEKRING",
                "satuan": "PCS",
                "harga": "2500",
                "qty": "2",
                "jml_keluar": " ",
                "ket": "-",
                "jabatan": "MEKANIK OPERATOR"
            },
            "200009": {
                "no_wo": "NV-214010",
                "kd_barang": "200009",
                "nm_barang": "CATALIST",
                "satuan": "KGR",
                "harga": "72500",
                "qty": "0.03",
                "jml_keluar": " ",
                "ket": "Alis cover plat nomor fiber",
                "jabatan": "FIBER OPERATOR"
            },
            "200013": {
                "no_wo": "NV-214010",
                "kd_barang": "200013",
                "nm_barang": "COBALT FIBER",
                "satuan": "KGR",
                "harga": "225000",
                "qty": "0.003",
                "jml_keluar": " ",
                "ket": "Mangkoan handle dalam",
                "jabatan": "FIBER OPERATOR"
            },
            "200034": {
                "no_wo": "NV-214010",
                "kd_barang": "200034",
                "nm_barang": "E-GLASS FIBER WOVEN ROVING 600 (MATT TIKAR 600)",
                "satuan": "KGR",
                "harga": "19000",
                "qty": "0.8",
                "jml_keluar": " ",
                "ket": "Bumper belakang fiber",
                "jabatan": "FIBER OPERATOR"
            },
            "300050": {
                "no_wo": "NV-214010",
                "kd_barang": "300050",
                "nm_barang": "KUAS 4\"",
                "satuan": "PCS",
                "harga": "10000",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "Bumper belakang fiber",
                "jabatan": "FIBER OPERATOR"
            },
            "200033": {
                "no_wo": "NV-214010",
                "kd_barang": "200033",
                "nm_barang": "MATT 300",
                "satuan": "KGR",
                "harga": "24970",
                "qty": "0.005",
                "jml_keluar": " ",
                "ket": "Mangkoan handle dalam",
                "jabatan": "FIBER OPERATOR"
            },
            "200037": {
                "no_wo": "NV-214010",
                "kd_barang": "200037",
                "nm_barang": "PIGMEN HITAM",
                "satuan": "LTR",
                "harga": "72500",
                "qty": "0.025",
                "jml_keluar": " ",
                "ket": "Cover safety bely untuk 2 pcs",
                "jabatan": "FIBER OPERATOR"
            },
            "200038": {
                "no_wo": "NV-214010",
                "kd_barang": "200038",
                "nm_barang": "PIGMEN PUTIH",
                "satuan": "KGR",
                "harga": "90000",
                "qty": "0.005",
                "jml_keluar": " ",
                "ket": "Mangkoan handle dalam",
                "jabatan": "FIBER OPERATOR"
            },
            "200045": {
                "no_wo": "NV-214010",
                "kd_barang": "200045",
                "nm_barang": "RESIN 157 JUSTUS",
                "satuan": "KGR",
                "harga": "27500",
                "qty": "0.8",
                "jml_keluar": " ",
                "ket": "Cover safety bely untuk 2 pcs",
                "jabatan": "FIBER OPERATOR"
            },
            "300065": {
                "no_wo": "NV-214010",
                "kd_barang": "300065",
                "nm_barang": "SABUN COLEK",
                "satuan": "PCS",
                "harga": "850",
                "qty": "1",
                "jml_keluar": " ",
                "ket": "Bumper belakang fiber",
                "jabatan": "FIBER OPERATOR"
            },
            "200050": {
                "no_wo": "NV-214010",
                "kd_barang": "200050",
                "nm_barang": "STERIN MONOMER FIBER",
                "satuan": "KG",
                "harga": "25000",
                "qty": "0.1",
                "jml_keluar": " ",
                "ket": "Bumper belakang fiber",
                "jabatan": "FIBER OPERATOR"
            },
            "200051": {
                "no_wo": "NV-214010",
                "kd_barang": "200051",
                "nm_barang": "TEPUNG HDK",
                "satuan": "KGR",
                "harga": "140000",
                "qty": "0.01",
                "jml_keluar": " ",
                "ket": "Mangkoan handle dalam",
                "jabatan": "FIBER OPERATOR"
            },
            "200059": {
                "no_wo": "NV-214010",
                "kd_barang": "200059",
                "nm_barang": "WAX MIRROR GLAZE\/POLISHING(FIBER)",
                "satuan": "KLG",
                "harga": "125000",
                "qty": "0.03",
                "jml_keluar": " ",
                "ket": "Cover safety bely untuk 2 pcs",
                "jabatan": "FIBER OPERATOR"
            }
        },
        "totalItems": 198
    };

    $scope.tmpBomModel = function () {
        var param = $scope.form.no_wo;
//        $scope.tableStateRef = tableState;
//        var offset = tableState.pagination.start || 0;
//        var limit = tableState.pagination.number || 10;
        Data.get('bom/rekaprealisasimodel', param).then(function (data) {
            $scope.r_bomModel = data.data;

            for (var j = 0; j < 50; j++) {
                $scope.displayed2.push(createRandomItem());
            }
//            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
//        $scope.isLoading = false;
    };

    var nameList = ['Pierre', 'Pol', 'Jacques', 'Robert', 'Elisa'],
            familyName = ['Dupont', 'Germain', 'Delcourt', 'bjip', 'Menez'];

    function createRandomItem() {
        var
                firstName = nameList[Math.floor(Math.random() * 4)],
                lastName = familyName[Math.floor(Math.random() * 4)],
                age = Math.floor(Math.random() * 100),
                email = firstName + lastName + '@whatever.com',
                balance = Math.random() * 3000;

        return{
            firstName: firstName,
            lastName: lastName,
            age: age,
            email: email,
            balance: balance
        };
    }


    $scope.displayed2 = [];



})