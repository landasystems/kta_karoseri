app.controller('rekapreturbarangkeluarCtrl', function ($scope, Data, toaster) {
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
        Data.get('returbbk/rekap', param).then(function (data) {
            $scope.displayed = data.data;
            $scope.displayedPrint = data.dataPrint;
            $scope.paginations = data.totalItems;
            if(data.totalItems != 0) {
                tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
            }
        });

        $scope.isLoading = false;
    };
    
    $scope.excel = function () {
        Data.get('returbbk/rekap', paramRef).then(function (data) {
            window.location = 'api/web/returbbk/excel';
        });
    }
     $scope.print = function() {
        Data.get('returbbk/rekap', paramRef).then(function(data) {
            window.open('api/web/returbbk/excel?print=true', "", "width=500");
        });
    }
   


})
