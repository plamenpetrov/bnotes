<?php

abstract class Model_Abstract
{
    /**
     * @var object Zend_Db_Table objectda tabase table
     */
    protected $_table;

    /**
     * @var string Zend_Db_Table_Abstract class name
     *
     * Table class name
     * Replace ? with the actual class name
     * For example, 'DbTable_User'
     */
    protected $_dbTableClass = 'DbTable_?';

    /**
     * @var array of objects of Observers attached to this object
     */
    protected $_observers = array();

    /**
     * @var array the default observers 
     * The names of the observer classes
     * When the current object is instantiated these observers are attached
     * The array contains class names not objects
     */
    protected $_defaultObservers = array();


    /**
     * @var string Status of the user
     */
    protected $_status;

    /**
     * @var Ephemeral data primarily used by observers
     * Store the values in keys known to the observers
     * For example $_ephemeralData['oldLead'] is the leadData before it was updated
     */
    protected $_ephemeralData = array();


    /**
     * @var object Current user object
     */
    protected $_currentUser;


    /**
     * Set the default db adapter
     * @deprecated 
     * The default db adapter is set only to provide backward compatibility
     * All the models have to operate on Zend_Db_Table objects soon
     * The db adapter should be fetched from 
     * Zend_Db_Table_Abstract::getAdapter method where needed
     */
    public function __construct($id = null)
    {
        $this->attachDefaultObservers();
        $this->setId($id);    
        //$this->setCurrentUser();
   	}
   	
   	public function setOptions($data){
   		$methods = get_class_methods($this);
   		$keys = array_keys($data);
        foreach ($data as $k=>$value){
        	if(in_array($k, $keys)){
        		$method = 'set'.ucfirst($k);
        		$this->$method($data[$k]);
        	}
        }
   	}
   
    /**
     * Set the id of the entity
     */
    public function setId($id)
    {
    }
    
    /**
     * @return Zend_Db_Table object
     */    
    public function getTable()
    {
        if (null === $this->_table) {
            $class = $this->_dbTableClass;
            $this->_table = new $class;        
        }
        return $this->_table;
    }

    public function query($sql, $params = null, $no_return = false)
    {
    	if(!isset($params[':id_user:']))
    	{
    		$user = $this->getCurrentUser();
    		$params[':id_user:'] = $user->id;
    	}
    	$table = $this->getTable();
    	//Zend_Debug::dump($params);
    	if($params){
    		$search = array_keys($params);
    		$replace = array_values($params);
    		$sql = str_replace($search, $replace, $sql);
    	}
    	//Zend_Debug::dump($sql);die;
    	$smtp = $table->getAdapter()->query($sql);
    	if($no_return){
    		return $smtp;
    	}
    	return $smtp->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function prepareArrayForQuery($data){
        $result = array();
        foreach ($data as $k => $v){
            $result[':'.$k.':'] = $v;
        }
        return $result;
    }
    
    public function getMyEmployees(){
    	$user = $this->getCurrentUser();
    	$table = $this->getTable();
    	$smtp = $table->getAdapter()->query('select id from myEmployees(?,1)', $user->id);
    	return $smtp->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Fetch a single recrod from the persistance
     */
    public function fetch()
    {
    }       

    /**
     * Fetch all records from persistance 
     * @return Zend_Db_Table_Rowset object
     */
    public function fetchAll()
    {
        $table = $this->getTable();
        $select = $table->select();
        $result = $table->fetchAll($select);
        if ($result) {
            $result = $result->toArray();
        }
        return $result;
    }

    /**
     * Create a record in the db    
     * @return lastInsertId from the db table create operation
     */
    public function create($data)
    {
        $data = $this->unsetNonTableFields($data);
        $table  = $this->getTable();
        return $table->insert($data);
    }

    /**
     * @param array $data contains the db columns as keys and values as values
     * If the data contains other keys unset them
     * @return array $data that can be inserted into the database
     */
    public function unsetNonTableFields($data = array())
    {
        $table  = $this->getTable();
        $fields = $table->info(Zend_Db_Table_Abstract::COLS);
        if (count($data)) {
            foreach ($data as $field => $value) {
                if (!in_array($field, $fields)) {
                    unset($data[$field]);
                }
            }
        }
        return $data;
    }

    /**
     * Edit a record in the db
     * @param array $data contains the edited data 
     */
    public function save($data = array())
    {
    }

    /*
     * Delete a record from the database
     */
    public function delete()
    {
    }

    /**
     * Attach the observer
     * @param object $object to be attached
     */
    public function attach($object)
    {
        $this->_observers[] = $object;
    }

    /*
     * Default observers of the application
     */
    public function attachDefaultObservers()
    {
        $observers = $this->_defaultObservers;
        foreach ($observers as $observer) {
            $object = new $observer;
            $this->attach($object);
        }
    }

    /**
     * Notify all observers
     */
    public function notify()
    {
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
                    
    }

    /**
     * @return status of the object
     */
    public function getStatus()
    {
        return $this->_status;
    }

    /**
     * @return status of the object
     */
    public function setStatus($status = null)
    {
        $this->_status = $status;
        $this->notify();
        return $this;
    }

    /**
     * @return array of ephemeral data
     */
    public function getEphemeral()
    {
        return $this->_ephemeralData;
    } 

    /**
     * Fetch the data before we lose it
     * we lose data when it is updated or deleted
     */
    public function prepareEphemeral()
    {
        $this->_ephemeralData = $this->fetch();
    }

    /**
     * @return @Bvb_Grid object
     */
    public function getPaginator($type, $ini_file, $rows)
    {
    	//TODO да се направи оптимизация на инициализацията на грида
		$baseConfig = new Zend_Config_Ini(APPLICATION_PATH .'/cache/tmp/grid/base.ini');
		$baseConfig = $baseConfig->toArray();
    	$name = md5(time());
    	file_put_contents(APPLICATION_PATH .'/cache/tmp/grid/'.$name.'.ini',
    													 $ini_file);
    	$config = new Zend_Config_Ini(APPLICATION_PATH .'/cache/tmp/grid/'.$name.'.ini');
    	unlink(APPLICATION_PATH .'/cache/tmp/grid/'.$name.'.ini');
    	$config = $config->toArray();
    	if(isset($config['production'])){
    		$config = $config['production'];
    	}
    	
    	$config = array_merge($config, $baseConfig);
    	
    	$grid = Bvb_Grid::factory($type, $config);
       	$grid->setExport(array('excel','word','pdf', 'print'));
       	$source = new Bvb_Grid_Source_Array($rows);
    	$grid->setSource($source);
    	$grid->setTranslator($this->getTranslate());
		$grid->setAlwaysShowOrderArrows(false);
		
    	return $grid;
    	
    }

    /**
     * @return object current user
     */
    public function getCurrentUser()
    {
        return Zend_Registry::get('user');
    }
    
    private function setCurrentUser()
    {
    	$this->_currentUser = $this->getCurrentUser();
    }

    /**
     * @return object Zend_Cache
     */
    public function getCache()
    {
        return Zend_Registry::get('cache');
    }
    
	/**
     * @return object Zend_Translate
     */
    public function getTranslate()
    {
        return Zend_Registry::get('Zend_Translate');
    }
    
    public function isAdmin() {
        return 1;
    }
    

}

