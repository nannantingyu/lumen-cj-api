<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return "<div style=\"width: 100%; font-size: 50px; padding-top:10%; padding-bottom:10%; text-align: center; font-family: 'Microsoft JhengHei', 'Arial','Microsoft YaHei','黑体','宋体', sans-serif;\"><p>聚金财经</p></div>";
});


$router->get('/getDates', 'EconomicController@getDates');
$router->get('/getPastorWillFd', 'EconomicController@getPastorWillFd');
$router->get('/getWeekData', 'EconomicController@getWeekData');
$router->get('/getjiedu', 'EconomicController@getjiedu');
$router->get('/getjiedudata', 'EconomicController@getjiedudata');
$router->get('/getcjdatas', 'EconomicController@getcjdatas');
$router->get('/getcjevent', 'EconomicController@getcjevent');
$router->get('/getcjholiday', 'EconomicController@getcjholiday');
$router->get('/fedata', 'EconomicController@fedata');
$router->get('/kx', 'KuaixunController@getkx');