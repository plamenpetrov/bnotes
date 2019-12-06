<?php

/**
 * @author pavel
 * Helper, който отпечатва на екрана дадена навигация
 */
class S_View_Helper_SNavigation extends Zend_View_Helper_Abstract
{

    /**
     * Печата на екрана определена навигация.
     * @param  S_Navigation $navigation - Обект от клас S_Navigation
     * @param  string $param навигацията която е нужна да се отпечата
     */
    public function SNavigation (S_Navigation $navigation, $param)
    {
        echo '<div id="navigation_'.$param.'">'.$navigation->getNavigation($param) .'</div>';
    }
    
}