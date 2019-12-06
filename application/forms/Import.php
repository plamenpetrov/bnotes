<?php

class Form_Import extends Zend_Form {

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

        $file = $this->addElement('file', 'excel', array(
            'filters' => array(),
            'required' => true,
            'label' => 'XLS',
            'disableLoadDefaultDecorators' => true,
            'decorators' => array(
                'File',
                'Errors'
            ),
            'validators' => array(
                array('count', true, array(
                        'min' => 1,
                        'max' => 1,
                        'messages' => array(
                            Zend_Validate_File_Count::TOO_FEW =>
                            'You must upload an xls file',
                            Zend_Validate_File_Count::TOO_MANY =>
                            'You can upload only one xls file'))),
                array('extension', true, array(
                        'extention' => 'xls',
                        'messages' => array(
                            Zend_Validate_File_Extension::NOT_FOUND =>
                            'The file has an invalid extention (xls only)',
                            Zend_Validate_File_Extension::FALSE_EXTENSION =>
                            'The file has an invalid extention (xls only)'))),
                'Size' => array('min' => 0, 'max' => 5242880),
                'Count' => array('min' => 1, 'max' => 1)
            )
        ));

        $save = $this->addElement('submit', 'save', array(
            'required' => false,
            'ignore' => true,
            'label' => 'Preview',
            'class' => 'button button-gray',
            'decorators' => $butonDecorators
        ));
    }

}
