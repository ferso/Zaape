<?php

class Zaape_Solution extends Zend_Controller_Action{
	
	
	public function init(){
		
		
		$this->view->controller = $this;
		
		
	}

	 public function setViewMessage($var,$mode, $data = '' ){
    	  		
   		 $this->viewMessage = '<div class=\"'.$mode.'\">';
   			
   		 		$this->viewMessage .=  $data;
   		 	
   		 $this->viewMessage .= '</div>';
			 
   		 $thisVar = '$this->view->view'.$var.' = "'.$this->viewMessage.'";';
    	 $thisvar = eval(  $thisVar );
			 
    }
    
    
    
    public function setInputError( $data, $var  ){
		
		
    	$this->setViewMessage($var, 'warning',' Please fill the required elements  ');
    	
    	foreach( $data as $key => $rowset ){
    				
    			$text = '<div class=\"red cite_error\">';
    		 		
					$msg   = end($rowset);
					$text .= "* $msg";
				
	    			/*foreach( $rowset as $row => $msg ){
	   		 			
	   		 			$text .= "* $msg";
	   		 			//$text .= "<li>$msg</li>";
	   		 			
	   		 		}	
   		 		*/
	   		 		
   		 		$text .= '</div>';
   		 		
   		 		$thisVar = '$this->view->error_'.$key.' = "'.$text.'";';
    			$thisvar = eval(  $thisVar );
    	}
		
		
	}
	
	public function printFieldMsg($field,$base){
		
		echo $message = isset( $this->view->{'error_'.$field} ) ? $this->view->{'error_'.$field} : $base ;
		
		
	}
	
	public function cacheField( $f ){
		
		if( isset( $_REQUEST )  ) {	
			
			return isset( $_REQUEST[$f] ) && !isset( $this->view->{'error_'.$f} ) ? $_REQUEST[$f] : '' ;
		
		}
		
	}

}


?>