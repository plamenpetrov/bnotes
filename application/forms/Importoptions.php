<?php

class Form_Importoptions extends Zend_Form {

    public function init() {

        $elementDecorators = array(
            'ViewHelper',
            array('Errors', array('class' => 'errors')),
            array('Description', array('tag' => 'small', 'class' => 'description')),
            array('HtmlTag', array('class' => 'form-div')),
            array('Label', array('class' => 'form-label', 'requiredSuffix' => ' *'))
                // comment out or remove the Label decorator from the element in question
                // you can do the same for any of the decorators if you don't want them rendered
        );

        $butonDecorators = array(
            'ViewHelper',
            array('Errors', array('class' => '')),
            array('Description', array('tag' => 'p', 'class' => 'description')),
            array('HtmlTag', array('class' => 'form-div'))
                // comment out or remove the Label decorator from the element in question
                // you can do the same for any of the decorators if you don't want them rendered
        );
        $type = $this->addElement('select', 'type', array(
            'validators' => array(
            ),
            'required' => true,
            'label' => 'Type',
            'decorators' => $elementDecorators,
            'multioptions' => array('person' => 'Person', 'company' => 'Company')
        ));

        $name = $this->addElement('text', 'name', array(
            'validators' => array(
            ),
            'required' => true,
            'label' => 'Name',
            'decorators' => $elementDecorators
        ));

        $idnum = $this->addElement('text', 'idnum', array(
            'validators' => array(
            ),
            'required' => true,
            'label' => 'IDN',
            'decorators' => $elementDecorators
        ));

        $phone = $this->addElement('text', 'phone', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alnum'
            ),
            'required' => false,
            'label' => 'Phone',
            'decorators' => $elementDecorators
        ));

        $email = $this->addElement('text', 'email', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Email'
            ),
            'required' => false,
            'label' => 'E-mail',
            'decorators' => $elementDecorators
        ));

        $website = $this->addElement('text', 'website', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
            ),
            'required' => false,
            'label' => 'Website',
            'decorators' => $elementDecorators
        ));


        $address = $this->addElement('textarea', 'address', array(
            'filters' => array(),
            'validators' => array(),
            'required' => false,
            'label' => 'Address',
            'decorators' => $elementDecorators
        ));

        $descritpion = $this->addElement('textarea', 'info', array(
            'filters' => array(),
            'validators' => array(),
            'required' => false,
            'label' => 'Description',
            'decorators' => $elementDecorators
        ));



        $save = $this->addElement('submit', 'save', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Save',
            'class' => 'button button-gray',
            'decorators' => $butonDecorators
        ));
    }

}
