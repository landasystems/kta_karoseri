app.controller('rekapclaimCtrl', function ($scope, Data, toaster) {
    //init data
    var tableStateRef;
    var paramRef;

    $scope.displayed = [];
    $scope.paginations = 0;
    $scope.is_edit = false;
    $scope.is_view = false;
    $scope.is_create = false;
    $scope.start = "";
    $scope.end = "";

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
        Data.get('claimunit/rekap', param).then(function (data) {
            $scope.grafik();
            $scope.displayed = data.data;
            $scope.displayedPrint = data.dataPrint;
            $scope.paginations = data.totalItems;
            if (data.totalItems != 0) {
                tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
            }
        });

        $scope.isLoading = false;
    };

    $scope.grafik = function () {

        Data.get('claimunit/char').then(function (data) {
            var exjumlah = data.Eksterior.jumlah;
            var exjeniskmp = data.Eksterior.jns_komplain;
            var injumlah = data.Interior.jumlah;
            var injeniskmp = data.Interior.jns_komplain;
            var sbjumlah = data.Small_Bus.jumlahnya;
            var sbjeniskmp = data.Small_Bus.jns_komplain;
            var mbjumlah = data.Mini_Bus.jumlahnya;
            var mbjeniskmp = data.Mini_Bus.jns_komplain;

//    console.log(injeniskmp);
            $scope.chartConfigSb = {
                options: {
                    chart: {
                        type: 'bar'
                    }
                },
                series: [
                    {"name": "JUMLAH KOMPLAIN SMALL BUSS", "data": sbjumlah}
                ],
                title: {
                    text: 'JENIS KOMPLAIN SMALL BUS'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: sbjeniskmp,
                },
                loading: false
            }
            $scope.chartConfigMb = {
                options: {
                    chart: {
                        type: 'bar'
                    }
                },
                series: [
                    {"name": "JUMLAH KOMPLAIN MINI BUS", "data": mbjumlah}
                ],
                title: {
                    text: 'JENIS KOMPLAIN MINI BUS'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: mbjeniskmp,
                },
                loading: false
            }
            $scope.chartConfigEx = {
                options: {
                    chart: {
                        type: 'bar'
                    }
                },
                series: [
                    {"name": "JUMLAH KOMPLAIN EKSTERIOR", "data": exjumlah}
                ],
                title: {
                    text: 'JENIS KOMPLAIN EKSTERIOR'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: exjeniskmp,
                },
                loading: false
            }


            $scope.chartConfigIn = {
                options: {
                    chart: {
                        type: 'bar'
                    }
                },
                series: [
                    {
                        "name": "JUMLAH KOMPLAIN INTERIOR", "data": injumlah}
                ],
                title: {
                    text: 'JENIS KOMPLAIN INTERIOR'
                },
                credits: {
                    enabled: false
                },
                xAxis: {
                    categories: injeniskmp,
                },
                loading: false
            }

        });
    }
    $scope.excel = function () {
        Data.get('claimunit/rekap', paramRef).then(function (data) {
//            var a = new window;
//            a.location = 'http://juuaaancoookkk.com/';
            window.open('api/web/claimunit/excel?excel=ex');
//            window.open('api/web/claimunit/char');
        });
    }

    $scope.print = function () {
        Data.get('claimunit/rekap', paramRef).then(function (data) {
//            var a = new window;
//            a.location = 'http://juuaaancoookkk.com/';
            window.open('api/web/claimunit/excel?excel=print');
        });
    }



})
