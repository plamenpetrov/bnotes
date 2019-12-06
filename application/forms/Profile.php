<?php

class Form_Profile extends Zend_Form {

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
            'validators' => array(),
            'required' => true
        ));
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

        $phone = $this->addElement('text', 'phone1', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'Alnum'
            ),
            'required' => false,
            'label' => 'Phone',
            'decorators' => $elementDecorators
        ));

        /* $phone2 = $this->addElement('text', 'phone2', array(
          'filters' => array('StringTrim', 'StringToLower'),
          'validators' => array(
          'Alnum'
          ),
          'required' => false,
          'label' => 'Phone 2',
          'decorators' => $elementDecorators
          )); */

        $address = $this->addElement('textarea', 'address', array(
            'filters' => array(),
            'validators' => array(),
            'required' => false,
            'label' => 'Address',
            'decorators' => $elementDecorators
        ));

        $username = $this->addElement('text', 'email', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
                'EmailAddress'
            ),
            'required' => true,
            'label' => 'E-mail address',
            'decorators' => $elementDecorators
        ));

        /*   $password = $this->addElement('password', 'password', array(
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
          )); */
        $login = $this->addElement('submit', 'login', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Save',
            'class' => 'button button-gray',
            'decorators' => $butonDecorators
        ));
    }

}
