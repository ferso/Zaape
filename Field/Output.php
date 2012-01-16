<?php

/*
* @filename.....: Output.php
* @package......: Filter
* @class........: Zaape_Filter_Output
* @purpose......: Parse format output
* @parameter....: none
* @created......: 2010-12-10
*
*/


class Zaape_Field_Output extends Zaape_Field {
	
	static private  $instance;
	
	public function __construct() {
		
		$this->init();
		
	}
	 
	static public function getInstance() {
	       
	    	if (self::$instance == NULL) {
	
	       		self::$instance = new self;
	       
	       }
	       
	       return self::$instance;
	       
	  }
	  
	  
	/**
	 * @method getEnconding
	 * @param string $string
	 * @param bolean $method
	 * @return string;
	 */
	 public function getEncoding($string=null, $method=null){
	  		
	 		$string = $string ? $string : $this->value;
	  		return $this->encodetype = @mb_detect_encoding($string, 'auto');
	  		
	  }
	  
	 /**
	 * @method toUTF8
	 * @param string $string
	 * @param bolean $method
	 * @return self;
	 */
	  public function toUTF8($method=null, $string=null ){
	  	
	  	 $value = $string ? $string : $this->value;
	    
	  	 //If Array, then convert one by one to UTF8
	  	 if( is_array( $value )  ){
	  	 	
			$newarray = array();
			
				foreach( $value as $key => $val ){
					
					$val =  mb_convert_encoding($val, "UTF-8", "ASCII" );
				
				}
				
				$this->setValue( $newarray );
			
			}else{
				
				$value =  @mb_convert_encoding($value, "UTF-8", "ASCII" );				
				$this->setValue( $value );
				
				
			}
				
	  	return $this;
	  	
	  } 
	  
	/**
	 * @method toXHTML
	 * @param string $string
	 * @param bolean $method
	 * @return self;
	 */
	public function toXHTML($string=null, $method=null){
	  	
	   $string = $string ? $string : $this->value;
	
	   $this->value = html_entity_decode( ( $string ),ENT_QUOTES,'UTF-8');
	  
	   return $this;
	  	
  }
	  
	/**
	 * @method arrayToJson 
	 * @param string $string
	 * @param bolean $method
	 * @return self;
	 */
	public function arrayToJson($array = array(), $method=null){
	   
	   $value = $array ? $array : $this->value;
	   $this->value =  ( json_encode( ( $value ) ) );
		
	   return $this;
	  	
  }
  
  
  
	  
}