<?php
ini_set('display_errors', 1);
require_once __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../src/app.php';
require __DIR__.'/../config/prod.php';
require __DIR__.'/../src/controllers.php';
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
$app->register(new Silex\Provider\MonologServiceProvider(), array(
    'monolog.logfile' => 'php://stderr',
));
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__.'/views',
));
$app->register(new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
      'driver' => 'pdo_mysql',
      'dbname' => 'heroku_9def66b1566b6eb',
      'user' => 'b40c70c7aa1f54',
      'password' => '9e17afc9',
      'host'=> "us-cdbr-iron-east-05.cleardb.net",
    )
));
$app->register(new Silex\Provider\SessionServiceProvider, array(
    'session.storage.save_path' => dirname(__DIR__) . '/tmp/sessions'
));
$app->before(function(Request $request) use($app){
    $request->getSession()->start();
});
$app->get("/",function() use($app){
    $app['twig']->render("index.html.twig");
});
$app->get("/get/{insID}",function($insID) use($app){
    $app['session']->set("ins_id",$insID);
    return $app->redirect("/institute/".$insID);
});
$app->get("/institute/{insID}",function($insID) use($app){
    if($app['session']->get("ins_id"))
    {
        return $app['twig']->render("ins.html.twig");
    }
    else
    {
        return $app->redirect("/get/".$insID);
    }
});
$app->get("/getEmails",function(Request $request) use($app){
    if($app['session']->get("ins_id"))
    {
        require("../classes/instituteMaster.php");
        require("../classes/emailMaster.php");
        $email=new emailMaster;
        $emails=$email->getEmails($app['session']->get("ins_id"),$request->get("offset"));
        if(is_array($emails))
        {
            return json_encode($emails);
        }
        return $emails;
    }
    else
    {
        return "PAGE_NOT_FOUND";
    }
});
$app->get("/getEmailsCount",function() use($app){
    if($app['session']->get("ins_id")){
        require("../classes/instituteMaster.php");
        require("../classes/emailMaster.php");
        $email=new emailMaster;
        $emailCount=$email->countEmailIDs($app['session']->get("ins_id"));
        return $emailCount;
    }
    else
    {
        return "INVALID_PARAMETERS";
    }
});
$app->get("/stanford/{page}/{search}",function($page,$search) use($app){
    require("../classes/instituteMaster.php");
    require("../classes/emailMaster.php");
    $email=new emailMaster;
    $chars='abcdefghijklmnopqrstuvwxyz';
//    $search='aac';
    $url='https://profiles.stanford.edu/proxy/api/cap/search/keyword?p='.$page.'&q='.$search.'&ps=10';
    $json=file_get_contents($url);
    $json=json_decode($json,true);
    $level=$json['ui'];
    $profiles=$level['keywordMatches'];
    $noResults=trim($level['noResultsMessage']);
    if(($noResults=="")||($noResults==NULL))
    {
        for($i=0;$i<count($profiles);$i++)
        {
            $profile=$profiles[$i];
            $name=trim(ucwords($profile['displayName']));
            $emailID=trim($profile['email']);
            $website=trim($profile['webPage']);
            $text=$profile['shortTitle'];
            $desc=trim($text['text']);
            $emailID=str_replace(" ","",$emailID);
            $response=$email->addEmail($emailID,$name,$website,$desc,1);
            echo $response.'<br>';
        }
        echo "Moving onto the next page ...";
        $page+=1;
        return $app->redirect("/stanford/".$page."/".$search);
    }
    else
    {
        return "Done";
    }
});
$app->run();
?>