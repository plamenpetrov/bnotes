<?php

class Form_Group extends Zend_Form {

    public function init() {

        $elementDecorators = array(
            'ViewHelper',
            array('Errors', array('class' => 'errors')),
            array('Description', array('tag' => 'p', 'class' => 'description')),
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
            'validators' => array(),
            'required' => true
        ));

        $groupname = $this->addElement('text', 'groupname', array(
            'filters' => array('StringTrim'),
            'validators' => array(
            ),
            'required' => true,
            'label' => 'Group name',
            'decorators' => $elementDecorators
        ));

        $profiles = new Model_Profile();
        $multiOptions = $profiles->fetchPairs();

        $groupname = $this->addElement('multiselect', 'people', array(
            'filters' => array('StringTrim'),
            'required' => true,
            'label' => 'Profiles',
            'multioptions' => $multiOptions,
            'decorators' => $elementDecorators,
            'class' => 'multiselect'
        ));

        $login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Save',
            'class' => 'button button-gray',
            'decorators' => $butonDecorators
        ));
    }

}
