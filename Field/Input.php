<?php
 
/*
* @filename.....: Input.php
* @package......: Var
* @class........: Var_Input
* @purpose......: Get, sanitize,clear data Input
* @parameter....: none
* @created......: 2010-12-10
*
*/

class Zaape_Field_Input extends Zaape_Field {
	
	static private  $instance;
	
	public function __construct() {
		
		$this->init();
		
	}
	 
	
	/**
	 * 
	 * Singleton Pattern ...
	 */
	static public function getInstance() {
	       
	    	if (self::$instance == NULL) {
	
	       		self::$instance = new self;
	       
	       }
	       
	       return self::$instance;
	       
	  }
	
	  
	/**
	 * @method agent
	 * @return boolean true / false
	 */
	public function cookie($name,$mode = true){
		
		
		$this->value = isset($_COOKIE[$name]) ? $_COOKIE[$name] : null ;
		
		return $this;
			
	
	}
	  
	/**
	 * @method _post
	 * @return boolean true / false
	 */
	public function _Post($name = null,$mode = true){
		
		if( $this->isPost() ){
			
			if( $name ){
					
					$this->value = isset($_POST[$name]) ? $_POST[$name] : null;
			
			}else{
				
				
				if(  isset( $_POST[$this->name]) ){
					
					
					$this->value = $_POST[$this->name];
					
					
				
				}else{					
					
					if( count($_POST) > 0){
	
						$this->value = $_POST;
						
					}
					
				}
				
			}
		
		}
		
		return $this;
		
	}
	
	
	/**
	 * @method _get
	 * @return self
	 */
	public function _Get($name,$mode = true){
		
		if( $this->isGet() ){
			
			if( $name ){
			
			$this->value = isset($_GET[$name]) ? $_GET[$name] : null ;
			
			}else{
				
				if( count($_GET) > 0){

					$this->value = $_GET;
					
				}
				
			}
		
		}	
		
		return $this;
		
	}


}