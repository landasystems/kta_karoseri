app.controller('ujimutuCtrl', function($scope, Data, toaster) {

    //init data
    var tableStateRef;
    $scope.displayed = [];
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.is_create = false;
    
     $scope.cariProduk = function ($query) {
        if ($query.length >= 3) {
            Data.get('ujimutu/cari', {nama: $query}).then(function (data) {
                $scope.results = data.data;
            });
        }
    }
    
    $scope.pilih =function(detail, $item){
        detail.merk = $item.merk
    }
    
    $scope.detUjimutu = [
        {
            id: '',
            kd_uji: '',
            bentuk_baru: '',
            kelas: '',
            biaya: '',
        }
    ];
    $scope.addDetail = function() {
        var newDet = {
            id: '',
            kd_uji: '',
            bentuk_baru: '',
            kelas: '',
            biaya: '',
        }
        $scope.total();
        $scope.detUjimutu.unshift(newDet);

    };

    $scope.removeRow = function(paramindex) {
        var comArr = eval($scope.detUjimutu);
        $scope.total();
        if (comArr.length > 1) {
            $scope.detUjimutu.splice(paramindex, 1);
            $scope.total();
        } else {
            alert("Something gone wrong");
        }

    };
    $scope.total = function() {
        var total = 0;
        var biaya_admin = parseInt($scope.form.biaya_admin);
        angular.forEach($scope.detUjimutu, function(detail) {
            total += detail.biaya;
        });
        $scope.form.total_biaya = total;

    }



    $scope.open1 = function($event) {
        $event.preventDefault();
        $event.stopPropagation();
        $scope.opened1 = true;
    };
    $scope.wo = {
        minimumInputLength: 3,
        allowClear: false,
        ajax: {
            url: "api/web/ujimutu/det_nowo/",
            dataType: 'json',
            data: function(term) {
                return {
                    kata: term,
                };
            },
            results: function(data, page) {
                return {
                    results: data.data
                };
            }
        },
        formatResult: function(object) {
            return object.no_wo;
        },
        formatSelection: function(object) {
            return object.no_wo;
        },
        id: function(data) {
            return data.no_wo;
        },
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

        Data.get('ujimutu', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });

        $scope.isLoading = false;
    };

    $scope.create = function(form, detail) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = true;
        $scope.formtitle = "Form Tambah Data";
        $scope.form = {};
//        $scope.form_total_biaya = 0;
//        $scope.form.biaya_admin = 0;
        $scope.detUjimutu = [
            {
                id: '',
                kd_uji: '',
                bentuk_baru: '',
                kelas: '',
                biaya: '',
            }
        ];

    };
    $scope.update = function(form) {
        $scope.is_edit = true;
        $scope.is_view = false;
        $scope.is_create = false;
        $scope.formtitle = "Edit Data : " + form.merk;
        $scope.form = form;
    };
    $scope.view = function(form) {
        $scope.is_edit = true;
        $scope.is_view = true;
        $scope.formtitle = "Lihat Data : " + form.merk;
        $scope.form = form;
    };
    $scope.save = function(form, detail) {
        var data = {
            ujimutu: form,
            det_ujimutu: detail,
        };
        var url = ($scope.is_create == true) ? 'ujimutu/create' : 'ujimutu/update/' + form.id;
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
        if (confirm("Apa anda yakin akan MENGHAPUS PERMANENT item ini ?")) {
            Data.delete('ujimutu/delete/' + row.id).then(function(result) {
                $scope.displayed.splice($scope.displayed.indexOf(row), 1);
            });
        }
    };


})
