<?php

/**
 * Zaape Extension Framework
 *
 * Class for Models
 *
 * @category   Zaape
 * @package    Zaape_Table
 * @copyright  No Applied
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    00.1
 */

/** @see Zend_Db_Table_Abstract */

class Zaape_Table extends Zend_Db_Table_Abstract {
	
	
	protected $_prefix 		= null;

	protected $_vars 		= array( );
	protected $_sessionVars = array( );
	protected $_formVars 	= array( );
	protected $_formValues 	= array( );
	public $_bindObjects 	= array( );
	public $Errors 			= array();
	public $Ecode;
	protected $_conn 		= null;
	protected $_rowSet 		= null;
	public $_row 			= null;
	public $_solution 		= null;
	public $_log 			= null;
	public $_selectQuery 	= null;
	public $_prCache  		= null;
	public $_pointer 		= 0;
	public $_session 		= null;
	protected $_Msg         = null;
	protected $_msgSuccess  = null;
	protected $initCountRow = null;
	protected $paging 		= null;
	protected $_meta		= null;

	/**
	 * init().
	 *
	 *  Initialize functions for Model Metadata
	 *
	 * @see Zend_Db_Table_Abstrac::_SetupMetada()
	 * @see Zend_Db_Table_Abstrac::_setupPrimaryKey()
	 *
	 * @param  void.
	 * @return void
	 */
	public function init() {

		try {
			
			$this->_setupMetadata();
			
		} catch (Exception $e) {
		    
		   
			//die(var_dump($this));
			
		}

		$this->_setupPrimaryKey();
		$this->_setUpZaape();
		$this->initZaape();
		$this->setUpZaape();
		//$this->getCache();


	}
	
	
	
	/**
	 * test().
	 *	 
	 * Print Metadata table and checkout all its ok 
	 */
	public function test(){
		
		echo "<pre>";
		echo "<p>Hi World ;) </p>";
		echo "";
		
		
		echo "</p>Metadata Table: $this->_name </p>";
		echo "</p>------------------------</p>";
		print_r( $this->_metadata  );
		
		
		echo " </pre>";
		
	}


	/**
	 * _setUpZaape().
	 *
	 *  Convert to Object to each column in the table
	 *  this information comes from metadata information
	 *
	 *
	 * @param  void
	 * @return void
	 *
	 */
	protected function _setUpZaape() {

		$i = 0;
		
		foreach ( $this->_metadata as $colum_name => $column_config ) {
				
			$camell = $this->camelize ( $colum_name, $this->_prefix );
				
			$this->$camell = $this->addVar ( $colum_name ) ;

			$this->$camell->type ( $column_config ['DATA_TYPE'] );
				
			$i++;
				
		}

			
	}

	/**
	 * setUpZaape().
	 *
	 * This a auxiliar function to start the model
	 *
	 *
	 * @param  void
	 * @return void
	 *
	 */
	public function setUpZaape(){

		//Actions here....
	}


	/**
	 * initZaape().
	 *
	 *  This a auxiliar function to start the model
	 *
	 *
	 * @param  void
	 * @return void
	 *
	 */
	public function initZaape(){

		//Actions here....

	}

	/**
	 * setName().
	 *
	 *  Set the table name in the model
	 *
	 *
	 * @param  String $name;
	 * @return void
	 *
	 */
	public function setName($name){

		$this->_name = $name;

	}

	/**
	 * __get
	 *
	 * se utiliza para consultar datos a partir de propiedades inaccesibles. ...
	 * Se invoca a los metodos de sobrecarga cuando se interactua con propiedades
	 * o metodos que no se han declarado o que no son visibles en el ambito activo.
	 *
	 * @see http://php.net/manual/es/language.oop5.overloading.php
	 *
	 * @param String $name
	 */
	public function __get( $name )
	{
		$namelower = strtolower( $name );

		if ($this->_rowSet instanceof Zend_Db_Table_Rowset_Abstract) {
				
			if ($this->_rowSet->valid () && isset($this->_rowSet->$namelower)) {

				$this->$name = $this->addVar($namelower);
				$this->$name->setValue($this->_rowSet->$namelower);

				return true;
			}
				
			return false;

		}elseif ( isset ( $this->_rowSet [ 0 ][ $namelower ] ) ){
				
			$this->$name = $this->addVar($namelower);
			$this->$name->setValue( $this->_rowSet[0][$namelower] );
			return $this->$name;
				
		} else if ( isset ($this->_rowSet[0]) ){
				
			$row = $this->_rowSet[0];
				
			foreach($row as $column => $value){

				$column_name = str_replace('_','',strtolower( $column ));

				if ( $column_name == $namelower ){
						
					$value =  $this->getRowSetValue($column);
					$this->$name = $this->addVar($column);
					$this->$name->setValue( $value );
					return $this->$name;

				}
					
			}
				
			return false;
		}

		return false;
	}
	
	
	public function __call( $function_name , $vars){
		
		exit( $function_name );
	
	}

	/**
	 *
	 * Enter description here ...
	 * @param stdClass $row
	 */
	public function loadFromStdClass(stdClass $row){
		$row = ( $row === null ) ? $this->_row : $row ;

		foreach($this->_vars as $column => $var)
		if (isset($row->$column))
		$var->setValue($row->$column);
	}

	/**
	 *
	 * camelize()
	 *
	 * Convert a string into camell format
	 *
	 * @param String $name
	 * @param String $prefix
	 * @return String $name_camelize
	 */
	public function camelize($name,$pre = '') {
		if ($name != '')
		$name = str_replace ( $pre, '', strtolower ( $name ) );

		$name = '_' . str_replace ( '_', ' ', strtolower ( $name ) );
		return ltrim ( str_replace ( ' ', '', ucwords ( $name ) ), '_' );
	}


	/**
	 * Se agrega una variable Zaape_Var a la lista de variables de Model_Object.
	 *
	 * @param String $name
	 * @return Zaape_var (Zaape Var)
	 */
	protected function addVar($name) {

		$Object = new Zaape_Field();
		$Object->set ( $name );
		
		return $this->_vars [$name] = $Object;


	}

	/**
	 * Alimenta las variables del objeto apartir de un arreglo();
	 * extFunt: funciones a disparar antes de asignar el valor
	 * separadar por comas.
	 *
	 * @param array() $array
	 * @param String $extFunt
	 */
	public function loadFromArray($array,$extFunt = '') {

		foreach ( $this->_vars as $var ) {
				
			if (isset ( $array [ $var->Db ()] ) ) {

				$var->setValue ( $array [$var->Db ()], $extFunt );
			}
				
		}
		
		
		

	}


	/*
	 * @name setSelectString
	 * @param Zend_Select $select
	 * Salva el query string creado por el Zend_Select
	 */

	public function setSelectString( $select ){

		if( isset( $select ) ){
			
			if( !is_string($select) ){
				
				$this->_selectQuery = $select->__toString();
				
				return;
			}
			
			$this->_selectQuery = $select;
			
		}
		

	}

	/*
	 * @name getSelectString
	 * Salva el query string creado por el Zend_Select
	 */

	public function getSelectString( ){

		return $this->_selectQuery;

	}

	/**
	 * Regresa el rowSet del objeto.
	 * Dependiendo si el rowSet es un objeto o un Array multidimencional.
	 * Regresa una copia o un puntero al rowSet.
	 *
	 * @return &$rowSet(Zen_Db_table_RowSet)| $rowSet(array())
	 */
	public function getRowSet() {

		return $this->_rowSet;

	}


	/**
	 * Permite asignar un rowSet desde fuera de la clase.
	 * NOTA: Esto se hace cuando ahi querys muy elaborados.
	 * o se alimentan a varios objetos con el mismo resultado.
	 *
	 * @param unknown_type $rowSet
	 */
	public function setRowSet( $rowSet ) {

		$this->_rowSet = $rowSet;
 
		if ( $rowSet instanceof Zend_Db_Table_Rowset_Abstract) {

			$this->_rowSet = $rowSet->toArray();
			
			//print_r( $this->_rowSet );

		}




	}
	
	
/**
	 * Recorreo el puntero interno del rowSet a la siguiente posicion.
	 * y carga los valores a las variables del objeto.
	 * Si el rowSet llego a su final regresa false.
	 * en caso contrario regresara true.
	 *
	 * @return boolean
	 */
	public function next() {
			
			$this->clean ();
		
			if (isset ( $this->_rowSet [$this->_pointer] )) {
				
			
				$this->loadFromArray ( $this->_rowSet [$this->_pointer] );
				
				foreach ( $this->_bindObjects as $object ) {
					
					$object->clean();
					$object->loadFromArray ( $this->_rowSet [$this->_pointer] );
					
				}
			
				
				$this->_pointer ++;
				return true;
				
			} else {
				
				return false;
				
			}
		
		
		return false;
	}
	
	
	/**
	 * 
	 * Count rowset  ...
	 */
	public function count(){
		if (isset($this->_rowSet) )
			return count($this->_rowSet);
		else
			return 0;
	}
	
	/**
	 * 
	 * getTotals from query ...
	 * 
	 * 
	 */
	public function getTotals(){
		
		return $this->_db->fetchOne('SELECT FOUND_ROWS()');
		
	}

	////////////////////////////////////////////////////////////////

	
/**
	 * Convierte todas las variables del objeto a un arreglo.
	 *
	 * array('nombre_variableA' => 'valor_variableA');
	 *
	 * Puedes pasar un arreglo con variables Zaape_vars y solo esas seran convertidas a un arreglo.
	 *
	 * @param array $vars
	 * @return array()
	 */
	public function toArray($vars = null) {

		$array = array ( );

		if ($vars === null)
		$vars = $this->_vars;

			
		if (is_array($vars)){
			foreach ( $vars as $var ){

				$array [$var->Db ()] =$var->getValue () ;
			}
		}
		else {
			$this->log[] = "Error: toArray() no valid array";
		}

		return $array;
	}



	

	////////////////////////////////////////////////////////////////

	/**

	* @param Zaape Var $vars
	*/
	public function insert($vars = null) {


		if ($vars === null)

		$vars = $this->_vars;

		try {

			$this->_db->insert ( $this->_name, $this->toArray ( $vars ) );
				
		} catch ( Zend_Db_Statement_Mysqli_Exception $e ) {

			echo "<br>" . $e . "<br>";
				
			return false;
		}

		if (count ( $this->_primary ) == 1) {
				
			$this->_vars [$this->_primary [1]]->setValue ( $this->_db->lastInsertId () );
				
		}

		return true;
	}

	/**
	 * Actualiza la base de datos con los valores actuales de las variables de la clase.
	 * Si no pasas un arreglod e variables a la funcion, tomara todas las variables de la clase
	 * para realizar el update.
	 *
	 * Si no pasas una llave primaria, tomara las valores actuales de la clase como llave primaria.
	 *
	 * @param Zaape_Var[] $vars
	 * @param array() $primary
	 * @return void
	 */
	public function update($vars = null,$primary = null) {

		if ($vars === null){
				
			$vars = $this->_vars;
				
		}

		try {
				
			$update = $this->_db->update ( $this->_name, $this->toArray ( $vars ), $this->wherePrimaryKey ( $primary ) );
				
			return $update;
				
		} catch ( Zend_Db_Statement_Exception $e ) {
				
			echo "<br>" . $e . "<br>";
			
			return false;
				
		}

	}

	/**
	 * Borra el registro de la base de datos.
	 * Basandoce en su llave primaria.
	 *
	 * @param array()|String $primary
	 * @return boolean
	 */
	public function delete($primary = null) {
		return parent::delete ( $this->wherePrimaryKey ( $primary ) );
	}


	///////////////////////////////////////////////////////////

	/**
	 * Obtiene el valor de la columna y el puntero indicado dentro del rowSet Actual.
	 *
	 * @param String $name_columns
	 * @param int $pointer
	 * @return unknown
	 */
	public function getRowSetValue($name_columns, $pointer = null) {
		if ($pointer === null){//Si no se pasa un puntero toma el actual..
			$this->_pointer = ($this->_pointer > 0)?$this->_pointer:1;
			$pointer = $this->_pointer - 1;
		}

		if (isset ( $this->_rowSet [$pointer] [$name_columns] ))
		return $this->_rowSet [$pointer] [$name_columns];
	}

	/**
	 * Establece el valor de todas las variables del objeto a
	 * value = '';
	 *
	 */
	public function clean() {

		foreach ( $this->_vars as $var )
		$var->setValue ( '' );
	}

	/**
	 * Carga los valores del RowClass a las variables del objeto.
	 *
	 * @param Zend_Db_Table_Row_Abstract $row
	 */
	public function loadFromRowClass(Zend_Db_Table_Row_Abstract $row = null) {

		$row = ($row === null) ? $this->_row : $row;

		foreach ( $this->_vars as $column => $var )

		if (isset ( $row->$column ))
		$var->setValue ( $row->$column );

	}


	/////////////////////////////////////////////////////////////


	public function getCache($minutes = 120){

		$config = Zend_Registry::get('config');
			
		if( $config->cache ){
				
				

			$frontendOptions = array(

		         'lifetime' => $minutes, // cache lifetime of 2 hours
			 
		         'automatic_serialization' => true

			);

			$backendOptions = array(

		      			'cache_dir' => realpath( $config->cache->path )

			);
			 
			//getting a Zend_Cache_Core object
			$this->_prCache = Zend_Cache::factory('Output', 'File',  $frontendOptions,  $backendOptions);

		}


	}

	/////////////////////////////////////////////////////////////
	/**
	* Imprime una tabla con todas las variables del objeto y sus valores.
	*
	*/
	public function printValues() {
		echo "\n<table class='printValues'>";
		echo "\n<tr class='top'><th><b>Nombre</b></th><th><b>Valor</b></th></tr>";
		foreach ( $this->_vars as $var ) {
			echo "\n<tr><td>" . $var->getName () . "</td><td>" . $var->getValue () . "</td></tr>";
		}
		echo "\n</table>";
	}

	/////////////////////////////////////////////////////////////


	/**
	 * Regresa las columnas que componen la llave primaria
	 *
	 * @return unknown
	 */
	public function _primary() {
		return $this->_primary;
	}

	/**
	 * Regresa las columnas que componen la llave primaria
	 * con sus valores actuales.
	 *
	 * @return unknown
	 */
	public function _primaryArray() {
		$primary = array ( );

		foreach ( $this->_primary as $column_name )
		$primary [$column_name] = $this->_vars [$column_name]->getValue ();

		return $primary;
	}

	/**
	 * Regresa el primary del objeto actual.
	 *
	 * @return primary
	 */
	public function getId() {
		$id = array ( );

		foreach ( $this->_primary as $column_name )
		$id [] = $this->_vars [$column_name]->getValue ();

		if (count ( $id ) == 1)
		list ( $id ) = $id;
		return $id;
	}


	/**
	 * Regresa un arreglo:
	 * Key: los nombres de las columnas que componen la llave primaria.
	 * Value: los valores que recive como parametro que forman la llave primaria que se esta buscando.
	 *
	 * @param unknown_type $array
	 * @return unknown
	 */
	public function wherePrimaryKey($array = null) {
		$where = array ( );

		if ($array !== null) {
			if (! is_array ( $array ))
			$array = array ($array );

			foreach ( $this->_primary as $key => $column_name ) {

				$where [] = '`'.$column_name.'`' . ' = ' . $this->_db->quote ( (isset ( $array [$key - 1] )) ? $array [$key - 1]  : '' );
			}
		} else {
			foreach ( $this->_primary as $key => $column_name ) {

				$where [] = '`'.$column_name.'`' . ' = ' . $this->_db->quote ( $this->_vars [$column_name]->getValue ()  );
			}
		}

		return $where;
	}

	/////////////////////////////////////////////////////////////

	/**
	 * Carga un registro de la base de datos que concida con la llave primaria.
	 * Sinonimo de $this->loadByPrimary();
	 *
	 * @param array()|String|int $id
	 * @return boolean
	 */
	public function loadById($id = null) {
		return $this->loadByPrimary ( $id );
	}


	public function loadByQuery( $query ) {

		$this->_pointer = 0;
		$this->_last_query = $query;
		$this->_rowSet = $this->_db->query( $query )->fetchAll();

	}

	/**
	 * Carga un registro de la base de datos que concida con la llave primaria.
	 *
	 * @param array()|String|int $id
	 * @return boolean
	 */
	public function loadByPrimary($array = null)
	{
		$this->_pointer = 0;
		if ($array === null && trim($this->_vars [$this->_primary[1]]->getValue ()) == '')
		return false;
			
			
		$strWhere = implode ( ' AND ',  $this->wherePrimaryKey ( $array ));

		$this->_rowSet = $this->fetchAll ($strWhere );

		$count = count ( $this->_rowSet );

		if ($count == 1)
		return $this->next ();
		else
		return ($count > 0) ? $count : false;
		
	}

	/**
	 * Cargar todos los registros de la tabla en el rowSet;
	 * Similar a un: select * from tabla;
	 *
	 */
	public function loadAllDb( $strWhere = '' ) {

		$this->_pointer = 0;
		$this->_rowSet = $this->_db->query('SELECT * FROM '. $this->_name .' '. $strWhere )->fetchAll();

	}


	/**
	 * Carga cache de un query via id
	 *
	 */
	public function loadByCache( $name, $select = null ) {

		if( isset($this->_prCache ) ){

				
			if( !( $result = $this->_prCache->load( $name ) )  ) {

				$result = $this->fetchAll( $select );
				 
				$this->setSelectString( $select );
					
				$this->_prCache->save( $result );
					
				$this->setRowSet( $result );

			}else{
					
				$this->setRowSet( $result );
					
			}


		}

	}
	
	
	//////////////////////////////////////////////////////////////////////////////

	/**
	 *
	 * Get a new mysql guid
	 *
	 */
	public function getUnquid( $prefix = null ){
			
		//$prefix = $prefix ? $prefix : $this->_name;			
		return uniqid(  $prefix );
		
		
	}

	/**
	 *
	 * Get a new mysql guid
	 *
	 */
	public function getGuid(){

		$guid      = $this->_db->fetchCol ( 'SELECT UUID()' );
		return $guid[0];

	}


	/**
	 *
	 * Force a Database close ...
	 */
	public function DBClose(){

		/// Close Database
		$this->conn = $this->_db->getConnection();

		$result = @$this->_db->query( 'SHOW FULL PROCESSLIST' );
		while ( $row = $result->fetch() ) {
				
			$process_id = $row["Id"];
				
			if ($row["Time"] = 50 ) {

				$sql = "KILL $process_id ";
				$this->conn->query( $sql );

			}
				
		}
		

	}


	/**
	 *
	 * get a session space ...
	 * @param String $name
	 */
	public function getSpace($name){

		if (!isset($this->_spaces[$name])){
			$this->_spaces[$name] = new Zend_Session_Namespace($name);
		}

		return $this->_spaces[$name];

	}




	/*----------- MSGs ----------------------*/


	/**
	 *
	 * Set the new message data for an action ...
	 * @param String $msg
	 */
	public function setSuccess( $msg ){

		$this->_msgSuccess = $msg;

	}


	/**
	 *
	 * Verify if succes msg is define in the process ...
	 */
	public function isSuccess(){


		if( $this->_msgSuccess ){
				
			return true;
				
		}

		return false;

	}

	/**
	 *
	 * pending for now ...
	 *
	 */
	public function getSuccessMsg(){

		return $this->_msgSuccess;

	}

	/**
	 *
	 * pending for now ...
	 *
	 */
	public function getSuccessMsgList(){


	}


	/**
	 * get a Form Error in Json format from a Zend_Form validation ...
	 *
	 */
	public function getJsonError(){

		return $this->_Msg;

	}

	/**
	 *
	 * Return errors from Zend_Form validation ...
	 * @return String _Msg
	 */
	public function getMessages(){

		return $this->_Msg;

	}

	public function getCodeResult(){

		return $this->ECode;
	}

	/**
	 *
	 * create a list from a Error Msg from Zend_Form validation
	 */
	public function listErrors(){

		if( $this->_Msg ) {
				
			$str = '<ul>';
				
			$strli = '';
				
			foreach( $this->_Msg as $key => $errors  ){

				$strli .= '<li>';

				$strli .= "<strong>$key</strong> <br>";

				$strvalue = '';

				foreach( $errors as $value  ){

					$strvalue .= $value;

				}
					

				$strli.= $strvalue .'</li>';



			}
				
			return $str .= $strli . '</ul>';

		}

	}

	/**
	 *
	 * Return the input error from Zend_Form validation...
	 * @param varchar $input
	 * @param unknown_type $context
	 */
	public function getInputError( $input = null, $context = null ) {
		if( $this->_Msg ) {
			// Check if the $input key exists in the array to get error messages
			if (array_key_exists($input, $this->_Msg))
			{
				// Create the message box for each value
				foreach( $this->_Msg[$input] as $value  ) {
					if( $context ) {
						$context   = explode(':',$context);
						$div   = $context[0];
						$class = $context[1];
						return "<$div class=\"$class\"> $value </$div>";
					} else {
						return  $strvalue = $value;
					}
				}
			}
		}
	}
	
	
	/**
	 * @name pagination
	 * Return the input error from Zend_Form validation...
	 * @param varchar $input
	 * @param unknown_type $context
	 */
	public function pagination(){

		global $_REQUEST;
		
		$this->page 	    = $_REQUEST['page'];
		$this->pageRows     = $_REQUEST['rows'];
		$this->initCountRow =  ( $this->page - 1) * $this->pageRows;
		
		$this->paging = true;
		
	} 
	
	/**
	 *
	 * Return the input error from Zend_Form validation...
	 * @param varchar $input
	 * @param unknown_type $context
	 */
	public function ZaaTable( $totals = null ){
		
		$page = $this->page;
		$rows = $this->pageRows;
		
		$totals = $totals ? $totals : $this->getTotals();
		$pages  = ceil(  $totals / $rows ) ;
		$pages  = $totals <= $rows  ? 1 : $pages;
		
		$rowset 		 = $this->getRowSet();
		$string          = array();
		$string['pages'] = array('total'=>$pages,'current'=>$page,'rows'=>$totals);		
		$string['data']  = $rowset;
		
		return $data = ( ( Zend_Json_Encoder::encode( ($string) ) )) ;
		
	}
	
	
	
	
	
 
	 
}
