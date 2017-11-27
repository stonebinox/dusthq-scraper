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
$app->get("/stanford",function() use($app){
    require("../classes/instituteMaster.php");
    require("../classes/emailMaster.php");
    $email=new emailMaster;
    $chars='abcdefghijklmnopqrstuvwxyz';
    $search='aaa';
    $page=1;
    $url='https://profiles.stanford.edu/proxy/api/cap/search/keyword?p='.$page.'&q='.$search.'&ps=10';
    $json=file_get_contents($url);
    /*$e=explode("@",$json);
    for($i=0;$i<count($e);$i+=2)
    {
        $part=$e[$i];
        $rev=strrev($part);
        $e2=explode('"',$rev);
        $first=strrev(trim($e2[0]));
        $second=$e[$i+1];
        $e2=explode('"',$second);
        $second=trim($e2[0]);
        $email=$first.'@'.$second;
        if(strpos($email,' ')==false)
        {
            
        }
    }
    return $text;*/
    //$json=json_encode($json);
    echo $json;
    $json=json_decode($json,true);
    $level=$json['ui'];
    $profiles=$level['keywordMatches'];
    for($i=0;$i<count($profiles);$i++)
    {
        $profile=$profiles[$i];
        break;
    }
    return var_dump($profiles[$i]);
});
$app->run();
?>