<?php

class Form_Company extends Zend_Form {

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

        $file = $this->addElement('file', 'avatar', array(
            'validators' => array(
                array('IsImage', false),
                array('Size', false, '2097152'),
                array('Upload', false)
            ),
            'required' => false,
            'label' => 'Avatar'
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

        $companyModel = new Model_Company();

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

        $save = $this->addElement('submit', 'save', array(
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
