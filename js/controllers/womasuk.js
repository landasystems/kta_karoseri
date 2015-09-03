app.controller('womasukCtrl', function($scope, Data, toaster, FileUploader) {
    var kode_unik = new Date().getUTCMilliseconds() + "" + (Math.floor(Math.random() * (20 - 10 + 1)) + 10);
    var uploader = $scope.uploader = new FileUploader({
        url: 'img/upload.php?folder=womasuk&kode=' + kode_unik,
        queueLimit: 1,
        removeAfterUpload: true,
    });

    uploader.filters.push({
        name: 'imageFilter',
        fn: function(item) {
            var type = '|' + item.type.slice(item.type.lastIndexOf('/') + 1) + '|';
            return '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
        }
    });
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
    $scope.open2 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened2 = true;
    };
    $scope.open3 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened3 = true;
    };
    $scope.open4 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened4 = true;
    };
    Data.post('womasuk/warna').then(function(data) {
        $scope.list_warna = data.warna;
    });

    $scope.cariSpk = function($query) {
        if ($query.length >= 3) {
            Data.get('spkaroseri/cari', {nama: $query}).then(function(data) {
                $scope.kdSpk = data.data;
            });
        }
    };
    $scope.cariNowo = function($query) {
        if ($query.length >= 3) {
            Data.get('womasuk/copy', {nama: $query}).then(function(data) {
                $scope.list_nowo = data.data;
            });
        }
    };
    $scope.copyData = function(nowo, nowo_baru) {
        $scope.form = nowo;
//        form.kode_asli = nowo.no_wo;
        Data.post('womasuk/view/', nowo).then(function(data) {
            $scope.form = data.data;
            $scope.eks = data.eksterior;
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
            $scope.form.no_wo = data.code;

            $scope.form.tgl_terima = data.data.titipan.tgl_terima;
            $scope.form.no_chassis = data.data.titipan.no_chassis;
            $scope.form.no_mesin = data.data.titipan.no_mesin;
            $scope.form.warna = data.data.titipan.warna.warna;
            


        });
    };
    $scope.cariTitipan = function($query) {
        if ($query.length >= 3) {
            Data.get('serahterimain/cari', {nama: $query}).then(function(data) {
                $scope.titip = data.data;
            });
        }
    };

   
    $scope.getTitipan = function(form, items) {
        form.kd_titipan = items.kd_titipan;
        Data.post('womasuk/getsti/', form).then(function(data) {
            form.tgl_terima = data.sti.tgl_terima;
            form.no_chassis = data.sti.no_chassis;
            form.no_mesin = data.sti.no_mesin;
            form.warna = data.sti.warna;

        });

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

        Data.get('womasuk', param).then(function(data) {
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
        $scope.form.tgl_kontrak = new Date();
        $scope.form.in_spk_marketing = new Date();
        $scope.eks = {};
        $scope.inter = {};

    };
    $scope.copy = function(form) {
        $scope.is_copy = true;
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Salin Data";
        $scope.form.tgl_keluar = new Date();
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
        $scope.selected(form);
        $scope.form = {};
        $scope.form.tgl_keluar = new Date(form.tgl_keluar);
        $scope.form.tgl_kontrak = new Date(form.tgl_kontrak);
        $scope.form.in_spk_marketing = new Date(form.in_spk_marketing);
        $scope.eks = {};
        $scope.inter = {};
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form.id);
    };
    $scope.save = function(form, eks, inter) {
        if ($scope.uploader.queue.length > 0) {
            $scope.uploader.uploadAll();
            form.foto = kode_unik + "-" + $scope.uploader.queue[0].file.name;
        } else {
            form.foto = '';
        }
        var data = {
            womasuk: form,
            eksterior: eks,
            interior: inter,
        };
        var url = ($scope.is_create == true) ? 'womasuk/create' : 'womasuk/update/';
        Data.post(url, data).then(function(result) {
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
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.post('womasuk/delete/', row).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.selected = function(form, no_wo_baru) {
        Data.post('womasuk/select/', form).then(function(data) {
            $scope.form = data.data;
            $scope.eks = data.eksterior;
            $scope.inter = data.interior[0];
            $scope.form.warna = data.det.warna;
            $scope.form.no_wo = data.det.no_wo;
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

            $scope.form.tgl_terima = data.data.titipan.tgl_terima;
            $scope.form.no_chassis = data.data.titipan.no_chassis;
            $scope.form.no_mesin = data.data.titipan.no_mesin;
            $scope.form.warna = data.data.titipan.warna.warna;
            


        });
    }
     $scope.getSpk = function(form, items) {
        form.no_spk = items.no_spk;

        Data.post('womasuk/getspk/', form).then(function(data) {
            form.merk = data.spk.merk;
            form.model_chassis = data.spk.model_chassis;
            form.jenis = data.spk.jenis;
            form.tipe = data.spk.tipe;
            form.model = data.spk.model;
            form.sales = data.spk.nama;
            form.no_wo = data.code;
            form.customer = data.spk.nm_customer;
            form.pemilik = data.spk.nm_pemilik;
            
            // INTERIOR
             $scope.list_plat = data.eksterior.plat;
             $scope.list_ventilasi = data.eksterior.ventilasi;
             $scope.list_spion = data.eksterior.spion;
             $scope.list_kdepan = data.eksterior.kdepan;
             $scope.list_kbelakang = data.eksterior.kbelakang;
             $scope.list_ksamping = data.eksterior.ksamping;
             $scope.list_ldepan = data.eksterior.ldepan;
             $scope.list_lbelakang = data.eksterior.lbelakang;
             $scope.list_pdepan = data.eksterior.pdepan;
             $scope.list_ppenumpang = data.eksterior.ppenumpang;
             $scope.list_pbagasi = data.eksterior.pbagasi;
             $scope.list_pbelakang = data.eksterior.pbelakang;
             $scope.list_wyper = data.eksterior.wyper;
             $scope.list_akarat = data.eksterior.akarat;
             $scope.list_strip = data.eksterior.strip;
             $scope.list_letter = data.eksterior.letter;
             
             // INTERIOR
             $scope.list_plavon = data.interior.plavon;
             $scope.list_trimming = data.interior.trimming;
             $scope.list_duchting = data.interior.duchting;
             $scope.list_lplavon = data.interior.lplavon;
             $scope.list_lantai = data.interior.lantai;
             $scope.list_karpet = data.interior.karpet;
             $scope.list_seat1 = data.interior.seat1;
             $scope.list_seat2 = data.interior.seat2;
             $scope.list_seat3 = data.interior.seat3;
             $scope.list_seat4 = data.interior.seat4;
             $scope.list_seat5 = data.interior.seat5;
             $scope.list_total_seat = data.interior.total_seat;
             $scope.list_ac = data.interior.ac;
             
             $scope.list_bagasi_dalam = data.interior.bagasi_dalam;
             $scope.list_bagasi_dalam = data.interior.bagasi_dalam;
             $scope.list_dashboard = data.interior.dashboard;
             $scope.list_peredam = data.interior.peredam;
             $scope.list_pegangan_atas = data.interior.pegangan_tangan_atas;
             $scope.list_pengaman_penumpang = data.interior.pengaman_penumpang;
             $scope.list_pengaman_kaca = data.interior.pengaman_kaca;
             $scope.list_pengaman_driver = data.interior.pengaman_driver;
             $scope.list_gordyn = data.interior.gordyn;
             $scope.list_driver_fan = data.interior.driver_fan;
             $scope.list_radio_tape = data.interior.radio_tape;
             $scope.list_spek_seat = data.interior.spek_seat;
             $scope.list_driver_seat = data.interior.driver_seat;
             $scope.list_cover_seat = data.interior.cover_seat;
             $scope.list_optional_seat = data.interior.optional_seat;
              
             console.log(data);


        });
    };
     $scope.tagTransformplat = function(newTag) {
        var item = {
            plat_body: newTag,
        };

        return item;
    };
     $scope.tagTransformventilasi = function(newTag) {
        var item = {
            ventilasi_atas: newTag,
        };

        return item;
    };
     $scope.tagTransformkdepan = function(newTag) {
        var item = {
            kaca_depan: newTag,
        };

        return item;
    };
     $scope.tagTransformkbelakang = function(newTag) {
        var item = {
            kaca_belakang: newTag,
        };

        return item;
    };
     $scope.tagTransformksamping = function(newTag) {
        var item = {
            kaca_samping: newTag,
        };

        return item;
    };
     $scope.tagTransformldepan = function(newTag) {
        var item = {
            lampu_depan: newTag,
        };

        return item;
    };
     $scope.tagTransformlbelakang = function(newTag) {
        var item = {
            lampu_belakang: newTag,
        };

        return item;
    };
     $scope.tagTransformpdepan = function(newTag) {
        var item = {
            pintu_depan: newTag,
        };

        return item;
    };
     $scope.tagTransformppenumpang = function(newTag) {
        var item = {
            pintu_penumpang: newTag,
        };

        return item;
    };
     $scope.tagTransformpbagasi = function(newTag) {
        var item = {
            pintu_bagasi_samping: newTag,
        };

        return item;
    };
     $scope.tagTransformpbelakang = function(newTag) {
        var item = {
            pintu_balakang: newTag,
        };

        return item;
    };
     $scope.tagTransformwyper = function(newTag) {
        var item = {
            wyper_set: newTag,
        };

        return item;
    };
     $scope.tagTransformstrip = function(newTag) {
        var item = {
            strip: newTag,
        };

        return item;
    };
     $scope.tagTransformletter = function(newTag) {
        var item = {
            letter: newTag,
        };

        return item;
    };
     $scope.tagTransformplavon = function(newTag) {
        var item = {
            plavon: newTag,
        };

        return item;
    };


})
