<?php

class Zaape_Language extends Zend_Controller_Plugin_Abstract{
	
	public function fullUrl($url) {
		
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $url = $request->getScheme() . '://' . $request->getHttpHost() . $url;
        return $url;
        
    }
	
	public function preDispatch( Zend_Controller_Request_Abstract $request ){
		
		$langs      = array('en','es','pt-br','de','fr');
		
		$controller = $this->getRequest()->getControllerName();
		$lang		= $this->getRequest()->getParam('lang');	
	
		//Zend Local
		$zl 		= new Zend_Locale(Zend_Locale::BROWSER); 
		
		
		// get Front Controller
		$view		= Zend_Layout::getMvcInstance()->getView();
		$front 		= Zend_Controller_Front::getInstance();
		$router 	= $front->getRouter();
		
		//Define language by default
		if( $lang == ":lang" ){
			
			$lang = 'en';	
			$this->_response->setRedirect("/$lang"); 
			
		
		}else{

			
			if( !in_array($lang, $langs) ){
						
				$url =  "/en/$lang/";
				$this->_response->setRedirect( $url );
				$lang = 'en';
			}
			
						
		}
		
		$this->setLanguange($lang);
		
	}
	
	public function setLanguange( $lang ){
		
			//Country Code for display local contact info
			$ip          =  "189.204.37.131"; //$_SERVER['SERVER_NAME'] == 'hr' ? '189.204.37.131' : $_SERVER['REMOTE_ADDR'];
			$client      = new Zend_Http_Client('http://freegeoip.appspot.com/json/'.$ip);
		    $response    = $client->request(Zend_Http_Client::GET)->getBody();
		   	$response    = Zend_Json::decode($response, Zend_Json::TYPE_OBJECT);
			$country     = $response->countrycode;
		    $cname 	     = strtolower($response->countryname);
		    
		    $view->CountryData = $response;
		   	    
	    			
			switch( $lang ){
				
				case 'en':
					
					$source = 'en_us';
					
				break;
				case 'es':
					
					$source = 'es_mx';
					
				break;
				
			}
			
			/////////////////////////////////////////////////////
		
			//----- Try to open csv file --- //
			try {
				
				@$translate = new Zend_Translate('csv', APPLICATION_PATH . '/languages/'. $source . '/static.csv' , $lang);
				@Zend_Registry::set('Zend_Translate', $translate);
				
			} catch (Exception $e) {
				
				@$translate = new Zend_Translate('csv', APPLICATION_PATH . '/languages/en_us/static.csv' , $lang);
				@Zend_Registry::set('Zend_Translate', $translate);
				
				
			}
		
	}
	
}