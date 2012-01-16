<?php

class Zaape_Controller extends Zend_Controller_Action{
	
	

	 public function setViewMessage($var,$mode, $data = '' ){
    	  		
   		 $this->viewMessage = '<div class=\"'.$mode.'\">';
   				
			$this->viewMessage .= '<div class=\"hot\">';
			
   		 		$this->viewMessage .=  $data;
				
			$this->viewMessage .= '</div>';
   		 	
   		 $this->viewMessage .= '</div>';
			 
   		 $thisVar = '$this->view->view'.$var.' = "'.$this->viewMessage.'";';
    	 $thisvar = eval(  $thisVar );
			 
    }
    
    
    
    public function setInputError( $data, $var  ){
    	
    	$this->setViewMessage($var, 'warning',' Please fill the required elements  ');
    	
    	foreach( $data as $key => $rowset ){
    				
    			$text = '<ul class=\"red\">';
    		 	
	    			foreach( $rowset as $row => $msg ){
	   		 			
	   		 			
	   		 			$text .= "<li>$msg</li>";
	   		 			
	   		 		}	
   		 		
	   		 		
   		 		$text .= '</ul>';
   		 		
   		 		$thisVar = '$this->view->error_'.$key.' = "'.$text.'";';
    			$thisvar = eval(  $thisVar );
    	}
    	
    	
    
    }
	

}


?>