app.controller('kpbCtrl', function($scope, Data, toaster) {
    $scope.jabatan = [];
    $scope.form = [];
    $scope.detail = [];

    $scope.cariWo = function($query) {
        if ($query.length >= 3) {
            Data.get('wo/wospk', {nama: $query}).then(function(data) {
                $scope.results = data.data;
            });
        }
    }

    $scope.listBagian = function($query) {
        if ($query.length >= 3) {
            Data.get('kpb/jabkpb', {key: $query}).then(function(data) {
                $scope.jabatan = data.data;
            });
        }
    }

    $scope.listBahan = function(kd_bom, kd_jab) {
        var dt = {
            kd_bom: kd_bom.kd_bom,
            kd_jab: kd_jab,
        };
        Data.post('kpb/listbahan', dt).then(function(data) {
            $scope.detail = data.data;
        });
    }
})
