<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	
	
	
	protected function _initResourceAutoloader()
	{
	     $autoloader = new Zend_Loader_Autoloader_Resource(array(
	        'basePath'  => APPLICATION_PATH,
	        'namespace' => '',
	     ));
	     $autoloader->addResourceType( 'model', 'models', 'Model');
	     $autoloader->addResourceType( 'form', 'forms', 'Form');
	     return $autoloader;
	}
   protected function _initView()
    {
        $viewResource = new Zend_Application_Resource_View();
        $view = $viewResource->init();
        $view->setEncoding('utf-8');
        $view->headMeta()->appendHttpEquiv('Content-Type',
                                   'text/html; charset=utf-8')
                 ->appendHttpEquiv('Content-Language', 'bg-BG');
        $view->addHelperPath('ZendX/JQuery/View/Helper', 'ZendX_JQuery_View_Helper');
        $view->addHelperPath('S/View/Helper', 'S_View_Helper');
        $view->addScriptPath(APPLICATION_PATH . '/../library/S/View/Partial');
		$view->headTitle()->setSeparator(' - ');
		$view->jQuery()->enable();
		
        return $view;
    }
    protected function _initConfig ()
    {	
    	try
    	{	
    		$config3 = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
    		$domain = $config3->production->domain;
    		$database = substr($_SERVER["SERVER_NAME"],0,-strlen($domain));
	    	$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/'.$database.'.ini');
	    	$config2 = new Zend_Config_Ini(APPLICATION_PATH . '/configs/database.ini');
	    	
    	}catch(Exception $e)
    	{	
    		Zend_Debug::dump($e->getMessage());
    		$response = new Zend_Controller_Response_Http();
            $response->setRedirect('/auth/logout');
    	}
    	Zend_Registry::set('config', $config);
    	Zend_Registry::set('config2', $config2);
    	Zend_Registry::set('application', $config3);
    	
    	$this->bootstrap('layout');
    	$layout = $this->getResource('layout');
    	$view = $layout->getView();
    	if(isset($config->email))
    	{
    		$view->emailname = $config->email->name;
    		$view->emaildomain = $config->email->domain;
    	}
    	return $config;
    }
    protected function _initDatabase ()
    {
        $db = null;
        $config = $this->bootstrap('config')->getResource('config');
        $config2 =  Zend_Registry::get('config2');
        if (! empty($config)) {
            $db = Zend_Db::factory($config->database);
            $db->setFetchMode(Zend_Db::FETCH_ASSOC);
            $db->query(new Zend_Db_Expr('SET NAMES utf8'));
            Zend_Registry::set('db', $db);
            Zend_Db_Table_Abstract::setDefaultAdapter($db);
        }
        
    	if (! empty($config2)) {
            $db2 = Zend_Db::factory($config2->database);
            $db2->setFetchMode(Zend_Db::FETCH_ASSOC);
            $db2->query(new Zend_Db_Expr('SET NAMES utf8'));
            Zend_Registry::set('db2', $db2);
        }
        return $db;
    }
    
     protected function _initUser()
    {
        $this->bootstrap('database');
        $db = $this->getResource('database');
        if (!$db) {
            return;
        }

        $auth =  Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $user = $auth->getStorage()->read();
            if($user) {
            	$profileModel = new Model_Profile();
		       	$profileModel->setId($user->id_profile);
		       	$profile = $profileModel->fetch();
		       	$this->bootstrap('layout');
		        $layout = $this->getResource('layout');
				$view = $layout->getView();
				$view->profile = $profile;
				
		        Zend_Registry::set('profile', $profile);
		        Zend_Registry::set('user', $profile);
		        return $user;
            } else {
            	 Zend_Auth::getInstance()->clearIdentity();
            }
        } else {
            Zend_Auth::getInstance()->clearIdentity();
        }
    }    
	/*	protected function _initNavigation ()
    {
    	$this->bootstrap('layout');
		$layout = $this->getResource('layout');
		$view = $layout->getView();
		$navObj = new Model_Navigation();
		$navConfig = $navObj->fetch();
		$this->bootstrap('translator');
		$translator = $this->getResource('translator');
		//$nav = new S_Navigation($navConfig, $translator);
		//$this->view->navigation = $nav;
    }
    
    protected function _initMainNavigation ()
    {
    	$this->bootstrap('layout');
    	$layout = $this->getResource('layout');
    	$view = $layout->getView();
    	$navObj = new Model_Navigation();
    	$navConfig = $navObj->fetchMainMenu();
    	$this->bootstrap('translator');
    	$translator = $this->getResource('translator');
    	$navMain = new S_Navigation($navConfig, $translator);
    	$this->view->navigationMain = $navMain;
    }*/
  
    
     protected function _initRequest()
    {
        if (PHP_SAPI == 'cli') {
            return;
        }
        $this->bootstrap('session');
        /** 
         * Ensure the front controller is initialized
         */
        $this->bootstrap('FrontController');

        // Retrieve the front controller from the bootstrap registry
        $front = $this->getResource('FrontController');
        $this->bootstrap('config');
        $config = $this->getResource('config');
        $front->setParam('config', $config); 

        $request = new Zend_Controller_Request_Http();
        $request->setBaseUrl('/');
        $front->setRequest($request);

        /** 
         * Ensure the request is stored in the bootstrap registry
         */
        return $request;
    }
    
    /**
     * Initialize the session
     * @return void
     */
    protected function _initSession()
    {
        if (PHP_SAPI == 'cli') {
            require_once 'Zend/Session.php';
            Zend_Session::$_unitTestEnabled = true;
            return;
        }
        Zend_Session::start();
        return null;
    }
    
	
    
	protected function _initTranslator(){
	
		$domain = Zend_Registry::get('application')->production->domain;
        if (!isset($_COOKIE['lang'])) {
        	setcookie('lang', 'bg', time()+30672000, '/',$domain );
        	$lang = 'bg';
        } else {
        	$lang = $_COOKIE['lang'];
        }
        try
        {	
        	$path = APPLICATION_PATH . DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'languages'.DIRECTORY_SEPARATOR.$lang.DIRECTORY_SEPARATOR.$lang.'.ini';
	        $contentObj = new Zend_Config_Ini($path);
	        $translate = new Zend_Translate(
	        		array(
	        				'adapter' => 'array',
	        				'content' => $contentObj->toArray(),
	        				'locale'  => $lang
	        		)
	        );
	        Zend_Validate_Abstract::setDefaultTranslator($translate);
	        Zend_Form::setDefaultTranslator($translate);
	        Zend_Registry::set('Zend_Translate', $translate);
        	$this->bootstrap('layout');
	        $layout = $this->getResource('layout');
			$view = $layout->getView();
			
			$view->domain = $domain;
	        return $translate;
        }    
	    catch(Exception $e)
        {	
            $translate = new Zend_Translate(
            		array(
            				'adapter' => 'array',
            		)
            );
           
        	return $translate;
        }
       
    }
    
    protected function _initTaskCategory()
    {
    	$this->bootstrap('database');
        $db = $this->getResource('database');
        if (!$db) {
            return;
        }
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
		$view = $layout->getView();
        $taskModel = new Model_Task();
		$view->cats = $taskModel->fetchCategory();
		$view->taskdue = $taskModel->fetchTaskDue();
    }
    
    
	protected function _initTasks()
    {
    	$this->bootstrap('database');
        $db = $this->getResource('database');
        if (!$db) {
            return;
        }
        $auth =  Zend_Auth::getInstance();
        if ($auth->hasIdentity()) 
        {
	        $this->bootstrap('layout');
	        $layout = $this->getResource('layout');
			$view = $layout->getView();
	        $taskModel = new Model_Task();
			$view->tasknum = $taskModel->getTaskNumber();
        }
    }
    
	protected function _initCompany()
    {
    	$this->bootstrap('database');
        $db = $this->getResource('database');
        if (!$db) {
            return;
        }
        $auth =  Zend_Auth::getInstance();
        if ($auth->hasIdentity()) 
        {
	        $this->bootstrap('layout');
	        $layout = $this->getResource('layout');
			$view = $layout->getView();
	        $companyModel = new Model_Company();
			$view->companies = $companyModel->getCompanies();
        }
    }
    protected function _initProfiles()
    {
    	$this->bootstrap('database');
    	$db = $this->getResource('database');
    	if (!$db) {
    		return;
    	}
    	$auth =  Zend_Auth::getInstance();
    	if ($auth->hasIdentity())
    	{
    		$this->bootstrap('layout');
    		$layout = $this->getResource('layout');
    		$view = $layout->getView();
    		$companyModel = new Model_Profile();
    		$view->profiles = $companyModel->fetchPairs();
    	}
    }
    
	protected function _initPerson()
    {
    	$this->bootstrap('database');
        $db = $this->getResource('database');
        if (!$db) {
            return;
        }
        $auth =  Zend_Auth::getInstance();
        if ($auth->hasIdentity()) 
        {
	        $this->bootstrap('layout');
	        $layout = $this->getResource('layout');
			$view = $layout->getView();
	        $companyModel = new Model_Person();
			$view->contactsnum = $companyModel->getContactsNumber();
			$view->contacts = $companyModel->fetchLatest();
			$view->persons = $companyModel->getPersons();
        }
    }
    
    protected function _initLatestC()
    {
    	$this->bootstrap('database');
    	$db = $this->getResource('database');
    	if (!$db) {
    		return;
    	}
    	$auth =  Zend_Auth::getInstance();
    	if ($auth->hasIdentity())
    	{
    		$this->bootstrap('layout');
    		$layout = $this->getResource('layout');
    		$view = $layout->getView();
    		if(isset($_COOKIE['latest']) && $_COOKIE['latest'])
    		{
	    		$companyModel = new Model_Person();
	    		$view->latestC = $companyModel->fetchCookieLatest($_COOKIE['latest']);
    		}
    	}
    }
    
    
	protected function _initProject()
    {
    	$this->bootstrap('database');
        $db = $this->getResource('database');
        if (!$db) {
            return;
        }
        $auth =  Zend_Auth::getInstance();
        if ($auth->hasIdentity()) 
        {
	        $this->bootstrap('layout');
	        $layout = $this->getResource('layout');
			$view = $layout->getView();
	        $projectModel = new Model_Project();
			$view->projnum = $projectModel->fetchCount();
        }
    }
    
    
    
}

