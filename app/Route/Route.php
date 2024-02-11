<?php

namespace App\Route;

use App\Http\Request;
use App\Http\Response;
use \Closure;
use \Exception;
use \ReflectionFunction;

class Route{

  /**
   * Url completa
   */
  private $url;

  /**
   * urls
   */
  private $routes = [];

  /**
   * Variavel com os dados da requisição
  */
  private $request;

  /**
   * Padrão das rotas
   */
  private $prefix;

  public function __construct($url = ''){
    $this->request = new Request;
    $this->url =$url;
    $this->setPrefix();
  }

  /**
   * Método responsável por definir o prefixo das rotas
   */
  private function setPrefix(){
    $parseUrl = parse_url($this->url);

    //Prefixo
    $this->prefix = $parseUrl['path'] ?? '';
  }

  /**
   * Método responsável por adicionar uma rota na classe
   */
  private function addRoute($method, $route,$params ){
    //validação dos param
    foreach($params as $key => $value){
      if($value instanceof Closure){
        $params['controller'] = $value;
        unset($params[$key]);
      }
    }
    

    //Variaveis da rota
    $params['variables'] = [];

    //padrão de validação das variaveis das rotas
    $patternVariable = '/{(.*?)}/';
    if(preg_match_all($patternVariable,$route,$matches)){
      $route = preg_replace($patternVariable,'(.*?)',$route);      
      $params['variables'] = $matches[1];
    }
    
    //Padrão de validação de url
    $patternRoute ='/^'.str_replace('/','\/',$route).'$/';
    
    //Adiciona a rota dentro da classe
    $this->routes[$patternRoute][$method] = $params;
  }

  /**
   * Método responsável por retornar a uri desconsiderando o prefix
   */
  private function getUri(){
    // uri da request
    $uri = $this->request->getUri();
    
    //Fatia a url com o prefix
    $Xuri = strlen($this->prefix) ? explode($this->prefix,$uri) : [$uri];
      
    //retorna a uri sem prefix
    return end($Xuri);
  }

  /**
   * Método responsável por retornar os dados da rota atual
   */
  private function getRoute(){
    //URI
    $uri = $this->getUri();

    //method
    $httpMethod = $this->request->getHttpMethod();
    
    //Valida as url
    foreach($this->routes as $patternRoute=>$methods){
      //Verifica se a rota bate com o padrão
      if(preg_match($patternRoute,$uri,$matches)){
        //Verifica o método
        if(isset($methods[$httpMethod])){
          //Remove a primeira posição
          unset($matches[0]);

          //Chaves
          $keys = $methods[$httpMethod]['variables'];
          $methods[$httpMethod]['variables'] = array_combine($keys,$matches);
          $methods[$httpMethod]['variables']['request'] = $this->request;
           
          //Retorno dos paramétros da rota
          return $methods[$httpMethod];
        }
        //Método não permitido
        throw new Exception("Método não permitido", 405);
      }
    }
    
     //404
     throw new Exception("URL não encontrada", 404);
  }

  /**
   * Método responsável por execultar a rota atual
   */
  public function run(){
    try {
      //Obtém a rota atual;
      $route = $this->getRoute();

      if(!isset($route['controller'])){
        throw new Exception("URL não pode ser processada", 500);
      }

      //Argumentos da função
      $args = [];

      //Reflection
      $reflection = new ReflectionFunction($route['controller']);

      foreach($reflection->getParameters() as $parameter){
        $name = $parameter->getName();
        $args[$name] = $route['variables'][$name] ?? '';

      }
      
      //Retorna a execução da função
      return call_user_func_array($route['controller'],$args);
    } catch (Exception $e) {
      return new Response($e->getCode(),$e->getMessage());
    }
  }

  /**
   * Método responsável por definir uma rota get
   */
  public function get($route,$params = []){
    return $this->addRoute('GET',$route,$params);
  }

  /**
   * Método responsável por definir uma rota post
   */
  public function post($route,$params = []){
    return $this->addRoute('POST',$route,$params);
  }

  /**
   * Método responsável por definir uma rota put
   */
  public function put($route,$params = []){
    return $this->addRoute('PUT',$route,$params);
  }

  /**
   * Método responsável por definir uma rota delete
   */
  public function delete($route,$params = []){
    return $this->addRoute('DELETE',$route,$params);
  }

}