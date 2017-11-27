var app=angular.module("dustad",[]);
app.controller("emails",function($scope,$compile,$http){
    $scope.emailList=[];
    $scope.offset=0;
    $scope.getEmails=function(){
        $http.get("../getEmails?offset="+$scope.offset)
        .then(function success(response){
            response=response.data;
            if(typeof response =="object"){
                $scope.emailList=response;
                $scope.displayEmailList();
            }
            else{
                $("#emaillist").html('<p>No email IDs found.</p>');
            }
        },
        function error(response){
            console.log(response);
            messageBox("Problem","Something went wrong while loading this email list.");
        });
    };
    $scope.displayEmailList=function(){
        if(validate($scope.emailList)){
            var emails=$scope.emailList;
            var table='<table class="table"><thead><tr><th>Email ID</th><th>Name</th><th>Website</th><th>Description</th></thead><tbody>';
            for(var i=0;i<emails.length;i++){
                var email=emails[i];
                var emailID=email.idemail_master;
                var name=stripslashes(email.emailee_name);
                var email_id=email.email_id;
                var website=email.emailee_website;
                var ins=email.institute_master_idinstitute_master;
                var inName=stripslashes(ins.institute_name);
                if(validate(website)){
                    website='<a href="'+website+'" target="_blank">'+website+'</a>';
                }
                else{
                    website='No website found';
                }
                var desc=nl2br(stripslashes(email.emailee_category));
                table+='<tr><td>'+email_id+'</td><td>'+name+'</td><td>'+website+'</td><td>'+desc+'</td></tr>';
            }
            table+='</tbody></table>';
            $("#emaillist").html(table);
            $("#insname").html("for "+inName);
        }
    };
});