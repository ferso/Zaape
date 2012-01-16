<?php

class Zaape_Auth extends Zaape_Table{
	
	var $_name;
	var $_Model;
	var $_role   = null;
	var $_action = null;
	var $_acl    = null;
	var $_aclp   = array(); 
	var $_twitterCallback = null;
	var $_facebookCallback = null;
	
	# this is the first step to standarize access
	protected $_accesView = array('
								default'=>array('start'=>'/')
							);
	
	var $_spaces = array();
	
	public function setUpZaape(){
		
		$this->initAuth();
		
		
			
		
		$this->url		= '';
		$this->methop 	= $_SERVER["REQUEST_METHOD"];
		$this->agent 	= (isset($_SERVER["HTTP_USER_AGENT"]))?$_SERVER["HTTP_USER_AGENT"]:'NO AGENT';
		$this->ip 		= $_SERVER["REMOTE_ADDR"];
		$this->port		= $_SERVER['SERVER_PORT'];
		
		
	}
	
	public function initAuth(){
		
		
		$this->_session = $this->getSpace( 'Zaape_Session'.$this->_name );
				
	}
	
	public function getCurrentUrl(){
		
		global $_SERVER;
		return (!empty($_SERVER['QUERY_STRING']))?"http://". $_SERVER['HTTP_HOST'] .$_SERVER["PHP_SELF"]."?".$_SERVER['QUERY_STRING']:"http://". $_SERVER['HTTP_HOST'] .$_SERVER["PHP_SELF"];
		
	}
	
	
	
	public function setFormLogin ($path){
		
		$this->urlFormLogin = $path;
		
	}
	
	
	public function setSessionBy( $method){
		
		$_SESSION['session_method'] = $method; 
			
	}
	
	
	public function getSessionBy(){
		
		return $_SESSION['session_method'] ; 
			
	}
	
	
	function login( $params = array() ){
		
		$this->_session->login = 'off';	
				
		$User = new $this->_Model;
		
		
		if( $User->login() !== true){
			
			
			
			$this->_Msg = $User->getLogs();
			
			return false;
			
		}
		
		$this->_session->login = 'on';		
		$this->setRedirectLogin( $params );
			
		
	}
	
	public function createNewSession( $method = null ){
		
			switch( $method ){
					
					case 'twitter':
						
						$this->createNewSessionWithTwitter();
					
					break;
					case 'facebook':
					
						$this->createNewSessionWithTwitter();
					
					break;
					default:
					
						//$this->session
					
					break;
				
				}
				
				
	}
	
	
	
	
	
	public function start( $controller = null, $params = array( ) ){
		
		# Redefine params acces
		$params = $this->setViewRequest( $params, 'login' );
		
		$this->_last = false;
		
		
		#Set Resource
		$this->_resource = $controller; 
		
		
		#Check the session is active
		if ( !$this->aclSuccess() || ( !isset( $this->_session->login ) || $this->_session->login != 'on' )   ){ 
			
			
			$this->refer = $this->_resource->getRequest()->getHttpHost() .$this->_resource->view->url() . $_SERVER['QUERY_STRING'];
			
			if( array_key_exists('last', $params) ){
	
				$this->_session->_last = 'http://'. $this->refer;
					
				$this->_redirect = true;
				
			}
			
			$this->setReturn( $params['redirect'] ) ;			
			$this->logout( $params );
			return false;
			
			
			
		}
		
		
		
		$this->_resource->session       = $this->_session;
		$this->_resource->view->session = $this->_session;
		$this->_resource->view->Auth	= $this;
		
		return true;
	}
	
	
	public function setRedirectLogin($params){ 
		
		# Redefine params acces
		$params = $this->setViewRequest( $params, 'start' );
		
		if(  (isset($params['redirect']) && $params['redirect'] === false)  ){
			
			//header('location:'. $this->getReturn()  );
			
		}
		
		elseif ((isset($params['redirect']) && $params['redirect'] != false)){
			//$this->_session->_last  ? $this->_session->_last  : 
			
			echo $redirect = $params['redirect'];
			
			//exit(  );
			
			$this->_session->_last = null;
			
			header('location:'. $redirect );
			return true;
			
		}
		
		elseif  ( isset( $params['function'] ) ){
			
			$uri  =  $this->$params['function']() ;
			header('location:'. $uri );
			
		}
		
		elseif (isset($params['print'])){
			
				echo $params['print'];
			
			return true;
		}
		else
			return true;	
		
	}
	
	
	public function setReturn($url = false){
		
		if ($url){
			
			$this->_session->return = $url;
			
		}
		else {
			$this->_session->return = $this->getCurrentUrl();
		}
		
		//exit($this->_session->return);
	}
	 
   
	public function getReturn(){
		
		$return = $this->_session->return;
		
		
		if (empty($return) || $return == 'login' || $return == '/login'){
			
			$return = '';
		}

		return $return;
	}

	
	
	function logout($params = array ( ) ){
		
		$this->_session->login = 'off';

		
		if (isset($params['print_failed'])){
			
			echo $params['print_failed'];
			exit();
		
		}
		
		if (isset($params['redirect'])){
			
			if ($params['redirect']){
				
				header('location:'.$params['redirect']);
			
			}else{
				 
				return true;
				
			}
			
		}else{
			
			header('location:'.$this->urlFormLogin);
			
		}
		
				
	}
	
	public function isLogin(){
		
		if (!isset($this->_session->login) || $this->_session->login != 'on' ){
			
			return false;		
		}
		
		return true;
		
	}
	
	
	public function getLogs(){
		
		if( isset($this->logs) ){
		
			return $this->logs[0];	
		
		}else{
			
			return null;
				
		}
		
	}
	
	
	/**
	 * @name setAcl 
	 * 
	 * Load the Acl Modules
	 * 
	 */
	public function setAcl( $acl ){
		
		if( count($acl) > 0 ){
			
			$this->_aclp = $acl;			
			$this->_acl = true;
			
		}
		
		
		
	}
	
	
	
	/**
	 * @roleDefintion
	 * 
	 * Enter description here ...
	 */
	public function  getRole(){
		
		
		if(  isset( $this->_session->user->userRole ) ){
			
			$this->_role = $this->_session->user->userRole->getValue();
		 	
			return true;	
		}
		
		$this->_role = 'default';
		
		return true;
	}
	
	
	/**
	 * @aclSuccess
	 * Validate sucess acces role  ...
	 * 
	 */
	
	public function aclSuccess(){
		
		
		$this->getRole();
		
		if( $this->_acl ){
			
			if( $this->isRoleAllow(	) ){
				
				
				if( array_key_exists('role', $this->_aclp) ){
						
					
					
					if( $this->_aclp['role'] == $this->_role ){
							
						return true;
					
					}else{
						
						
						return false;	
					}
					
				
				}else{
					
					
					return true;	
				}
				
					
					
			}else{
				
				
				return false;	
				
			}
			
			
		}
		
		return true;
		
		
	}
	
	
	public function setViewRequest( $params, $start = 'start' ){
		
			$this->getRole();
			
			if( !array_key_exists('redirect', $params ) ){ 
					
				if( array_key_exists($this->_role, $this->_accesView) ){
					
					$params['redirect'] = $this->_accesView[ $this->_role ][$start];
					
				}else{
					
					$params['redirect'] = $this->_accesView['default'][$start];
					
				}
					
			}
			
			//exit( $params['redirect'] .' - '.$this->_role);
			
			//exit( print_r( $params ) );
			
		return $params;
		
		
	}
	
	
	
	/*
	 * @name accessdontallow
	 * 
	 * dontallow Acces by Role or Status
	 * 
	 */
	
	public function isRoleAllow(  ){
		
		if(array_key_exists('dontallow', $this->_aclp) ){
			
			#get the role 
			$this->getRole();
			
			$xp = explode(',', $this->_aclp['dontallow'] );
			
			if( !in_array($this->_role, $xp )  ){
				
				return true;
				
			}
			
			return false;
		
		}	
		
		return true;	
	
	}
	
	
	
	//////////////////////////////////////////////////////////
	
	public function socialLogin( $User ){
			
			$this->_session->login = 'on';							
		    $this->_session->user = $User;			
			
			$this->view->session = $this->_session;	
				
			$this->_resource->session  =  $this->_session;
			
		}
	
	
	public function loadTwitterAccess(){
		
		
		$registry = Zend_Registry::getInstance();
							
							if( isset( $registry['config']->api ) ){
								
							$this->key     = $registry['config']->api->twitter->key;
							$this->secret  = $registry['config']->api->twitter->secret;
							
							}else{
								
								exit('Twitter Api Authentication Undefined');	
								
							}	
		
		
	}
	
	public function twitterStart(){
		
		$this->setSessionBy('twitter');
		
		global $_REQUEST, $_POST, $_GET;
		
			if( !array_key_exists('access_token', $_SESSION ) ){
				
				
				if (isset($_GET['oauth_token']) && isset( $_SESSION['request_token'])) {
					
						#Load api acces 
						$this->loadTwitterAccess();

						
						$config = array(
							'callbackUrl' =>  $this->_twitterCallback,
							'siteUrl' => 'http://twitter.com/oauth',
							'consumerKey' => $this->key,
							'consumerSecret' => $this->secret
						);
						
						$oauth = new Zend_Oauth_Consumer($config);
						
						try{
							
							$access = $oauth->getAccessToken($_GET, unserialize($_SESSION['request_token']));  
							
							$User =  $this->successTwitterLogin( $access );
						
							
						}catch(Exception $e) {
							
						 echo 'Error: '.$e->getMessage();
						 exit (1);
						
						
						}
						
						 $_SESSION['access_token'] = serialize($access);
   				  		 $_SESSION['request_token'] = null;
						
				
			
				}elseif(!empty($_GET['denied'])){
					
					exit('you have denied us access to your twitter crendentials');
					
				} else {
					
					exit('Invalid callback request. Oops. Sorry.');
				
				}
			
			
			}else{
			
				$User = $this->successTwitterLogin(  );
					
			}
			
		
			$this->socialLogin( $User );
		
	}
	
	public function createNewSessionWithTwitter(){
		
		
		$this->loadTwitterAccess();
		
		if( $this->_twitterCallback ) {
		
		$config = array(
					'callbackUrl'    => $this->_twitterCallback,
					'siteUrl'        => 'http://twitter.com/oauth',
					'consumerKey'    => $this->key,
					'consumerSecret' => $this->secret
				);
				
				$consumer = new Zend_Oauth_Consumer($config);
				 
				try{
					
					$token 		 = $consumer->getRequestToken();					
					$oauth_token = $token->oauth_token;
					
					//store request token in session					
					$_SESSION['app_id']        = $id;
					$_SESSION['request_token'] = serialize($token);					
					$_SESSION['twitter_token'] = $oauth_token;
					
					
					header('Location: http://twitter.com/oauth/authorize?oauth_token='.$_SESSION['twitter_token']);	
				
				
				}
				
					catch(Exception $e) {
					echo 'Error: '.$e->getMessage();
					exit (1);
				
				
				}
				
				
		}else{
			
			 exit('Twitter Callback Undefined');
			
		}
		
		
	}
	
	
	
	public function createNewSessionWithFacebook(){
		
		
		
	}
	
	
	/////////////////////////////////////
	
	
	
	public function successTwitterLogin( $success ){
		
			exit('Define successTwitterLogin() Method in Model_User_Auth');	
		
	}
	
	
}




?>