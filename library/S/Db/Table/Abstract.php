<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Abstract
 *
 * @author pavel
 */
abstract class S_Db_Table_Abstract extends Zend_Db_Table_Abstract {
    
	public function toArray ($class = null)
    {
        $vars = get_class_vars($class);
        foreach ($vars as $k => $v) {
            $array[$k] = $this->__get($k);
        }
        return $array;
    }
    
}
?>
