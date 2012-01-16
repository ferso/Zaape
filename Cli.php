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

			echo "Invalid command.\n", exit;

		}

		echo "No command given.\n", exit;
	}


	public function assemble ($userParams, $name = null, $reset = false, $encode = true){
		
		echo "Not implemented\n", exit;
	
	}

}




?>