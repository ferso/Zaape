<?php

class Zaape_Cli extends Zend_Controller_Router_Abstract{
	
	public function route (Zend_Controller_Request_Abstract $dispatcher){
		
		
		$getopt = new Zend_Console_Getopt (array ());
		$arguments = $getopt->getRemainingArgs ();
		
		if ($arguments){
			
			
		   
		    $controller = $arguments[0];
		    $action     = $arguments[1];
		    
 			if (! preg_match ('~\W~', $command)){
				
				$dispatcher->setControllerName ( $controller );
				$dispatcher->setActionName ($action );
				
				unset ($_SERVER ['argv'] [1]);

				return $dispatcher;
			}

			exit(  "Invalid command.\n" );

		}

		exit( "No command given.\n" );
	}


	public function assemble ($userParams, $name = null, $reset = false, $encode = true){
		
		exit(  "Not implemented\n" );
	
	}

}




?>