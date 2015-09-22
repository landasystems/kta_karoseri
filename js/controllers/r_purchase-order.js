app.controller('returpoCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;
    
    $scope.displayed = [];
    $scope.paginations = 0;
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
    //laporan PO
    $scope.excellpo = function () {
        Data.get('po/rekap', paramRef).then(function (data) {
            window.location = 'api/web/po/excel';
        });
    }
    
    $scope.printlpo = function () {
        Data.get('po/rekap', paramRef).then(function (data) {
            window.open('api/web/po/excel?print=true');
        });
    }
    
    //
    //beli tunai kredit
    
    $scope.excelbeli = function () {
        Data.get('po/rekap', paramRef).then(function (data) {
            window.location = 'api/web/po/excelbeli';
        });
    }
    $scope.printbeli = function () {
        Data.get('po/rekap', paramRef).then(function (data) {
            window.open('api/web/po/excelbeli?print=true');
        });
    }
    //
    //pemantauan
    $scope.excelpantau = function () {
        Data.get('po/rekap', paramRef).then(function (data) {
            window.location = 'api/web/po/excelpantau';
        });
    }
     $scope.printpantau= function () {
        Data.get('po/rekap', paramRef).then(function (data) {
            window.open('api/web/po/excelpantau?print=true');
        });
    }
    //
    //fluktuasi
    $scope.excelfluktuasi = function () {
        Data.get('po/rekap', paramRef).then(function (data) {
            window.location = 'api/web/po/excelfluktuasi';
        });
    }
    
     $scope.printfluktuasi = function () {
        Data.get('po/rekap', paramRef).then(function (data) {
            window.open('api/web/po/excelfluktuasi?print=true');
        });
    }
    ////

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

   


})
