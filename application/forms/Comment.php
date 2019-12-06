<?php

class Form_Comment extends Zend_Form {

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
            'decorators' => $elementDecorators,
            'validators' => array()
        ));


        $id_company = $this->addElement('hidden', 'id_company', array(
            'filters' => array(),
            'decorators' => $elementDecorators,
            'validators' => array()
        ));

        $id_person = $this->addElement('hidden', 'id_person', array(
            'filters' => array(),
            'decorators' => $elementDecorators,
            'validators' => array()
        ));

        $id_case = $this->addElement('hidden', 'id_case', array(
            'filters' => array(),
            'decorators' => $elementDecorators,
            'validators' => array()
        ));

        $access = $this->addElement('hidden', 'access', array(
            'filters' => array(),
            'decorators' => $elementDecorators,
            'validators' => array()
        ));

        $group = $this->addElement('hidden', 'group', array(
            'filters' => array(),
            'decorators' => $elementDecorators,
            'validators' => array()
        ));

        $comment = $this->addElement('textarea', 'note', array(
            'validators' => array(),
            'required' => true,
            'label' => 'Comment',
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
