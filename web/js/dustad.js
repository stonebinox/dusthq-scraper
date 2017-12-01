var app=angular.module("dustad",[]);
app.config(function($interpolateProvider){
    $interpolateProvider.startSymbol('{[{').endSymbol('}]}');
});
app.controller("emails",function($scope,$compile,$http){
    $scope.emailList=[];
    $scope.offset=0;
    $scope.emailsCount=0;
    $scope.getEmails=function(){
        if($scope.offset==0){
            $("#emaillist").html('<div class="text-center"><img src="../images/ripple.gif" alt="Loading" width=30 height=30></div>');
        }
        $http.get("../getEmails?offset="+$scope.offset)
        .then(function success(response){
            if($scope.offset==0){
                $("#emaillist").html('');
            }
            response=response.data;
            if(typeof response =="object"){
                $scope.emailList=response;
                $scope.displayEmailList();
            }
            else{
                $("#emaillist").append('<p>No email IDs found.</p>');
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
            if($scope.offset==0){
                var table='<table class="table"><thead><tr><th>Email ID</th><th>Name</th><th>Website</th><th>Description</th></thead><tbody>';
            }
            else{
                var table='<table class="table"><tbody>';
            }
            for(var i=0;i<emails.length;i++){
                var email=emails[i];
                var emailID=email.idemail_master;
                var name=stripslashes(email.emailee_name);
                var email_id=email.email_id;
                var website=email.emailee_website;
                var ins=email.institute_master_idinstitute_master;
                var inName=stripslashes(ins.institute_name);
                if(validate(website)){
                    website='<a href="'+website+'" target="_blank">Website</a>';
                }
                else{
                    website='No website found';
                }
                var desc=nl2br(stripslashes(email.emailee_category));
                if(!validate(desc)){
                    desc="No description found.";
                }
                table+='<tr><td>'+email_id+'</td><td>'+name+'</td><td>'+website+'</td><td>'+desc+'</td></tr>';
            }
            table+='</tbody></table>';
            $("#emaillist").append(table);
            $("#insname").html("for "+inName);
            if(emails.length>=100){
                $scope.offset+=100;
                $("#loadmore").css("display","block");
            }
            else
            {
                $("#loadmore").css("display","none");
            }
        }
    };
    $scope.getEmailsCount=function(){
        $http.get("../getEmailsCount")
        .then(function success(response){
            response=response.data;
            if(!isNaN(response)){
                response=parseInt(response);
                $scope.emailsCount=response;
            }
            else{
                console.log(response);
                messageBox("Problem","Something went wrong while getting email information. Please try again later. This is the error we see: "+response);
            }            
        },
        function error(response){
            console.log(response);
            messageBox("Problem","Something went wrong while getting email information. Please try again later.");
        });
    };
    $scope.showEmailForm=function(){
        var text='<form><div class="form-group"><label for="email">Email body</label><div id="editor"></div></div><div class="text-center"><button type="button" class="btn btn-primary" ng-click="sendEmail()">Send</button></div></form>';
        messageBox("Compose Email",text);
        var options = {
            debug: 'info',
            placeholder: 'Compose an email ...',
            readOnly: false,
            modules: {
                toolbar: [
                    [{ header: [1, 2, false] }],
                    ['bold', 'italic', 'underline'],
                    ['image', 'code-block']
                ]
            },
            theme: "snow"
        };
        var editor = new Quill('#editor',options);
        $compile("#myModal")($scope);
    };
    $scope.sendEmail=function(){
        var content=$.trim(quill.getContents());
        console.log(content);
    };
});