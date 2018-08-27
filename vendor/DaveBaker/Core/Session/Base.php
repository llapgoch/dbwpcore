<?php

 namespace DaveBaker\Core\Session;
 /**
  * Class Base
  * @package DaveBaker\Core\Session
  */
 class Base extends \DaveBaker\Core\Base
 {
     const MESSAGES_KEY = '___messages___';
     protected $namespaceCode = 'session';
     protected $sessionNamespace = '';

     /**
      * @return \DaveBaker\Core\Base|void
      * @throws Exception
      */
     protected function _construct()
     {
         if(!$this->sessionNamespace){
             throw new Exception('Session namespace not set');
         }

         $this->initSessionObject();
     }

     /**
      * @param $message
      * @param string $type
      * @return $this
      */
     public function addMessage($message, $type = \DaveBaker\Core\Definitions\Messages::SUCCESS)
     {
         $optionKey = $this->getNamespacedOption('');

         if(!isset($_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY][$type])){
             $_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY][$type] = [];
         }

         $_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY][$type][] = $message;

         return $this;
     }

     /**
      * @param null $type
      * @return array
      */
     public function getMessages($type = null, $clear = true)
     {
        $messages = [];

        $optionKey = $this->getNamespacedOption('');

        if(!$type){
            $messages = $_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY];
        }

        if(isset($_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY][$type])){
            $messages = $_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY][$type];
        }

        if($clear){
            $this->clearMessages($type);
        }

        return $messages;
     }

     /**
      * @param null $type
      * @return $this
      */
     public function clearMessages($type = null)
     {
         $optionKey = $this->getNamespacedOption('');

         if(!$type){
             $_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY] = [];
         }else{
             if(isset($_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY][$type])){
                 unset($_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY][$type]);
             }
         }

         return $this;
     }

     /**
      * @param $key
      * @return null
      */
     public function get($key)
     {
        $this->initSessionObject();

        if(isset($_SESSION[$this->getNamespacedOption('')][$this->sessionNamespace][$key])){
            return $_SESSION[$this->getNamespacedOption('')][$this->sessionNamespace][$key];
        }

        return null;
     }

     /**
      * @param $key
      * @param $data
      * @return $this
      */
     public function set($key, $data)
     {
         $_SESSION[$this->getNamespacedOption('')][$this->sessionNamespace][$key] = $data;
         return $this;
     }

     /**
      * @param null $key
      * @return $this
      */
     public function clear($key = null)
     {
         if($key){
             if(isset($_SESSION[$this->getNamespacedOption('')][$this->sessionNamespace][$key])){
                 unset($_SESSION[$this->getNamespacedOption('')][$this->sessionNamespace][$key]);
             }
         }else{
             unset($_SESSION[$this->getNamespacedOption('')][$this->sessionNamespace]);
         }

         return $this;
     }

     protected function initSessionObject()
     {
        $optionKey = $this->getNamespacedOption('');
        if(!isset($_SESSION[$optionKey])){
            $_SESSION[$optionKey] = [];
        }

        if(!isset($_SESSION[$optionKey][$this->sessionNamespace])){
            $_SESSION[$optionKey][$this->sessionNamespace] = [];
        }

        if(!isset($_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY])){
            $_SESSION[$optionKey][$this->sessionNamespace][self::MESSAGES_KEY] = [];
        }
     }
 }