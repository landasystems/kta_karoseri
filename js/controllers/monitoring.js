app.controller('monitoringCtrl', function($scope, Data, toaster) {
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
        Data.get('monitoring', param).then(function(data) {
            $scope.displayed = data.data;
            tableState.pagination.numberOfPages = Math.ceil(data.totalItems / limit);
        });
        $scope.isLoading = false;
    };

    $scope.print = function() {
        Data.get('monitoring', paramRef).then(function(data) {
            window.open('api/web/monitoring/excel?print=true', "", "width=500");
        });
    }

    $scope.export = function() {
        Data.get('monitoring', paramRef).then(function(data) {
            window.location = 'api/web/monitoring/excel';
        });
    }
});