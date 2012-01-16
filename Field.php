<?php

/*
 * @filename.....: Field.php
 * @package......: Field
 * @class........: Field
 * @purpose......: Input main classe provide usual Field Objects
 * @parameter....: none
 * @created......: 2010-12-10
 *
 */

class Zaape_Field {
 
	private static $instance;
	public static $_Input = null;
	public static $_Output = null;
	protected $nameDB; // Database name of a Field
	var $value = '';

	public function __construct() {

		$this->init ();

	}

	/**
	 *
	 * Singleton pattern ...
	 */
	public static function getInstance() {

		if (! self::$instance instanceof self) {

			self::$instance = new self ();
		}

		return self::$instance;

	}

	/**
	 * @method init
	 * @return void
	 * obtiene los valores que recibe el server en el request
	 */
	public function init() {

		$this->_method 	= $_SERVER ["REQUEST_METHOD"];
		$this->_agent 	= (isset ( $_SERVER ["HTTP_USER_AGENT"] )) ? $_SERVER ["HTTP_USER_AGENT"] : null;
		$this->_ip 		= $_SERVER ["REMOTE_ADDR"];
		$this->_port 	= $_SERVER ['SERVER_PORT'];
		$this->value 	= '';

	}

	/**
	 * @method Input
	 * @return object
	 * Crea un object Var_Input
	 */
	public function Input() {

		//return $this->_Input = new Zaape_Field_Input ();

	}

	/**
	 *
	 * @method Ouput
	 * @return Field Ouput
	 *
	 * Crea un object Var_Ouput
	 *
	 */
	public function Output() {

//		$this->_Output = new Zaape_Field_Output ();
//		$this->_Output->setValue ( $this->getValue () );
//
//		return $this->_Output;

	}

	/**
	 * @method set
	 * @return string
	 * regresa el valor final
	 */
	public function set($name) {

		$this->nameDB = $name;

		return $this->name = $name;
	}

	/**
	 * @method getValue
	 * @return string
	 * regresa el valor final
	 */
	public function getValue() {

		return $this->value;
	}

	/**
	 * @method setValue
	 * @return void
	 * regresa el valor final
	 */
	public function setValue($value) {

		$this->value = $value;

		return $this;
	}

	/**
	 *
	 * Set the db name of a Field ...
	 * @param String $nameDB
	 */
	public function Db($nameDB = '') {

		if ($nameDB != '') {

			$this->nameDb = $nameDB;

		} else {

			return $this->nameDB;

		}
	}

	/**
	 * @method isPost
	 * @return boolean
	 * Valida si el request es por metodo POST, nos sirve para prevenir errores en las validaciones por POST
	 */
	public function isPost() {

		return $this->_method == "POST";

	}

	/**
	 * @method isGet
	 * @return boolean
	 * Revisa si el request es por metodo GET, nos sirve para prevenir errores en las validaciones por GET
	 */
	public function isGet() {

		return $this->_method == "GET";

	}

	/**
	 * @method agent
	 * @return boolean true / false
	 */
	public function ip_address($ip = null) {

		$ip = isset ( $ip ) ? $ip : $this->_ip;

		if ($this->valid_ip ( $ip )) {

			$this->value = $ip;

		}

		return $this;

	}

	/**
	 * @method valid_ip
	 * @param string $string
	 * @param bolean $method
	 * @return bolean;
	 */
	public function valid_ip($ip = null) {

		$this->init ();

		$ip = isset ( $ip ) ? $ip : $this->_ip;

		if ($result = Field_var ( $ip, Field_VALIDATE_IP )) {

			$this->value = $result;

			return true;

		} else {

			return false;
		}

	}

	/**
	 * @method agent
	 * @return object
	 */
	public function agent() {

		$this->value = $this->_agent;
		return $this;

	}

	/**
	 * @method qoute
	 * @return string / boolean
	 */
	public function quote($string) {

		return Field_var ( $string, Field_SANITIZE_MAGIC_QUOTES );

	}

	/**
	 * @method sanitize
	 * @param $string
	 * @return object
	 */
	public function sanitize($value = null) {

		$value = isset ( $value ) ? $value : $this->value;

		if (is_array ( $value )) {

			$this->_newValue = array ();

			foreach ( $value as $key => $val ) {

				$this->_newValue [$key] = filter_var ( $val, FILTER_SANITIZE_STRING );
					
			}

			$this->value = $this->_newValue;

		} else {

			$this->value = Field_var ( $value, Field_SANITIZE_STRING );

		}

		return $this;

	}

	/**
	 * @method sanitize
	 * @param string $string
	 * @param bolean $method
	 * @return object;
	 */
	public function sanitizeEncode($string = null) {

		$string = isset ( $string ) ? $string : $this->value;
		$this->value = Field_var ( $string, Field_SANITIZE_SPECIAL_CHARS, Field_FLAG_NO_ENCODE_HIGH );

		return $this;

	}

	/**
	 * @method isEmail
	 * @param string $string
	 * @param bolean $method
	 * @return object;
	 */
	public function isEmail($string = null) {

		$string = isset ( $string ) ? $string : $this->value;
		$this->value = Field_var( $string, Field_VALIDATE_EMAIL );

		return $this;

	}

	/**
	 *
	 * Enter description here ...
	 * @param String $type
	 */
	public function type($type) {

		$this->_type = $type;

	}

	/**
	 *
	 * Enter description here ...
	 */
	public function MD5() {

		$md5 = md5 ( $this->getValue () );

		$this->setValue ( $md5 );

		return $md5;

	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $name
	 */
	public function getName($name = '') {

		switch ($name) {
			case 'GET' :
			case 'GET1' :
				return $this->nameGET;
				break;
					
			case 'GET2' :
				return $this->nameGET2;
				break;
					
			case 'POST' :
			case 'POST1' :
				return $this->namePOST;
				break;
					
			case 'POST2' :
				return $this->namePOST2;
				break;
					
			case 'DB' :
				return $this->nameDB;
				break;
					
			default :
				return $this->name;
				break;

		}

	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $extFunt
	 */
	function getPOST($extFunt = '') {

		$aux1 = (isset ( $_POST [$this->name] )) ? $_POST [$this->name] : NULL;

		$this->setValue ( $aux1, $extFunt );

		return $aux1;

	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $name1
	 * @param unknown_type $name2
	 */
	function nameGET($name1, $name2 = '') {
		$this->nameGET = $name1;
		if ($name2 != '')
		$this->nameGET2 = $name2;
	}

	/**
	 *
	 * Enter description here ...
	 * @param unknown_type $extFunt
	 */
	function getGET($extFunt = '') {
		global $_GET;

		if ($this->nameGET != '') {
			$aux1 = (isset ( $_GET [$this->nameGET] )) ? $_GET [$this->nameGET] : '';

			if ($this->nameGET2 != '') {
				$aux2 = (isset ( $_GET [$this->nameGET2] )) ? $_GET [$this->nameGET2] : '';

				if ($aux1 != '' && $aux2 != '' && $aux1 != $aux2) {
					$this->setValue ( $aux1, $extFunt );
					$this->statGET = 1;
					return $aux1;
				}
			}

			$this->setValue ( $aux1, $extFunt );
			return $aux1;
		}

		$aux1 = (isset ( $_GET [$this->name] )) ? $_GET [$this->name] : $this->default;

		$this->setValue ( $aux1, $extFunt );

		return $aux1;
	}

	/**
	 *
	 * $save var optional param for save the value in the current rowset in the model
	 *
	 * @param bolean $save
	 *
	 */
	function keygen($save = NULL) {

		$string = $this->value;

		$sub2 = '';

		$aux = explode ( ".", $string );

		$sub1 = substr ( $aux [0], rand ( 1, 2 ), 2 );
		if (count ( $aux ) > 1)
		$sub2 = substr ( $aux [1], rand ( 0, 1 ), 2 );

		$num1 = substr ( rand (), 0, 2 );
		$num2 = substr ( rand (), 0, 2 );

		switch (rand ( 1, 5 )) {
			case 1 :
				$np = $sub1 . $sub2 . $num1 . $num2;
				break;
					
			case 2 :
				$np = $sub2 . $num2 . $sub1 . $num1;
				break;
					
			case 3 :
				$np = $num1 . $sub1 . $sub2 . $num2;
				break;
					
			case 4 :
				$np = $sub1 . $num1 . $num2 . $sub2;
				break;
					
			case 5 :
				$np = $sub1 . $num1 . $sub2 . $num2;
				break;
		}

		$np = strtolower ( $np );
		$np = substr ( md5 ( $np ), 0, 10 );

		//If saved required
		if ($save) {

			$this->setValue ( $np );

			return $np;

		} else {

			return $np;

		}

	}

	/**
	 *
	 * create a friendly from value url  ...
	 * @param String $string
	 */
	public function slugify($save = false, $string = null) {
		
		$string = trim($string);
		
		
		$string = $string ? $string : $this->value;
		
		
		//Unwanted:  {UPPERCASE} ; / ? : @ & = + $ , . ! ~ * ' ( )
		$string = strtolower ( $string );
		
		
		
		
		
		//Strip any unwanted characters
		$string = preg_replace ( "/[^a-z0-9_\s-]/", "", $string );
		//Clean multiple dashes or whitespaces
		$string = preg_replace ( "/[\s-]+/", " ", $string );
		//Convert whitespaces and underscore to dash
		$string = preg_replace ( "/[\s_]/", "-", $string );
		
		return $save ? $this->setValue( $string ) : $string;
		
		


	}

	/**
	 *
	 * sanitize string file name converting to ceo URL name
	 * $save var optional param for save the value in the current rowset in the model
	 * @param bolean $save
	 *
	 */
	function sanitizeFileName($save = null) {

		$filename = explode ( '.', $this->value );
		$extension = array_pop ( $filename );
		$filename = str_replace ( '.' . $extension, '', $this->value );
		$filename = $this->slugify ( $filename );
		
		$clean_name = strtr ( $filename, 'Å Å½Å¡Å¾Å¸Ã€Ã�Ã‚ÃƒÃ„Ã…Ã‡ÃˆÃ‰ÃŠÃ‹ÃŒÃ�ÃŽÃ�Ã‘Ã’Ã“Ã”Ã•Ã–Ã˜Ã™ÃšÃ›ÃœÃ�Ã Ã¡Ã¢Ã£Ã¤Ã¥Ã§Ã¨Ã©ÃªÃ«Ã¬Ã­Ã®Ã¯Ã±Ã²Ã³Ã´ÃµÃ¶Ã¸Ã¹ÃºÃ»Ã¼Ã½Ã¿', 'SZszYAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy' );
		$clean_name = strtr ( $clean_name, array ('Ãž' => 'TH', 'Ã¾' => 'th', 'Ã�' => 'DH', 'Ã°' => 'dh', 'ÃŸ' => 'ss', 'Å’' => 'OE', 'Å“' => 'oe', 'Ã†' => 'AE', 'Ã¦' => 'ae', 'Âµ' => 'u' ) );
		$filename = preg_replace ( array ('/\s/', '/\.[\.]+/', '/[^\w_\.\-]/' ), array ('_', '.', '' ), $clean_name );
		

		
		$filename = $filename . '.' . $extension;

		if ($save) {

			$this->setValue ( $filename );

			return $this;

		} else {

			return $filename;

		}

	}

	/**
	 *
	 * create a filename from a string,  ...
	 * $save var optional param for save the value in the current rowset in the model
	 *
	 * @param string $string
	 * @param bolean $save
	 */
	public function createFileNameFromString($string, $save = null) {

		$ext = $this->getExtension ( $this->value );

		$filename = $string . '.' . $ext;

		if ($save) {

			$this->setValue ( $filename );

			return $this;

		} else {

			return $filename;

		}

	}

	/**
	 *
	 * if the string is a filename ...
	 * @param unknown_type $string
	 */
	public function getExtension($string = null) {

		$string = $string ? $string : $this->value;

		$filename = explode ( '.', $string );

		return count ( $filename ) > 0 ? array_pop ( $filename ) : null;

	}

	/**
	 *
	 * create a friendly from value url
	 * $save var optional param for save the value in the current rowset in the model
	 */
	public function toSEOUrl($save = null) {

		$string = $this->slugify ( $this->value );

		if ($save) {

			$this->setValue ( $string );

			return $this;

		} else {

			return $string;

		}

	}

	/**
	 *
	 * $save var optional param for save the value in the current rowset in the model
	 *
	 */
	public function encode() {

		$encode = Zend_Json_Encoder::encode ( $this->value );

		$this->setValue ( $encode );

		return $this;

	}
	
	
	/**
	 *
	 * isDate
	 *
	 */
	function isDate( ) { 
	
	
		  $stamp = strtotime( $this->value ); 
		  
		  if (!is_numeric($stamp)){ 
			 
			 return FALSE; 
		  } 
		  
		  $month = date( 'm', $stamp ); 
		  $day   = date( 'd', $stamp ); 
		  $year  = date( 'Y', $stamp ); 
		  
		  if (checkdate($month, $day, $year)){ 
			 
			 return TRUE; 
			 
		  } 
		  
		  return FALSE; 
	} 
	
	
	function setMergeValue( $data ){
		
		
			$data =  empty($data) ? array(): $data;
			
			$meta =  empty($this->value) ? array('Data'=>'') : json_decode( $this->value,true );
			
			$newvalue = array_merge( $meta , $data ) ;
			
			$newvalue = json_encode($newvalue);
			
			$this->setValue( $newvalue );
		
		
	}
	
	function getMetaValue(){
		
		return $this->metaValue;
		
	}
	
	function setMetaValue( $value ){
		
		$this->metaValue = $value;
		
	}
	
	
	function getMeta( $key ){
		
		$value = $this->value;
		
		$meta =  empty($value) ? array() : json_decode($value);
		
		
		if( array_key_exists($key,$meta) ){
			
			$this->setMetaValue( $meta->$key );
				
		}
		
		return $this;
		
	}
	
	
	public function flip( $value = null ){
	
	    $value = $value ? $value : $this->value;
	    
		$flip = $this->getFlipValue();
	
		if( $flip > $value ){
				
			return false;
				
		}
	
		return true;
			
	
	}
	
	
	public function getFlipValue( $flag = false ){
	    
	    if( $flag ){
	        
	        $this->setValue(  rand(15, 100) / 100 ) ;
	        
	    }else{
	     
	        return  rand(0, 100) / 100 ;
	    }
	    
	    
		 
	}
	
	

}