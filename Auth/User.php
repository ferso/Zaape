<?php


class Zaape_Auth_User extends Zaape_Table{
	
	protected $_prefix 		= '';
	protected $_spaces		= '';
	protected $_auth 		= null;
 	public   $_context	    = null; 
	public   $_encrypt	    = null;
	
	public function initAuth(){
		
			
		
		$this->_session = $this->getSpace('Zaape_Session'.$this->_name);
		
		$this->_loginf  	= str_replace($this->_prefix,'',$this->_login);
		$this->_loginf		= $this->_prefix.$this->_loginf;
		
		$_POST[ $this->_loginf ] = $_POST[$this->_login];
		
		$this->_loginfield	= ( isset( $this->_vars[ $this->_loginf ] ) ) ? $this->_vars[ $this->_loginf ] : null ;
		
		
		
		$this->_passf   	= str_replace($this->_prefix,'',$this->_password );
		$this->_passf		= $this->_prefix.$this->_passf ;
		
		$_POST[ $this->_passf ] = $_POST[$this->_password];
		
		$this->_passfield	= ( isset( $this->_vars[ $this->_passf ] ) ) ? $this->_vars[ $this->_passf ] : null ;
		
		
		if ( $this->_login !== null && $this->_password !== null){
			
			
			if( $this->_encrypt ){
				
				$this->_context 	= $this->_context ? ' AND '. $this->_context : '';
				
			
	$this->_auth = new Zend_Auth_Adapter_DbTable( $this->_db , $this->_name, $this->_loginf , $this->_passf, 'MD5(?) '. $this->_context  );
	
				
			}else{
			
	$this->_auth 		= new Zend_Auth_Adapter_DbTable( $this->_db , $this->_name, $this->_loginf , $this->_passf );
			
			}
			
		} 
		
		
		/**
		 * 
		 * Trigger function
		 * 
		 */
		$this->initHelper();

		
		
	}
	
	
	
	/**
	 * @name initHelper
	 * This functions is called after login success...
	 *  
	 */
	public function initHelper(){
		
			
		
	}
	
	
	/**
	 * @name sucessLogin
	 * 
	 * This function is called after the session login success
	 * 
	 */
	public function sucessLogin(){
		
		
		
	}
	
	
	/**
	 * @name onSucessLogin
	 * 
	 * This function is called after the session login success
	 * 
	 */
	public function onSucessLogin(){
		
		
	}
	
	/**
	 * @name sessionHelper
	 * 
	 * This function is called after the session login success
	 * 
	 */
	public function sessionHelper(){
		
		
	}
	
	/**
	 * 
	 * @getLogs
	 * Enter description here ...
	 *
	 */
	public function getLogs(){
		
		return $this->_log;
		
	}
	
	/**
	 * 
	 * @name existLogin
	 * @param unknown_type $login
	 */
	public function existLogin($login = null){
		
		if ($login === null)
			$login = $this->_login->getValue('trim');
		
		$where = $this->_db->quoteInto ( $this->_login->Db() .' = ?', $login );
		$result = $this->fetchAll ( $where );
		
		return count($result);
		
	}
	
	
	/**
	 * 
	 * @login ...
	 * @param string $login
	 * @param string $password
	 * 
	 */
	public function login($login = null,$password = null){	
		
		
		$this->initAuth();
				
	
		if ($login === null) { 
			
			
		    // Get Values via post
			$this->_loginfield->getPost();
			$this->_passfield->getPost();
			
				
			
			
			$login   		= $this->_loginfield->getValue();
			$password 		= $this->_passfield->getValue();
			$this->remember = isset( $_POST['remember'] ) ? $_POST['remember'] : null;
			
		
			
		}
		
	
		$this->_auth->setIdentity($login)->setCredential($password);
		
		
		try {
			
				
		
			
			$this->_result =  $this->_auth->authenticate();
			
			
			
		}catch(Exception $e){
			
		
				exit( print_r( $e->getMessage() ) );
			
			$this->_log[] = $e->getMessage() ;		
			
			return false;
			
			
		}
		
		
		# Get the errors results 
					
		switch ( $this->_result->getCode() ) {
	
	    	case Zend_Auth_Result::FAILURE_IDENTITY_NOT_FOUND:
	    	   
	    	    if ($login != "###")
	    	    	$this->_log[] = "Failure due to identity not being found";
	    	    	
	    	    break;
	    	  case Zend_Auth_Result::FAILURE_CREDENTIAL_INVALID:
	    	 
	    	    if ($login != "###")
	    	    	$this->_log[] = "Failure due to invalid credential being supplied";

	    	   break;
			   case Zend_Auth_Result::FAILURE_IDENTITY_AMBIGUOUS:
	    	 
	    	    if ($login != "###")
	    	    	
	    	    $this->_log[] = "Failure due to identity being ambiguous";
	    	    
	    	   break;
	
	    	case Zend_Auth_Result::SUCCESS:
			
				//Set the data user table in session
	    	    $this->setDataInSession();
				
	    	    //Trigger actions after session start
				$this->sessionHelper();
				
				$this->onSucessLogin();
					
				//Remember session if avalible
				$this->rememberSession();
	    	   
					
	    	return true;
			 
	    	break;	
	    	default:
			
	    		return false;
	    		
	    	break;	
			
		} 
		
		return false;
		
		
	}
	
	
	public function setDataInSession(){
		
			$row = $this->_auth->getResultRowObject(); 	    	   		
			$this->loadFromStdClass( $row );
			
			#Session
			$this->_session->auth 	 = $this->_result;	
			$this->_session->authRow = $row;
			$this->_session->user    = $this;
		
	}
	
	public function refreshSession( $controller ){
		
		$this->_session = $this->getSpace('Zaape_Session'.$this->_name);
		
		$this->_session->authRow = $this->getRowSet();
		
		$this->_session->user = $this;
		
		$this->sessionHelper();
		
		$this->onSucessLogin();
		
		$controller->view->session = $this->_session->user;
		
		
	}
	
	
	/**
	 * @rememberSession
	 * Enter description here ...
	 */
	public function rememberSession(){

		if( $this->remember ){
			
			 $seconds  = 60 * 60 * 24 * 365; // 1 year
			Zend_Session::rememberMe($seconds);
		
		 }
		 
	}
	
	
	
	
	
}
?>