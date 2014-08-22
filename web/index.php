<?php
error_reporting(0);

require_once __DIR__.'/../vendor/autoload.php';
require_once 'config.php';
require_once 'model/IPAddr.php';
require_once 'model/IPAddrList.php';

$app = new Silex\Application();


$app->after(function(\Symfony\Component\HttpFoundation\Request $request, \Symfony\Component\HttpFoundation\Response $response){
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Headers', 'Content-Type');
    $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT');
});


$app->get('/', function(Silex\Application $app){
    $ipAddrList = new IPAddrList();
    
    // DB exception
    if($ipAddrList->dbList === false)
        return $app->json('Database error', 500);
      
    // error getting IPs
    if($ipAddrList->currentList === false)
        return $app->json('IPs cannot be resolved', 500);
    
    // no record in DB
    if(sizeof($ipAddrList->dbList) == 0){
        // insert current IPs into DB
        $ipAddrList->insertIPsToDB();
        $ipAddrList->getIPListFromDB();
    }
    return $app->json($ipAddrList->dbList, 200);
});

$app->put('/{id}', function(Silex\Application $app, $id, \Symfony\Component\HttpFoundation\Request $request) {
    $ipAddr = IPAddr::constructByJson($request->getContent());
    $res = IPAddrList::updateIPAddr($ipAddr);

    if($res === false)
        return $app->json($res, 500);
    
    return $app->json($res, 200);
});

$app->match('/update', function(Silex\Application $app){
    $ipAddrList = new IPAddrList();
    $res = array();
    
    // DB exception
    if($ipAddrList->dbList === false){
        $res['success'] = false;
        $res['msg'] = 'Database error';
        return $app->json($res, 500);
    }

    // error getting IPs
    if($ipAddrList->currentList === false){
        $res['success'] = false;
        $res['msg'] = 'IPs cannot be resolved';
        return $app->json($res, 500);
    }
    
    $ins = $ipAddrList->insertIPsToDB();
    // delete redundant IDs from DB
    $del = $ipAddrList->deleteRedundantIPsFromDB();
    if($ins === false || $del === false){
        $res['success'] = false;
        $res['msg'] = 'Database error';
        return $app->json($res, 500);
    }
    $res['success'] = true;
    $res['msg'] = 'IPs list successfully updated';
    return $app->json($res, 200);
})->method('GET|POST');

$app->run();
