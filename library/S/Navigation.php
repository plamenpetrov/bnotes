<?php
/**
 * @author pavel
 * Градене на навигациите
 */
class S_Navigation
{
	public $_result = array();
	public $_translator;
	private $_htmlParts = array('style','id','class','onclick','onblur','onchange');

	/**
	 * Конструктор за класа за създаване на HTML-а за всички навигации
	 * @param array $array - съдържа трите основни навигациони панела с техните елементи
	 * @return array - масив със HTML на трите основни навигационни панела
	 */
	public function __construct($array, Zend_Translate $translator)
	{
	    $this->_translator = $translator;
	    if($array != null){
			foreach ($array as $i =>$navConfig){
				$this->_result[$i] = $this->createNavigation($navConfig);
			}
	    }
	}

	/**
	 * Създава елемент <li> в който има линк (и) икона
	 * @param array $element - елемент от менюто
	 * @return string
	 */
	private function createElement($element)
	{
		if(isset($element['pages'])) unset($element['pages']);
		$result  = $this->createLink($element);
		return $result;
	}

	/**
	 * Подава се елемент от менюто и се генерира линка който седи в
	 * неномерирания списък.
	 * @param array $element
	 * @return string
	 */
	private function createLink($element){
		$result = '';
		foreach ($element as $key => $component){
			if(in_array($key, $this->_htmlParts)){
				$result .= $this->createHtmlPart($component, $key);
			}
		}
		$label = $this->_translator->translate($element['label']);
		if($element['icon']){
			$text  = '<span class="navigation_icon"><img src="'.$element['icon'].'" /></span>';
			$text .= '<span class="navigation_label">'. $label .'</span>';
		} else {
			$text = '<span class="navigation_label">'. $label .'</span>';
		}
		
		return '<a href="'.$element['uri'].'" '.$result .'>'.$text.'</a>';
	}

	/**
	 * Подават се два параметъра, които се преобразуват до атрибути
	 * на HTML елемент
	 * @param string $value - Стойност на елемента
	 * @param string $html - тип на атрибута към елемента
	 * @return string
	 */
	private function createHtmlPart($value, $html){
		if(!$value) return '';
		return $html . '="'.$value.'" ';
	}
	
	/**
	 * Създава възел от навигацията и ако има деца дадения възел
	 * създава рекурсия
	 * @param array $element
	 * @param bool $haveChild
	 * @return string
	 */
	private function createNode($element, $haveChild){
	    $res = '';
	    if($haveChild){
	        if($element['li_style']){
	        	$res .= '<li class="node" style="' . $element['li_style'] .'">';
	        } else {
	        	$res .= '<li class="node">'. $this->createElement($element);
	        }
	        $res .= $this->createNavigation($element['pages']) .'</li>';
	    } else {
	        if($element['li_style']){
	        	$res .= '<li style="' . $element['li_style'] .'">';
	        	$res .= $this->createElement($element) .'</li>';
	        } else {
	        	$res .= '<li>'. $this->createElement($element) .'</li>';
	        }
	    }
	    return $res;
	}

	/**
	 * Подават се елементите на една цяла навигация.
	 * Функцията се вика рекурсивно сама себе си, за да
	 * създаде всички подменю елементи
	 * @param array $navigation
	 * @return string - целия HTML за дадена навигация
	 */
	private function createNavigation($navigation){
		$res = '<ul>';
		foreach ($navigation as $i => $element){
			if($this->isValid($element)){
				$res .= $this->createNode($element, $this->hasChildren($element));
			}
		}
		$res .= '</ul>';
		return $res;
	}

	/**
	 * Подава възел, който бива проверен и ако има елемент ['pages']
	 * Връща истина т.е. има под елементи на този, ако ли не връща лъжа
	 * @param array $node - Възел
	 * @return bool
	 */
	private function hasChildren($node){
		if(isset($node['pages'])) {
			return true;
		}
		return false;
	}

	/**
	 * Подава му се елемент и проверява дали е валиден
	 * Ако е валиден връща TRUE, иначе връща FALSE
	 * @param array - $data - възел/елемент от менюто
	 * @return bool
	 */
	private function isValid($data)
	{
		if(is_array($data)){
			return true;
		}
		return false;
	}

	/**
	 * Метод който връща съдържанието на едно от трите менюта
	 * @param string $param - име на менюто което се желае - |left|top|bottom|
	 * @return string - html, който трябва да се отпечата на екрана
	 */
	public function getNavigation($param){
	    if(isset($this->_result[$param]))
		return $this->_result[$param];
	}
}