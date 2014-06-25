angular.module("DayZEpoch",['ngRoute','googlechart','ui.bootstrap'])
.config(['$routeProvider',function($routeProvider) {
    $routeProvider.when("/home",{
        templateUrl: "app/home/home.html",
        controller: "HomeController"
    }).when("/players",{
        templateUrl: "app/players/players.html",
        controller: "PlayersController"
    }).when("/objects",{
        templateUrl: "app/objects/objects.html"
    }).otherwise({
        redirectTo: "/home"
    });
}])
.factory('ChartFactory', ['$http',function($http){
    var factory = {};
    factory.getChart = function(type,options,cols,host) {
        var chart = {};
        chart.type = type;
        chart.options = options;
        chart.data = {};
        chart.data.cols = cols;
        chart.data.rows = [];
        chart.readRow = function(o) {
            var obj = {
                c: []
            };
            var keys = Object.keys(o);
            for(var i=0;i<keys.length;i++) {
                var v = {};
                v.v = parseFloat(o[keys[i]]);
                if(isNaN(v.v)) {
                    v.v = o[keys[i]];
                }
                obj.c.push(v);
            }
            this.data.rows.push(obj);
        };
        chart.readRows = function(o) {
            for(var i=0;i<o.length;i++) {
                this.readRow(o[i]);
            }
        };
        $http.get(host).success(function(data) {
            chart.readRows(data);
        });
        return chart;
    };
    return factory;
}])
.controller('AppController', ['$scope','$location',function($scope,$location){
    $scope.isActive = function(loc) {
        return $location.path() === loc;
    };
}])
.controller('HomeController', ['$scope','$http', function($scope,$http){
    $scope.summary = {};
    $http.get('api/summary').success(function(data) {
        $scope.summary = data;
    });
    $scope.leaders = {};
    $http.get('api/leaders').success(function(data) {
        $scope.leaders = data;
    });
}])
.controller('PlayersController', ['$scope','$http','ChartFactory', function($scope,$http,ChartFactory){
    //leaderboard
    $http.get('api/leaders').success(function(data) {
        $scope.leaders = data;
    });

    //players
    $scope.filter = '';
    $scope.playerTable = {
        page: 1,
        pages: 50,
        size: 15,
        order: "ZombieKills",
        dir: "desc",
        filter: "no-filter",
        isFiltered: false
    };

    $scope.isAlive = function(player) {
        return player.Alive === "1";
    };

    $scope.setPage = function(index) {
        $scope.playerTable.page = Math.min(Math.max(1,index),$scope.playerTable.pages);
        $scope.refreshPlayers();
    };

    $scope.refreshPlayers = function() {
        $http.get('api/players/' + $scope.playerTable.order + "/" + $scope.playerTable.dir + "/" + $scope.playerTable.size + "/" + $scope.playerTable.page + "/" + $scope.playerTable.filter).success(function(data) {
            $scope.players = data;
        });
        $http.get('api/count/players/' + $scope.playerTable.order + "/" + $scope.playerTable.dir + "/" + $scope.playerTable.size + "/" + $scope.playerTable.page + "/" + $scope.playerTable.filter).success(function(data) {
            $scope.playerTable.pages = Math.ceil(parseFloat(data[0]["RECORD_COUNT"]) / $scope.playerTable.size);
        });
    };

    $scope.isSortDescending = function() {
        return $scope.playerTable.dir === "desc";
    };

    $scope.isSelected = function(s) {
        return $scope.playerTable.order === s;
    };

    $scope.select = function(s) {
        if($scope.playerTable.order === s) {
            if($scope.playerTable.dir === "desc") {
                $scope.playerTable.dir = "asc";
            } else {
                $scope.playerTable.dir = "desc";
            }
        } else {
            $scope.playerTable.order = s;
            $scope.playerTable.dir = "desc";
        }
        $scope.refreshPlayers();
    };

    $scope.toggleFilter = function() {
        $scope.playerTable.isFiltered = !$scope.playerTable.isFiltered;
        if($scope.playerTable.isFiltered) {
            $scope.playerTable.filter = $scope.filter;
            $scope.playerTable.page = 1;
            //alert('api/count/players/' + $scope.playerTable.order + "/" + $scope.playerTable.dir + "/" + $scope.playerTable.size + "/" + $scope.playerTable.page + "/" + $scope.playerTable.filter);
        } else {
            $scope.playerTable.filter = "no-filter";
        }
        $scope.refreshPlayers();
    };

    $scope.disableFilter = function() {
        $scope.playerTable.isFiltered = false;
        $scope.playerTable.filter = "no-filter";
        $scope.refreshPlayers();
    };

    $scope.refreshPlayers();
}])
.controller('HumanityChartController', ['$scope','$http','ChartFactory', function($scope,$http,ChartFactory){
    $scope.panel = {
        heading: "Humanity",
        classes: "panel-success"
    };
    $scope.chart = ChartFactory.getChart(
        "PieChart",
        {
            'title': 'Player Lifestyle'
        },
        [
            {id: "t", label: "Lifestyle", type: "string"},
            {id: "s", label: "Survivors", type: "number"}
        ],
        'api/chartdata/humanity'
    );
}])
.controller('KillChartController', ['$scope','$http','ChartFactory', function($scope,$http,ChartFactory){
    $scope.panel = {
        heading: "Kills Per Life",
        classes: "panel-success"
    };
    $scope.chart = ChartFactory.getChart(
        "BarChart",
        {'title': 'Average Kills Per Life'},
        [
            {id: "t", label: "Kill Type", type: "string"},
            {id: "s", label: "Number of Kills", type: "number"}
        ],
        'api/chartdata/avgkills'
    );
}])
.controller('CreatedObjectsController', ['$scope','$http','ChartFactory', function($scope,$http,ChartFactory){
    $scope.panel = {
        heading: "New Server Objects",
        classes: "panel-info"
    };
    $scope.chart = ChartFactory.getChart(
        "LineChart",
        {'title': 'Objects Created By Day'},
        [
            {id: "t", label: "Date", type: "string"},
            {id: "s", label: "Objects Created", type: "number"},
            {id: "s", label: "Vehicles Created", type: "number"},
            {id: "s", label: "Structures Created", type: "number"}
        ],
        'api/chartdata/objectsbycreateddate'
    );
}])
.controller('UpdatedObjectsController', ['$scope','$http','ChartFactory', function($scope,$http,ChartFactory){
    $scope.panel = {
        heading: "Updated Server Objects",
        classes: "panel-warning"
    };
    $scope.chart = ChartFactory.getChart(
        "LineChart",
        {'title': 'Objects Updated By Day'},
        [
            {id: "t", label: "Date", type: "string"},
            {id: "s", label: "Objects Updated", type: "number"},
            {id: "s", label: "Vehicles Updated", type: "number"},
            {id: "s", label: "Structures Updated", type: "number"}
        ],
        'api/chartdata/objectsbyupdateddate'
    );
}])
.controller('ObjectChartController', ['$scope','$http','ChartFactory', function($scope,$http,ChartFactory){
    $scope.panel = {
        heading: "Objects",
        classes: "panel-success"
    };
    $scope.chart = ChartFactory.getChart(
        "PieChart",
        {
            'title': 'Server Object Summary'
        },
        [
            {id: "t", label: "Object Type", type: "string"},
            {id: "s", label: "Number of Objects", type: "number"}
        ],
        'api/chartdata/objects'
    );
}])
.controller('VehicleChartController', ['$scope','$http','ChartFactory', function($scope,$http,ChartFactory){
    $scope.panel =  {
        heading: "Vehicles",
        classes: "panel-success"
    };
    $scope.chart = ChartFactory.getChart(
        "PieChart",
        {
            'title': 'Vehicles By Class',
            'sliceVisibilityThreshold': 1/90
        },
        [
            {id: "t", label: "Vehicle Class", type: "string"},
            {id: "s", label: "Number of Vehicles", type: "number"}
        ],
        'api/chartdata/vehiclesbyclass'
    );
}])
.controller('StructureChartController', ['$scope','$http','ChartFactory', function($scope,$http,ChartFactory){
    $scope.panel =  {
        heading: "Structures",
        classes: "panel-success"
    };
    $scope.chart = ChartFactory.getChart(
        "PieChart",
        {
            'title': 'Structures By Class',
            'sliceVisibilityThreshold': 1/90
        },
        [
            {id: "t", label: "Structure Class", type: "string"},
            {id: "s", label: "Number of Structures", type: "number"}
        ],
        'api/chartdata/structuresbyclass'
    );
}])
.controller('RecentLoginsController', ['$scope','$http', function($scope,$http){
    $http.get('api/logins/4').success(function(data) {
        $scope.logins = data;
    });
}])
;