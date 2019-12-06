<?php

/**
 * @see Zend_Controller_Action_Helper_Abstract
 */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

class S_Controller_Action_Helper_Output extends Zend_Controller_Action_Helper_Abstract
{
    
    const OUTPUT_JSON = 'json';
    const OUTPUT_XML  = 'xml';
    const OUTPUT_HTML = 'html';
    
    protected $_output;
    protected $_type;
    
    public function direct($output, $type = self::OUTPUT_JSON)
    {
        $this->_output = $output;
        $this->_type   = $type;
    }
    
    public function postDispatch()
    {
        if (null !== $this->_output) {
            $this->getHelper('layout')->disableLayout();
            $this->getHelper('viewRenderer')->setNoRender(true);
            $this->getResponse()->clearBody();
            
            switch ($this->_type) {
                case self::OUTPUT_JSON:
                    $this->getResponse()->clearAllHeaders()
                                        //->setHeader('Content-Type', 'application/json; charset=utf-8')
                                        ->setBody(Zend_Json::encode($this->_output));
                    break;
                case self::OUTPUT_HTML:
                    $this->getResponse()->clearAllHeaders()
                                        ->setBody($this->_output);
                    break;
            }
        }
    }
    
    protected function getHelper($helper)
    {
        return Zend_Controller_Action_HelperBroker::getStaticHelper($helper);
    }
    
}
