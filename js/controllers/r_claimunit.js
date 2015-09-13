app.controller('rekapclaimCtrl', function ($scope, Data, toaster) {
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
        Data.get('claimunit/rekap', param).then(function (data) {
            $scope.displayed = data.data;
            console.log(data.data);
            $scope.displayedPrint = data.dataPrint;
            $scope.paginations = data.totalItems;
            if (data.totalItems != 0) {
                tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
            }
        });

        $scope.isLoading = false;
    };


//    Data.get('claimunit/char').then(function (data) {

        $scope.chartConfigEx = {
            options: {
                chart: {
                    type: 'bar'
                }
            },
            series: [
                {"name": "KOMPLAIN EKSTERIOR", "data": [2, 45, 33, 47]}
            ],
            title: {
                text: 'JENIS KOMPLAIN EKSTERIOR'
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: ['Africa', 'America', 'Asia', 'Europe'],
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
                {"name": "KOMPLAIN INTERIOR", "data": [2, 45, 33, 47]}
            ],
            title: {
                text: 'JENIS KOMPLAIN INTERIOR'
            },
            credits: {
                enabled: false
            },
            xAxis: {
                categories: ['Africa', 'America', 'Asia', 'Europe'],
            },
            loading: false
        }

//    });

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
