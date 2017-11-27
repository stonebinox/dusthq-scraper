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
$app->get("/stanford/{page}",function($page) use($app){
    require("../classes/instituteMaster.php");
    require("../classes/emailMaster.php");
    $email=new emailMaster;
    $chars='abcdefghijklmnopqrstuvwxyz';
    $search='aab';
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
            $emailID=str_replace(" ","",$emailID);
            $response=$email->addEmail($emailID,$name,1);
            echo $response.'<br>';
        }
        echo "Moving onto the next page ...";
        $page+=1;
        return $app->redirect("/stanford/".$page);
    }
    else
    {
        return "Done";
    }
});
$app->run();
?>