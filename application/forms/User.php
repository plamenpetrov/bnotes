<?php

class Form_User extends Zend_Form {

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

        $oldpassword = $this->addElement('password', 'oldpassword', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 50)),
            ),
            'required' => true,
            'label' => 'Old password',
            'decorators' => $elementDecorators
        ));
        $password = $this->addElement('password', 'password', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 50)),
            ),
            'required' => true,
            'label' => 'New password',
            'decorators' => $elementDecorators
        ));
        $password2 = $this->addElement('password', 'password2', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 50)),
                array('identical', false, array('token' => 'password'))
            ),
            'required' => false,
            'label' => 'Confirm password',
            'decorators' => $elementDecorators
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
