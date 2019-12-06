<?php
class S_Forms_Create extends Zend_Form
{
	private $_form = null;
	
	public function init($options){
		$this->_form = new Zend_Form();

		$this->_form->setAction($options['action']);
		$this->_form->setMethod($options['method']);
		$this->_form->setName($options['title']);
		$this->_form->setAttrib('alt', $options['description']);
		$this->_form->setAttrib('style', $options['style']);
		$this->_form->setAttrib('class', $options['class']);
		
		foreach ($options['elements'] as $element)
		{
			switch ($element['type']){
				case "text":
					self::addTextElement($element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['onclick'], $element['onchange'], $element['onfocus'], $element['onblur'], $element['onselect'], $element['is_required']);
					break;
				case "password":
					self::addPasswordElement($element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['onclick'], $element['onchange'], $element['onfocus'], $element['onblur'], $element['onselect'], $element['is_required']);
					break;
				case "textarea":
					self::addTextareaElement($element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['onclick'], $element['onchange'], $element['onfocus'], $element['onblur'], $element['onselect'], $element['is_required']);
					break;
				case "reset":
					self::addResetBtnElement($element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['onclick'], $element['onchange'], $element['onfocus'], $element['onblur'], $element['onselect'], $element['is_required']);
					break;
				case "submit":
					self::addSubmitBtnElement($element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['onclick'], $element['onchange'], $element['onfocus'], $element['onblur'], $element['onselect'], $element['is_required']);
					break;
				case "hidden":
					self::addHiddenElement($element['name'], $element['label'], $element['value']);
					break;
				case "select":
					self::addSelectBoxElement($element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['onclick'], $element['onchange'], $element['onfocus'], $element['onblur'], $element['onselect'], $element['is_required']);
					break;
				case "multiselect":
					self::addMultiSelectBoxElement($element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['onclick'], $element['onchange'], $element['onfocus'], $element['onblur'], $element['onselect'], $element['is_required']);
					break;
				case "radio":
					self::addRadioBoxElement($element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['onclick'], $element['onchange'], $element['onfocus'], $element['onblur'], $element['onselect'], $element['is_required']);
					break;
				case "check":
					self::addCheckBoxElement($element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['onclick'], $element['onchange'], $element['onfocus'], $element['onblur'], $element['onselect'], $element['is_required']);
					break;
				case "datetime":
					self::addDatetimeElement('datetime', $element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['is_required']);
					break;
				case "date":
					self::addDatetimeElement('date', $element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['is_required']);
					break;
				case "time":
					self::addDatetimeElement('time', $element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['is_required']);
					break;
				case "file":
					self::addFileElement('file', $element['name'], $element['label'], $element['value'], $element['style'], $element['alt'], $element['is_required']);
					break;
			}
			
		}
		return $this->_form;
	}
	
	/**
	 * Създава елемент тип - текстова кутия с вече въведена дата и възможност да се покаже datetime picker (textbox)
	 * @param string $type - datetime|date|time
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $is_required
	 */
	private function addDatetimeElement($type, $name, $label, $value, $style, $alt, $is_required)
	{
		$this->_form->addElement('text',$name.'_picker');
		$this->_form->getElement($name)
			->setLabel($label)
			->setValue($value)
			->setAttrib('class', $type)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setRequired($is_required);
			
	}

	/**
	 * Създава елемент тип - текстова кутия с вече въведена дата и възможност да се покаже datetime picker (textbox)
	 * @param string $type - datetime|date|time
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $is_required
	 */
	private function addFileElement($type, $name, $label, $value, $style, $alt, $is_required)
	{
		$this->_form->setAttrib('enctype', 'multipart/form-data');
		$this->_form->addElement('file',$name);
		$this->_form->getElement($name)
			->setLabel($label)
			->setDestination($value)
			->setAttrib('class', $type)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setRequired($is_required);
			
	}
	
	/**
	 * Създава елемент тип - текстова кутия (textbox)
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $onclick
	 * @param string $onchange
	 * @param string $onfocus
	 * @param string $onblur
	 * @param string $onselect
	 * @param string $is_required
	 */
	private function addTextElement($name, $label, $value, $style, $alt, $onclick, $onchange, $onfocus, $onblur, $onselect, $is_required)
	{
		$this->_form->addElement('text',$name);
		$this->_form->getElement($name)
			->setLabel($label)
			->setValue($value)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setAttrib('onclick', $onclick)
			->setAttrib('onchange', $onchange)
			->setAttrib('onfocus', $onfocus)
			->setAttrib('onblur', $onblur)
			->setAttrib('onselect', $onselect)
			->setRequired($is_required);
			
	}
	
	/**
	 * Създава елемент тип - избиращ се 1 елемент от списък (selectbox)
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $onclick
	 * @param string $onchange
	 * @param string $onfocus
	 * @param string $onblur
	 * @param string $onselect
	 * @param string $is_required
	 */
	private function addSelectBoxElement($name, $label, $value, $style, $alt, $onclick, $onchange, $onfocus, $onblur, $onselect, $is_required)
	{
		
		$this->_form->addElement('select',$name);
		$element = $this->_form->getElement($name);
		$element->setLabel($label)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setAttrib('onclick', $onclick)
			->setAttrib('onchange', $onchange)
			->setAttrib('onfocus', $onfocus)
			->setAttrib('onblur', $onblur)
			->setAttrib('onselect', $onselect)
			->setRequired($is_required);
		if('sql' == substr($value, 0 , 3))
		{
			$db = Zend_Registry::get('db');
			$sql = substr($value, 4, strlen($value));
			$smtp = $db->query($sql);
			$options = $smtp->fetchAll(PDO::FETCH_KEY_PAIR);
		}
		$element->addMultiOptions($options);
	}
	
	/**
	 * Създава елемент тип - multi selectbox
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $onclick
	 * @param string $onchange
	 * @param string $onfocus
	 * @param string $onblur
	 * @param string $onselect
	 * @param string $is_required
	 */
	private function addMultiSelectBoxElement($name, $label, $value, $style, $alt, $onclick, $onchange, $onfocus, $onblur, $onselect, $is_required)
	{
		$this->_form->addElement('multiselect',$name);
		$element = $this->_form->getElement($name);
		$element->setLabel($label)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setAttrib('onclick', $onclick)
			->setAttrib('onchange', $onchange)
			->setAttrib('onfocus', $onfocus)
			->setAttrib('onblur', $onblur)
			->setAttrib('onselect', $onselect)
			->setRequired($is_required);
		if('sql' == substr($value, 0 , 3))
		{
			$db = Zend_Registry::get('db');
			$sql = substr($value, 4, strlen($value));
			$smtp = $db->query($sql);
			$options = $smtp->fetchAll(PDO::FETCH_KEY_PAIR);
		}
		$element->addMultiOptions($options);
	}
	
	/**
	 * Създава елемент тип - radio
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $onclick
	 * @param string $onchange
	 * @param string $onfocus
	 * @param string $onblur
	 * @param string $onselect
	 * @param string $is_required
	 */
	private function addRadioBoxElement($name, $label, $value, $style, $alt, $onclick, $onchange, $onfocus, $onblur, $onselect, $is_required)
	{
		
		$this->_form->addElement('radio',$name);
		$element = $this->_form->getElement($name);
		$element->setLabel($label)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setAttrib('onclick', $onclick)
			->setAttrib('onchange', $onchange)
			->setAttrib('onfocus', $onfocus)
			->setAttrib('onblur', $onblur)
			->setAttrib('onselect', $onselect)
			->setRequired($is_required);
		if('sql' == substr($value, 0 , 3))
		{
			$db = Zend_Registry::get('db');
			$sql = substr($value, 4, strlen($value));
			$smtp = $db->query($sql);
			$options = $smtp->fetchAll(PDO::FETCH_KEY_PAIR);
		}
		$element->addMultiOptions($options);
	}
	
	/**
	 * Създава елемент тип - checkbox
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $onclick
	 * @param string $onchange
	 * @param string $onfocus
	 * @param string $onblur
	 * @param string $onselect
	 * @param string $is_required
	 */
	private function addCheckBoxElement($name, $label, $value, $style, $alt, $onclick, $onchange, $onfocus, $onblur, $onselect, $is_required)
	{
		
		$this->_form->addElement('MultiCheckbox',$name);
		$element = $this->_form->getElement($name);
		$element->setLabel($label)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setAttrib('onclick', $onclick)
			->setAttrib('onchange', $onchange)
			->setAttrib('onfocus', $onfocus)
			->setAttrib('onblur', $onblur)
			->setAttrib('onselect', $onselect)
			->setRequired($is_required);
		if('sql' == substr($value, 0 , 3))
		{
			$db = Zend_Registry::get('db');
			$sql = substr($value, 4, strlen($value));
			$smtp = $db->query($sql);
			$options = $smtp->fetchAll(PDO::FETCH_KEY_PAIR);
		}
		$element->addMultiOptions($options);
	}
	
	/**
	 * Създава елемент тип - текстова кутия за парола (password field)
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $onclick
	 * @param string $onchange
	 * @param string $onfocus
	 * @param string $onblur
	 * @param string $onselect
	 * @param string $is_required
	 */
	private function addPasswordElement($name, $label, $value, $style, $alt, $onclick, $onchange, $onfocus, $onblur, $onselect, $is_required)
	{
		$this->_form->addElement('password',$name);
		$this->_form->getElement($name)
			->setLabel($label)
			->setValue($value)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setAttrib('onclick', $onclick)
			->setAttrib('onchange', $onchange)
			->setAttrib('onfocus', $onfocus)
			->setAttrib('onblur', $onblur)
			->setAttrib('onselect', $onselect)
			->setRequired($is_required);
			
	}
	
	/**
	 * Създава елемент тип - текстова кутия за писане на голям текст. (textarea)
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $onclick
	 * @param string $onchange
	 * @param string $onfocus
	 * @param string $onblur
	 * @param string $onselect
	 * @param string $is_required
	 */
	private function addTextareaElement($name, $label, $value, $style, $alt, $onclick, $onchange, $onfocus, $onblur, $onselect, $is_required)
	{
		$this->_form->addElement('textarea',$name);
		$this->_form->getElement($name)
			->setLabel($label)
			->setValue($value)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setAttrib('cols',20)
			->setAttrib('rows',4)
			->setAttrib('onclick', $onclick)
			->setAttrib('onchange', $onchange)
			->setAttrib('onfocus', $onfocus)
			->setAttrib('onblur', $onblur)
			->setAttrib('onselect', $onselect)
			->setRequired($is_required);
	}
	
	/**
	 * Създава елемент тип - бутон за изчистване на вече въведените стойности на формата и връщане на стойностите по подразбиране. (reset button)
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $onclick
	 * @param string $onchange
	 * @param string $onfocus
	 * @param string $onblur
	 * @param string $onselect
	 * @param string $is_required
	 */
	private function addResetBtnElement($name, $label, $value, $style, $alt, $onclick, $onchange, $onfocus, $onblur, $onselect, $is_required)
	{
		$this->_form->addElement('reset',$name);
		$this->_form->getElement($name)
			->setLabel($label)
			->setValue($value)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setAttrib('onclick', $onclick)
			->setAttrib('onchange', $onchange)
			->setAttrib('onfocus', $onfocus)
			->setAttrib('onblur', $onblur)
			->setAttrib('onselect', $onselect)
			->setRequired($is_required);
	}
	
	/**
	 * Създава елемент тип - бутон за запис. (submit button)
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 * @param string $style
	 * @param string $alt
	 * @param string $onclick
	 * @param string $onchange
	 * @param string $onfocus
	 * @param string $onblur
	 * @param string $onselect
	 * @param string $is_required
	 */
	private function addSubmitBtnElement($name, $label, $value, $style, $alt, $onclick, $onchange, $onfocus, $onblur, $onselect, $is_required)
	{
		$this->_form->addElement('submit',$name);
		$this->_form->getElement($name)
			->setLabel($label)
			->setValue($value)
			->setAttrib('style', $style)
			->setAttrib('alt', $alt)
			->setAttrib('onclick', $onclick)
			->setAttrib('onchange', $onchange)
			->setAttrib('onfocus', $onfocus)
			->setAttrib('onblur', $onblur)
			->setAttrib('onselect', $onselect)
			->setRequired($is_required);
	}
	
	/**
	 * Създава елемент тип - бутон за запис. (submit button)
	 * @param string $name
	 * @param string $label
	 * @param string $value
	 */
	private function addHiddenElement($name, $label, $value)
	{
		$this->_form->addElement('hidden',$name);
		$this->_form->getElement($name)
			->setValue($value);
	}
}