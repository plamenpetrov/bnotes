<?php

class Model_Import extends Model_Abstract {

    //protected $_dbTableClass = 'Model_DbTable_Person';
    protected $id;

    /**
     * @return the $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * @param field_type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Import data in system from excel
     * @param type $data
     * @param type $companyModel
     * @param type $personModel
     */
    public function importdata($data, $companyModel, $personModel) {
        if (isset($data['field_type'])) {
            $db = Zend_Registry::get('db');
            $dbconf = $db->getConfig();

            $modelImport = new Model_Import();

            $idUser = $modelImport->getCurrentUser()->id;
            $fname = $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $dbconf['dbname'] . DIRECTORY_SEPARATOR . $idUser . DIRECTORY_SEPARATOR . 'import.xls';

            $inputFileType = 'Excel5';

            /**  Create a new Reader of the type defined in $inputFileType  * */
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            /**  Load $inputFileName to a PHPExcel Object  * */
            $objReader->setReadDataOnly(true);
            $array_data = array();

            $objPHPExcel = PHPExcel_IOFactory::load($fname);
            foreach ($objPHPExcel->getWorksheetIterator() as $worksheet) {
                $worksheetTitle = $worksheet->getTitle();
                $highestRow = $worksheet->getHighestRow(); // e.g. 10
                $highestColumn = $worksheet->getHighestColumn(); // e.g 'F'
                $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
                $nrColumns = ord($highestColumn) - 64;

                for ($row = 1; $row <= $highestRow; ++$row) {

                    for ($col = 0; $col < $highestColumnIndex; ++$col) {
                        $cell = $worksheet->getCellByColumnAndRow($col, $row);
                        $val = $cell->getValue();
                        $dataType = PHPExcel_Cell_DataType::dataTypeForValue($val);

                        if ($data['field_type'][$col])
                            $array_data[$row][$data['field_type'][$col]] = $val;
                    }

                    if (isset($array_data[$row]['type']) && $array_data[$row]['type'] == 'Person') {
                        if (isset($array_data[$row]['firstname']) && $array_data[$row]['firstname']) {
                            if (isset($array_data[$row]['lastname']) && $array_data[$row]['lastname']) {
                                if (isset($array_data[$row]['company']) && $array_data[$row]['company']) {
                                    try {
                                        $dataperson = $array_data[$row];
                                        $idCompany = $companyModel->add($array_data[$row]);
                                        $dataperson['id_company'] = $idCompany;
                                        $personModel->add($dataperson);
                                    } catch (Exception $e) {
                                        $error[$row]['error'] = $e->getMessage();
                                    }
                                } else {
                                    $error[$row]['error'] = 'Company name is required in order to Import a Person';
                                }
                            } else {
                                $error[$row]['error'] = 'Lastname is required in order to Import a Person';
                            }
                        } else {
                            $error[$row]['error'] = 'Firstname is required in order to Import a Person';
                        }
                    } elseif (isset($array_data['type']) && $array_data['type'] == 'Company') {
                        if (isset($array_data[$row]['company']) && $array_data[$row]['company']) {
                            try {
                                $companyModel->add($array_data[$row]);
                            } catch (Exception $e) {
                                $error[$row]['error'] = $e->getMessage();
                            }
                        } else {
                            $error[$row]['error'] = 'Company name is required in order to Import a Company';
                        }
                    }
                }
            }
        }
    }

}
