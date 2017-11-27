var app=angular.module("dustad",[]);
app.controller("emails",function($scope,$compile,$http){
    $scope.emailList=[];
    $scope.getEmails=function(){
        $http.get("getEmails")
        .then(function success(response){
            response=response.data;
            console.log(response);
        },
        function error(response){
            console.log(response);
        });
    };
});