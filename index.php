<?php

use App\Controller\Controller;
use App\Http\Request;
use App\Http\Response;
use App\Route\Route;

require_once  __DIR__ . '/vendor/autoload.php';


$http = new Route('http://localhost/vaisaber');
$http->get('/',[function(){
  return new Response(200,Controller::hello());
}]);

//{nome da variavel} -> quando passado na rota irá pegar o valor
$http->get('/{a}/{b}',[function($a,$b){    
  return new Response(200,Controller::hello(),'application/json');
}]);

$http->run()->sendReponse();

