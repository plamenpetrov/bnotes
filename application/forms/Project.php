<?php

class Form_Project extends Zend_Form {

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
        $id = $this->addElement('hidden', 'id', array(
            'filters' => array(),
            'validators' => array()
        ));

        $name = $this->addElement('text', 'name', array(
            'validators' => array(
            ),
            'required' => true,
            'label' => 'Name',
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
