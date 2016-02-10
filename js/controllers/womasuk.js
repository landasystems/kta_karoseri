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
           var x = '|jpg|png|jpeg|bmp|gif|'.indexOf(type) !== -1;
            if(!x) {
                toaster.pop('error', "Jenis gambar tidak sesuai");
            }
            return x;
        }
    });
    
    uploader.filters.push({
        name: 'sizeFilter', 
        fn: function (item) {
            var xz = item.size <= 1048576;
            if(!xz) {
                toaster.pop('error', "Ukuran gambar tidak boleh lebih dari 1 MB");
            }
        }
    });
    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_create = false;
    $scope.form = {};

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
    // PRINT MASTER
    $scope.small = function () {
        Data.get('womasuk/small').then(function (data) {
            window.open('api/web/womasuk/small?print=true', "");
        });
            
        
    }
    $scope.mini = function () {
            Data.get('womasuk/mini').then(function (data) {
            window.open('api/web/womasuk/mini?print=true', "");
        });
        
    }
//    TUTUP PRINT MASTER
    
    Data.post('womasuk/warna').then(function(data) {
        $scope.list_warna = data.warna;
    });
     Data.get('proyek/list').then(function(data) {
        $scope.proyeklist = data.proyek;
    });
//    $scope.getnw = function (form) {
//       
//          Data.get('womasuk/proyek?kd=' + form).then(function (data) {
//               console.log(data);
//            $scope.form.no_wo = data.code;
//        });
//    };
    $scope.getnw = function (kode) {
//        var kods = $scope.form.kd_titipan;
        var buat = $scope.is_create;
        if (buat == true) {
            Data.get('womasuk/proyek', {kd: kode}).then(function (data) {
                $scope.form.no_wo = data.data;
            });
        } else {
             Data.get('womasuk/proyek', {kd: kode}).then(function (data) {
                $scope.form.no_wo_baru = data.data;
            });
        }
    }

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
//        $scope.form.in_spk_marketing = new Date();
        $scope.eks = {};
        $scope.inter = {};

    };
    $scope.copy = function(form) {
        $scope.is_copy = true;
        $scope.is_create = true;
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.formtitle = "Salin Data";
//        $scope.form.tgl_keluar = new Date();
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
        $scope.form.tgl_kontrak = new Date(form.tgl_kontrak);
        $scope.eks = {};
        $scope.inter = {};
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.no_wo;
        $scope.form = form;
        $scope.selected(form);
    };
    $scope.buka = function(form) {
        console.log(form);
        if (confirm("Apa anda yakin akan memproses item ini ?")) {
            Data.post('womasuk/bukaprint/', form).then(function(result) {
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
    
    $scope.save = function(form, eks, inter) {
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
        $scope.callServer(tableStateRef);
    };
    $scope.delete = function(row) {
//        alert(row);
        if (confirm("Menghapus data akan berpengaruh terhadap transaksi lain yang berhubungan, apakah anda yakin ?")) {
            Data.post('womasuk/delete/', row).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };
    $scope.print = function (no_wo) {
        Data.get('womasuk/sqlprint/',  {kd: no_wo}).then(function (data) {
            window.open('api/web/womasuk/print');
        });
    };
    $scope.copyData = function(nowo, nowo_baru) {
        $scope.form = nowo;
        Data.post('womasuk/view/', nowo).then(function(data) {
            $scope.form = data.data;
            $scope.eks = data.eksterior;
            $scope.inter = data.interior[0];
            $scope.form.warna = data.det.warna;
//            $scope.form.no_spk = data;
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
            $scope.form.no_spk = data.data.no_spk.as;
            $scope.form.no_wo = data.code;

            $scope.form.tgl_terima = data.data.titipan.tgl_terima;
            $scope.form.no_chassis = data.data.titipan.no_chassis;
            $scope.form.no_mesin = data.data.titipan.no_mesin;
            $scope.form.warna = data.data.titipan.warna.warna;
            $scope.selected(nowo);


        });

    };
    $scope.selected = function(form, no_wo_baru) {
        Data.post('womasuk/select/', form).then(function(data) {
            $scope.form = data.data;

            if (Object.keys(data.eksterior).length > 0) {
                $scope.eks = data.eksterior;
            }
            if (Object.keys(data.interior).length > 0) {
                $scope.inter = data.interior;
               
            }
            console.log(data.det);
//            $scope.form.tgl = data.in_spk_marketing;
            $scope.form.warna = data.det.warna;
            $scope.form.no_wo = data.det.no_wo;
//            $scope.form.kode = data.kode_proyek;
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
            $scope.form.no_spk = data.data.no_spk.as;

            $scope.form.tgl_terima = data.data.titipan.tgl_terima;
            $scope.form.no_chassis = data.data.titipan.no_chassis;
            $scope.form.no_mesin = data.data.titipan.no_mesin;
            $scope.form.warna = data.data.titipan.warna.warna;

            $scope.getSpk(form);
           


        });
    }
    $scope.getSpk = function(form, items) {
        
        Data.post('womasuk/getspk/', form).then(function(data) {
            form.merk = data.spk.merk;
            form.model_chassis = data.spk.model_chassis;
            form.jenis = data.spk.jenis;
            
            form.in_spk_marketing = data.spk.tgl;
            form.tipe = data.spk.tipe;
            form.model = data.spk.model;
            form.sales = data.spk.nama;
            form.customer = data.spk.nm_customer;
            form.pemilik = data.spk.nm_pemilik;

            // EXTERIOR
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
            $scope.list_seat1 = data.interior.seat_satu;
            $scope.list_seat2 = data.interior.seat_dua;
            $scope.list_seat3 = data.interior.seat_tiga;
            $scope.list_seat4 = data.interior.seat_empat;
            $scope.list_seat5 = data.interior.seat_lima;
            $scope.list_total_seat = data.interior.total_seat;
            $scope.list_ac = data.interior.ac;

            $scope.list_bagasi_dalam = data.interior.bagasi_dalam;
//            $scope.list_bagasi_dalam = data.interior.bagasi_dalam;
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



        });
    };
    $scope.tagTransformwarnasatu = function(newTag) {
        var item = {
            warna: newTag,
        };

        return item;
    };
    $scope.tagTransformwarnadua = function(newTag) {
        var item = {
            warna2: newTag,
        };

        return item;
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
    $scope.tagTransformspion = function(newTag) {
        var item = {
            kaca_spion: newTag,
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
    $scope.tagTransformkarat = function(newTag) {
        var item = {
            anti_karat: newTag,
        };

        return item;
    };

    //INTERIOR
    $scope.tagTransformplavon = function(newTag) {
        var item = {
            plavon: newTag,
        };

        return item;
    };
    $scope.tagTransformtrimming = function(newTag) {
        var item = {
            trimming_deck: newTag,
        };

        return item;
    };
    $scope.tagTransformduchting = function(newTag) {
        var item = {
            duchting_louver: newTag,
        };

        return item;
    };
    $scope.tagTransformlplavon = function(newTag) {
        var item = {
            lampu_plavon: newTag,
        };

        return item;
    };
    $scope.tagTransformlantai = function(newTag) {
        var item = {
            lantai: newTag,
        };

        return item;
    };
    $scope.tagTransformkarpet = function(newTag) {
        var item = {
            karpet: newTag,
        };

        return item;
    };
    $scope.tagTransformbdalam = function(newTag) {
        var item = {
            bagasi_dalam: newTag,
        };

        return item;
    };
    $scope.tagTransformdashboard = function(newTag) {
        var item = {
            dashboard: newTag,
        };

        return item;
    };
    $scope.tagTransformperedam = function(newTag) {
        var item = {
            peredam: newTag,
        };

        return item;
    };
    $scope.tagTransformpegangan_atas = function(newTag) {
        var item = {
            pegangan_tangan_atas: newTag,
        };

        return item;
    };
    $scope.tagTransformpengaman_penumpang = function(newTag) {
        var item = {
            pengaman_penumpang: newTag,
        };

        return item;
    };
    $scope.tagTransformpengaman_kaca = function(newTag) {
        var item = {
            pengaman_kaca_samping: newTag,
        };

        return item;
    };
    $scope.tagTransformpengaman_driver = function(newTag) {
        var item = {
            pengaman_driver: newTag,
        };

        return item;
    };
    $scope.tagTransformgordyn = function(newTag) {
        var item = {
            gordyn: newTag,
        };

        return item;
    };
    $scope.tagTransformdriver_Fan = function(newTag) {
        var item = {
            driver_fan: newTag,
        };

        return item;
    };
    $scope.tagTransformradio_tape = function(newTag) {
        var item = {
            radio_tape: newTag,
        };

        return item;
    };
    $scope.tagTransformspek_seat = function(newTag) {
        var item = {
            spek_seat: newTag,
        };

        return item;
    };
    $scope.tagTransformdriver_seat = function(newTag) {
        var item = {
            driver_seat: newTag,
        };

        return item;
    };
    $scope.tagTransformseat1 = function(newTag) {
        var item = {
            konf_seat1: newTag,
        };

        return item;
    };
    $scope.tagTransformseat2 = function(newTag) {
        var item = {
            konf_seat2: newTag,
        };

        return item;
    };
    $scope.tagTransformseat3 = function(newTag) {
        var item = {
            konf_seat3: newTag,
        };

        return item;
    };
    $scope.tagTransformseat4 = function(newTag) {
        var item = {
            konf_seat4: newTag,
        };

        return item;
    };
    $scope.tagTransformseat5 = function(newTag) {
        var item = {
            konf_seat5: newTag,
        };

        return item;
    };
    $scope.tagTransformcover_seat = function(newTag) {
        var item = {
            cover_seat: newTag,
        };

        return item;
    };
    $scope.tagTransformoptional_seat = function(newTag) {
        var item = {
            optional_seat: newTag,
        };

        return item;
    };
    $scope.tagTransformtotal_seat = function(newTag) {
        var item = {
            total_seat: newTag,
        };

        return item;
    };
    $scope.tagTransformac = function(newTag) {
        var item = {
            merk_ac: newTag,
        };

        return item;
    };



})
