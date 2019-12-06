<?php
class S_Controller_Plugin_Auth extends Zend_Controller_Plugin_Abstract
{
    public function preDispatch (Zend_Controller_Request_Abstract $request)
    {
    	$mname = $request->getModuleName();
    	$cname = $request->getControllerName();
    	$aname = $request->getActionName();
    	if($mname == 'default' &&  $cname == 'error'){ 
    		return;
    	}
    	if ( $mname == 'default' and $cname == 'auth' and ( $aname == 'process' || $aname == 'login' ) ){
    		
    	} else {
    		$config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/application.ini');
        	$config = $config->toArray();
        	
    		$cookie = unserialize(base64_decode($_COOKIE['SOfficeOAuth']));
    		//Zend_Debug::dump($cookie,'ДЪМП ОТ ПРОВЕРКАТА ------------------------>');
    		if ( $cookie['u'] == '' && $cookie['f'] == 'local' ) {
    		    $request->setActionName('login');
    		    $request->setControllerName('auth');
    		    $request->setModuleName('default');
    		} elseif ( $cookie['u'] == '' && $cookie['f'] != 'local' ) {
    		    header("Location: http://".$config['production']['loginurl']);die;
    		} else {
    		    $db 	= Zend_Registry::get('db');
    		    $table 	= new Zend_Db_Table('session');
    		    $select = $table->select()->where('hash = '.$db->quote($cookie['u'], 'string'));
    		    $select = $table->select()
    		    			->from(array('s1'=>'session'),'id_user')->columns('count(*) as number_active_users')
    		    			->joinLeft(array('s2'=>'session'),'s1.id_user = s2.id_user','')
    		    			->where('s1.hash = '.$db->quote($cookie['u'], 'string'))
    		    			->group('s1.id_user');
    		    
    		    $row 	= $db->fetchRow( $select );
    		    
    		    if($row && $row->number_active_users < 2){//Ако има резултат и той е 0 или 1 тогава потребителя трябва да продължи работата си необезпокояван
    		        setcookie('SOfficeOAuth',base64_encode(serialize($cookie)), time()+1800, '/', $config['production']['cookieurl']);
    		    } elseif($row && $row->number_active_users >= 2) {//потребителя е логнат от повече от едно места (браузъри), потребителя трябва да бъде изхвърлен
    		        $table->delete('id_user = '.$db->quote($row->id_user,'int')); //изтриват се всички активни записи за текущия потребител, така че да бъде изхвърлен от всички възможни места
    		        //die('potrebitelq ne e lognat i nqma pravo da vijda crm-a');
    		        if( $cookie['f'] == 'local' ) { //ako cookie-to se e startiralo localno
    		        	$request->setActionName('login');
    		        	$request->setControllerName('auth');
    		        	$request->setModuleName('default');
    		        } else { //ako cookie-to se e startiralo ot nqkade drugade
    		        	setcookie("SOfficeOAuth", "", time()-3600, '/', $config['production']['cookieurl']);
    		        	header("Location: http://".$config['production']['loginurl']);die;
    		        }
    		    } else { //не е върнат резултат за този потребител => той не би трябвало да може да използва системата
    		    	if( $cookie['f'] == 'local' ) { //ako cookie-to se e startiralo localno
    		            $request->setActionName('login');
    		            $request->setControllerName('auth');
    		            $request->setModuleName('default');
    		        } else { //ako cookie-to se e startiralo ot nqkade drugade
    		            setcookie("SOfficeOAuth", "", time()-3600, '/', $config['production']['cookieurl']);
    		            header("Location: http://".$config['production']['loginurl']);die;
    		        }
    		    }
    		}
    	}
    }
}