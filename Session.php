<?php

require_once 'Zend/Session/SaveHandler/Interface.php';

class Zaape_Session implements Zend_Session_SaveHandler_Interface{
	
    /**
     * This is instance of Zaape_Session, which extends Zend_Db_Table and manages the database connection
     *
     * @var Zaape_Session
     */
    private static $sessionData;
 
    private static $thisIsOldSession = false;
	private static $originalSessionId = '';
 
    public function open($save_path, $name) {
    	
        self::$sessionData = new Model_Session;
        return true;
    }
 
    public function close(){
 
        return true;
    }
 
    public function read($id){
    	
       $rows = self::$sessionData->find($id);
        $row = $rows->current();
        
        if ($row){
        	
            self::$thisIsOldSession = true;
			self::$originalSessionId = $id;
            return $row->session_data;
            
        }else{
        	
            return '';
        }
    }
 
    public function write($id, $sessionData){

    	$data = array ( 
    		'session_data' => $sessionData,
            'modified' => new Zend_Db_Expr('NOW()'),
        );
 
		if (self::$thisIsOldSession && self::$originalSessionId != $id){
            // session ID is regenerated, so set $thisIsOldSession to false, so we insert new row
            self::$thisIsOldSession = false;
        }
 
        if (self::$thisIsOldSession){
        	
            self::$sessionData->update($data,self::$sessionData->getAdapter()->quoteInto('session_id = ?', $id) );

        }else{
        	
            //no such session, create new one
            $data['session_id'] = $id;
            $data['modified'] = new Zend_Db_Expr('NOW()');
            self::$sessionData->insert($data);
            
        }
 
        return true;
    }
 
    public function destroy($id) {
        
    	self::$sessionData->delete(self::$sessionData->getAdapter()->quoteInto('session_id = ?', $id));
        return true;
        
    }
 
    public function gc($maxLifetime) {
        
    	$maxLifetime = intval($maxLifetime);
        self::$sessionData->delete("DATE_ADD(modified, INTERVAL $maxLifetime SECOND) < NOW()");
        return true;
        
    }
    
    
}