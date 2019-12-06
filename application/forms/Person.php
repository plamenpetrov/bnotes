<?php

class Form_Person extends Zend_Form {

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
        $title = $this->addElement('text', 'title', array(
            'filters' => array(),
            'validators' => array(
            ),
            'label' => 'Title',
            'decorators' => $elementDecorators
        ));


        $companyModel = new Model_Company();
        $multiOptions = $companyModel->fetchPairs();

        $company = $this->addElement('select', 'company', array(
            'required' => true,
            'label' => 'Company',
            'multioptions' => $multiOptions,
            'decorators' => $elementDecorators,
            'class' => ''
        ));

        $translate = Zend_Registry::get('Zend_Translate');

        $publOptions = array(
            '1' => $translate->translate('person_visible_to_everyone'),
            '2' => $translate->translate('person_visible_to_owner'),
            '3' => $translate->translate('person_visible_to_namedgroup'),
            '4' => $translate->translate('person_visible_to_adhocgroup')
        );

        $publ = $this->addElement('select', 'publ', array(
            'required' => true,
            'label' => 'Publ',
            'multioptions' => $publOptions,
            'decorators' => $elementDecorators,
            'class' => ''
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


        $city = $this->addElement('text', 'city', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
            ),
            'required' => false,
            'class' => 'city',
            'label' => 'City',
            'decorators' => $elementDecorators
        ));

        $state = $this->addElement('text', 'state', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
            ),
            'required' => false,
            'label' => 'State',
            'decorators' => $elementDecorators
        ));

        $zip = $this->addElement('text', 'zip', array(
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array(
            ),
            'required' => false,
            'label' => 'Zip',
            'decorators' => $elementDecorators
        ));

        $countryOptions = $companyModel->query('select id, name from nmcl_country');

        $country = $this->addElement('select', 'country', array(
            'required' => false,
            'multioptions' => $this->arrayOption($countryOptions),
            'decorators' => $elementDecorators,
            'label' => 'Country',
            'class' => ''
        ));

        $address_typeOptions = $companyModel->query('select id, name from nmcl_addresstype');

        $address_type = $this->addElement('select', 'address_type', array(
            'required' => false,
            'multioptions' => $this->arrayOption($address_typeOptions),
            'decorators' => $elementDecorators,
            'label' => 'Type',
            'class' => ''
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
        $login = $this->addElement('submit', 'save', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Save',
            'class' => 'button button-gray',
            'decorators' => $butonDecorators
        ));
    }

    protected function arrayOption($array) {
        $row = array();

        foreach ($array as $temprow) {
            $row[$temprow['id']] = $temprow['name'];
        }

        return $row;
    }

}
