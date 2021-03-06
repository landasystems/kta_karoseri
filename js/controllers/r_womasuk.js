app.controller('rekapwomasukCtrl', function ($scope, Data, toaster) {
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
        Data.get('rekap/rekapwomasuk', param).then(function (data) {
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

    $scope.excel = function () {
        Data.get('rekap/rekapwomasuk', paramRef).then(function (data) {
            window.location = 'api/web/rekap/excelwomasuk';
        });
    }
    $scope.excel2 = function () {
        Data.get('rekap/rekapwomasuk', paramRef).then(function (data) {
            window.location = 'api/web/rekap/excelwomasuk2';
        });
    }
    $scope.print2 = function () {
        Data.get('rekap/rekapwomasuk', paramRef).then(function (data) {
            window.open('api/web/rekap/excelwomasuk2?print=true', "", "width=500");
        });
    }
    $scope.print = function () {
        Data.get('rekap/rekapwomasuk', paramRef).then(function (data) {
            window.open('api/web/rekap/excelwomasuk?print=true', "", "width=500");
        });
    }
    $scope.grafik = function () {
        Data.get('rekap/chartwomasuk').then(function (data) {
            $scope.start = data.start;
            $scope.end = data.end;
            var merk = data.merk;
            var model = data.model;
            var sales = data.sales;
            var hari = data.hari;

           
            $scope.chartSales = {
                options: {
                    chart: {
                        type: 'column'
                    }
                },
                title: {
                    text: 'Grafik Jumlah Unit Per Sales'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'UNIT'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }
                },
                series: [{
                        name : "JUMLAH",
                        colorByPoint: true,
                        data: sales
                    }],
            };
            $scope.chartMerk = {
                options: {
                    chart: {
                        type: 'column'
                    }
                },
                title: {
                    text: 'Grafik Jumlah Unit Per Merk'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'UNIT'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }
                },
                series: [{
                        name : "JUMLAH",
                        colorByPoint: true,
                        data: merk
                    }],
            };
            $scope.chartModel = {
                options: {
                    chart: {
                        type: 'column'
                    }
                },
                title: {
                    text: 'Grafik Jumlah Unit Per Model'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'UNIT'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }
                },
                series: [{
                        name : "JUMLAH",
                        colorByPoint: true,
                        data: model
                    }],
            };
            $scope.chartHari = {
                options: {
                    chart: {
                        type: 'column'
                    }
                },
                title: {
                    text: 'Grafik Jumlah Unit Per Model'
                },
                xAxis: {
                    type: 'category'
                },
                yAxis: {
                    title: {
                        text: 'UNIT'
                    }

                },
                legend: {
                    enabled: false
                },
                plotOptions: {
                    series: {
                        borderWidth: 0,
                        dataLabels: {
                            enabled: true,
                            format: '{point.y}'
                        }
                    }
                },
                series: [{
                        name : "JUMLAH",
                        colorByPoint: true,
                        data: hari
                    }],
            };


        });

    }


})
