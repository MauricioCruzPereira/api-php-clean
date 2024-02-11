<?php

namespace App\Http;

/**
 * Classe responsável por gerenciar a requisição.
 */
class Request{

  /**
   * Cabecalho da requisição
   */
  private $headers;

  /**
   * Uri da requisição
   */
  private $uri;

  /**
   * Parametros da requisição
   */
  private $valuesGet;

  /**
   * Valores dos posts
   */
  private $valuesPost;

  /**
   * Método da requisição
   */
  private $httpMethod;

  public function __construct(){
    $this->headers    = getallheaders();    
    $this->valuesGet  = $_GET ?? '';
    $this->valuesPost = $_POST ?? '';
    $this->uri        = $_SERVER['REQUEST_URI'] ?? '';
    $this->httpMethod = $_SERVER['REQUEST_METHOD'] ?? '';
  }

  /**
   * Método responsável por retornar o cabeçalho da requisição
   */
  public function getHeaders(){
    return $this->headers;
  }

  /**
   * Método responsável por retornar os gets da requisição
   */
  public function getValuesGet(){
    return $this->valuesGet;
  }

  /**
   * Método responsável por retornar os dados enviados do form
   */
  public function getValuesPost(){
    return $this->valuesPost;
  }

  public function getUri(){
    return $this->uri;
  }

  public function getHttpMethod(){
    return $this->httpMethod;
  }

  
}