<?php

namespace App\Http;

class Response{

  /**
   * Código retornado na requisição
   */
  private $httpCode = 200;

  /**
   * Headers a serem enviados
   */
  private $headers = [];

  /**
   * Tipo do conteúdo
   */
  private $contentType = 'application/json';

  /**
   * Conteúdo a ser retornado
   */
  private $content;

  public function __construct($httpCode,$content,$contentType='application/json'){
    $this->httpCode = $httpCode;
    $this->content = $content;
    $this->setContentType($contentType);
  }

  /**
   * Altera o content type do response
   */
  public function setContentType($contentType){
    $this->contentType = $contentType;
    $this->addHeaders('Content-Type',$contentType);
  }

  /**
   * Adiciona item no cabeçalho de repsonse
   */
  public function addHeaders($key,$value){
    $this->headers[$key] = $value;
  }

  /**
   * método responsável por retornar os dados dos headers
   */
  private function sendHeaders(){
    //status
    http_response_code($this->httpCode);
    
    foreach($this->headers as $key=>$value){
      header($key .': '.$value);
    }
  }

  public function sendReponse(){    
    $this->sendHeaders();
    
    echo json_encode($this->content, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
  }

}