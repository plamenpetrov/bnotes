<?php
class AuthController extends Zend_Controller_Action {
	
 	public function init() 
 	{
        /* Initialize action controller here */
    }
     public function processAction() 
     {	
     	if(isset($_COOKIE['crmAuth']))
     	{	
	     	$userModel = new Model_User();
	     	$auth = $userModel->getUserByHash($_COOKIE['crmAuth']);
	     	if($auth)
	     	{
     				$authAdapter = $this->getAuthAdapter();
	                $authAdapter->setIdentity($auth['username'])
	                        ->setCredential($auth['password']);
	                $auth = Zend_Auth::getInstance();
	                $result = $auth->authenticate($authAdapter);
	
	                if ($result->isValid()) {
	                    $identity = $authAdapter->getResultRowObject();
	                    $authStorage = $auth->getStorage();
	                    $authStorage->write($identity);
	                    $this->_redirect('/');
	                } else {
	                  	$this->_redirect('/auth/logout/');
	                }
	     	}
     	}
   	}
  	private function getAuthAdapter() 
  	{
        $authAdapter = new Zend_Auth_Adapter_DbTable(Zend_Db_Table::getDefaultAdapter());
        $authAdapter->setTableName('user')
                ->setIdentityColumn('username')
                ->setCredentialColumn('password');
        return $authAdapter;
    }
    
  	public function logoutAction() 
  	{
        Zend_Auth::getInstance()->clearIdentity();
      	$domain = Zend_Registry::get('application')->production->domain;
        $this->_redirect('http://login'.$domain);
    }
   }