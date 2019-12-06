<?php

class Form_Register extends Zend_Form {

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


        $firstname = $this->addElement('text', 'firstname', array(
            'filters' => array(),
            'validators' => array(
            ),
            'required' => true,
            'label' => 'Firstname',
            'decorators' => $elementDecorators
        ));

        $lastname = $this->addElement('text', 'lastname', array(
            'filters' => array(),
            'validators' => array(
            ),
            'required' => true,
            'label' => 'Lastname',
            'decorators' => $elementDecorators
        ));


        $username = $this->addElement('text', 'username', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'EmailAddress'
            ),
            'required' => true,
            'label' => 'E-mail address',
            'decorators' => $elementDecorators
        ));

        $password = $this->addElement('password', 'password', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                'Alnum',
                array('StringLength', false, array(6, 50)),
            ),
            'required' => true,
            'label' => 'Password',
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
            'label' => 'Submit',
            'class' => 'button button-gray'
        ));
    }

}
