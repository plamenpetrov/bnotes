<?php

/**
 * Description of ContactController
 */
class ContactController extends Zend_Controller_Action {

    public $personModel;
    public $customTagModel;
    public $tagModel;
    public $groupModel;
    public $companyModel;
    public $activityModel;

    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/auth/logout/');
        }

        $this->personModel = new Model_Person();
        $this->customTagModel = new Model_Customtag();
        $this->tagModel = new Model_Tag();
        $this->groupModel = new Model_Group();
        $this->companyModel = new Model_Company();
        $this->activityModel = new Model_Activity();
    }

    /**
     * List all available contacts
     */
    public function indexAction() {
        $data = $this->getRequest()->getParams();
        if (isset($data['msg'])) {
            $this->view->msg = $data['msg'];
        }

        if (isset($data['error'])) {
            $this->view->error = $data['error'];
        }

        $this->view->allcontacts = $this->personModel->fetchAllConatacts();
        $this->view->custom = $this->customTagModel->fetchAll();
        $this->view->tags = $this->tagModel->sortAndIndexArray($this->tagModel->fetchAll());
        $this->view->contacttags = $this->tagModel->fetchAllTags();
        $this->view->groups = $this->groupModel->fetchAll();
    }

    /**
     * Add tag to person or company
     */
    public function addtagAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();
        $this->tagModel->add($data);
        $this->view->data = $this->tagModel->fetchTags($data['id_ref'], $data['type']);
    }

    /**
     * Add tag to all selected contacs
     * @return boolean
     */
    public function addtagallAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        if (!isset($data["tag"]) || !isset($data["contact"])) {
            return false;
        }

        $tags = $this->tagModel->addTagsAll($data);
        $this->view->data = $tags;
    }

    /**
     * Add tag to all contacts
     */
    public function addtagallcontactsAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        if (isset($data["tag"])) {
            $this->tagModel->addTagToAll($data);
        }
    }

    /**
     * Edit contact form
     */
    public function edittagAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        $this->view->data = $this->tagModel->edit($data);
    }

    /**
     * Remove tag from contact
     */
    public function removetagAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        $this->tagModel->removetag($data);
        $this->view->data = $this->tagModel->fetchTags($data['id_ref'], $data['type']);
    }

    /**
     * Add custom fields to form
     */
    public function customfieldsAction() {
        $this->view->tags = $this->customTagModel->fetchAll();
    }

    /**
     * Create new custom field
     */
    public function addcustomfieldAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();
        $this->view->data = $this->customTagModel->add($data);
    }

    /**
     * Delete custom field from contact
     */
    public function removecustomfieldAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();
        $this->view->data = $this->customTagModel->remove($data);
    }

    /**
     * Update custom field
     */
    public function updatecustomfieldAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();
        $this->view->data = $this->customTagModel->rename($data);
    }

    /**
     * Delete tag from all contacts
     */
    public function deletetagAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();
        $this->view->data = $this->tagModel->deletetag($data['id']);
    }

    /**
     * Sort contacts according to specific user preferences
     */
    public function sortAction() {
        $data = $this->getRequest()->getParams();

        if (isset($data['msg'])) {
            $this->view->msg = $data['msg'];
        }

        if (isset($data['error'])) {
            $this->view->error = $data['error'];
        }

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->layout()->disableLayout();
            $allcontacts = $this->personModel->fetchContagtsByTag($data['tag'], $data['count'], 6);

            if (count($allcontacts) > 0) {
                foreach ($allcontacts as $key => $contact) {
                    $allcontacts[$key]['tags'] = $this->tagModel->fetchTagByContact($contact['id'], $contact['type']);
                }
            }

            $this->view->allcontacts = $allcontacts;
        } else {
            $this->view->allcontacts = $this->personModel->fetchContagtsByTag($data['tag']);

            $tags = explode(',', $data['tag']);

            if (count($tags) > 1) {
                $arrayTags = array();

                foreach ($tags as $tag) {
                    $this->tagModel->setId($tag);
                    array_push($arrayTags, $this->tagModel->fetch());
                }

                $this->view->multipletags = $arrayTags;
            } else {
                $this->tagModel->setId($data['tag']);
                $this->view->onetag = $this->tagModel->fetch();
            }

            $this->view->tag = $data['tag'];
            $this->view->firstload = 1;
            $this->view->contacttags = $this->tagModel->fetchAllTags();

            $this->view->tags = $this->tagModel->sortAndIndexArray($this->tagModel->fetchAll());
        }
    }

    /**
     * Merge two persons as one if they are possibly duplicated
     */
    public function mergepersonAction() {
        $data = $this->getRequest()->getParams();

        if (isset($data['id'])) {
            $this->personModel->setId($data["id"]);
            $this->view->person = $this->personModel->fetch();
            $this->view->people = $this->personModel->fetchAll(0);
            $this->view->id = $data["id"];
            if (isset($data['mergeid']) && $data['mergeid']) {
                $this->personModel->merge($data["id"], $data['mergeid']);

                $this->view->msg = 'Persons were successfully merged';
            }
        }
    }

    /**
     * Merge two companies as one if they are possibly duplicated
     */
    public function mergecompanyAction() {
        $data = $this->getRequest()->getParams();

        if (isset($data['id'])) {
            $this->companyModel->setId($data["id"]);
            $this->view->company = $this->companyModel->fetch();
            $this->view->companies = $this->companyModel->fetchAll(0);
            $this->view->id = $data["id"];
            if (isset($data['mergeid']) && $data['mergeid']) {
                $this->companyModel->merge($data["id"], $data['mergeid']);

                $this->view->msg = 'Companies were successfully merged';
            }
        }
    }

    /**
     * Edit person form. If request is post the store data
     */
    public function editpersonAction() {
        $data = $this->getRequest()->getParams();
        $form = new Form_Person();

        if (isset($data["id"])) {
            $this->view->id = $data["id"];
            $this->personModel->setId($data["id"]);
            if ($request->isPost()) {
                //Zend_Debug::dump($data);die;
                if ($this->personModel->edit($data)) {
                    $this->_forward('index', 'contact', 'default', array('msg' => 'Contact saved'));
                } else {
                    $this->_forward('index', 'contact', 'default', array('error' => 'Contact not saved'));
                }
            }
            $values = $this->personModel->fetch();

            $fields = $this->customTagModel->fetchAll();
            $this->view->fields = $fields;
            if ($fields) {
                foreach ($fields as $field) {
                    $form->addElement('text', $field['name'], array('label' => $field['label']));
                }
            }

            $this->view->values = $values;

            $form->populate($values);
            $form->company->setValue($values['id_company']);
            $this->view->phones = $this->personModel->fetchPhones();
            $this->view->emails = $this->personModel->fetchEmails();
            $this->view->addresses = $this->personModel->fetchAdresses();
            $this->view->websites = $this->personModel->fetchWebsites();
            $this->view->publ = 1;
            $this->view->form = $form;
            $this->view->groups = $this->groupModel->fetchAll();
        }
    }

    /**
     * Edit company form. If request is post the store data
     */
    public function editcompanyAction() {
        $form = new Form_Company();
        $data = $this->getRequest()->getParams();
        if (isset($data["id"])) {
            $this->view->id = $data["id"];
            $this->companyModel->setId($data["id"]);
            if ($this->getRequest()->isPost()) {
                $this->companyModel->saveFiles($data);

                if ($this->companyModel->edit($data)) {
                    $this->_forward('index', 'contact', 'default', array('msg' => 'Contact saved'));
                } else {
                    $this->_forward('index', 'contact', 'default', array('error' => 'Contact not saved'));
                }
            }
            $values = $this->companyModel->fetch();

            $fields = $this->customTagModel->fetchAll();
            $this->view->fields = $fields;
            if ($fields) {
                foreach ($fields as $field) {
                    $form->addElement('text', $field['name'], array('label' => $field['label']));
                }
            }

            $this->view->groups = $this->groupModel->fetchAll();

            $this->view->values = $values;

            $form->populate($values);
            $this->view->phones = $this->companyModel->fetchPhones();
            $this->view->emails = $this->companyModel->fetchEmails();
            $this->view->addresses = $this->companyModel->fetchAdresses();
            $this->view->websites = $this->companyModel->fetchWebsites();
            $this->view->form = $form;
        }
    }

    /**
     * Delete person data
     */
    public function deletepersonAction() {
        $id = $this->getRequest()->getParam('id');
        if ($id) {

            $this->personModel->setId($id);
            try {
                $this->personModel->deleteNotes();
                $this->personModel->delete();
                $this->_forward("index", "contact", "default", array('msg' => 'Contact deleted'));
            } catch (Exception $e) {
                $this->_forward("index", "contact", "default", array('error' => 'Contact can not be deleted. There are associated.'));
            }
        }
    }

    /**
     * Delete company data
     * @return boolean
     */
    public function deletecompanyAction() {
        $id = $this->getRequest()->getParam('id');
        if (!$id) {
            return false;
        }

        $this->companyModel->setId($id);

        try {
            $this->companyModel->deleteNotes();
            $this->companyModel->delete();
            $this->_forward("index", "contact", "default", array('msg' => 'Contact deleted'));
        } catch (Exception $e) {
            $this->_forward("index", "contact", "default", array('error' => 'Contact can not be deleted. There are people associated.'));
        }
    }

    /**
     * Create new company
     */
    public function addcompanyAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        if (isset($data['company']) && $data['company']) {
            try {
                $data['id'] = $this->companyModel->add($data);
                $this->view->data = $data;
            } catch (Exception $e) {
                $data['error'] = $e->getMessage();
                $this->view->data = 0;
            }
        } else {
            $this->view->data = 0;
        }
    }

    /**
     * Add person
     */
    public function addpersonAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        if (isset($data['firstname']) && $data['firstname'] && isset($data['lastname']) && $data['lastname'] && isset($data['id_company']) && $data['id_company']) {
            try {
                $data['id'] = $this->personModel->create($data);
                $this->view->data = $data;
            } catch (Exception $e) {
                $data['error'] = $e->getMessage();
                $this->view->data = 0;
            }
        } else {
            $this->view->data = 0;
        }
    }

    /**
     * Preview person data and all related data
     */
    public function personrecordAction() {
        $data = $this->getRequest()->getParams();
        if (isset($data['note'])) {
            $id_note = $this->activityModel->add($data);
            if ($id_note) {
                $this->personModel->saveFiles($data);
            }
        }

        $this->personModel->setId($data['id']);

        $caseModel = new Model_Project();
        $progileModel = new Model_Profile();

        $this->view->tags = $this->tagModel->fetchTags($data['id'], 1);

        $this->view->profiless = $progileModel->fetchAll();
        $this->view->groups = $this->groupModel->fetchAll();
        $this->view->projects = $caseModel->fetchAll();

        $this->view->person = $this->personModel->fetchRecord();
        $this->view->additional = $this->customTagModel->fetchAll();
        $this->view->phones = $this->personModel->fetchPhones();
        $this->view->emails = $this->personModel->fetchEmails();
        $this->view->addresses = $this->personModel->fetchAdresses();
        $this->view->websites = $this->personModel->fetchWebsites();
        $this->view->notes = $this->personModel->getNotes();
    }

    /**
     * Preview company data and all related data
     */
    public function companyrecordAction() {
        $id = $request->getParam('id');
        $data = $this->getRequest()->getParams();
        if (isset($data['note'])) {
            //Zend_Debug::dump($data);die;
            $id_note = $this->activityModel->add($data);
            if ($id_note) {
                $this->companyModel->saveRecordFiles($id_note, $data);
            }
        }

        $this->view->tags = $this->tagModel->fetchTags($id, 2);
        $this->personModel->setId($id);

        $caseModel = new Model_Project();
        $progileModel = new Model_Profile();

        $this->view->countPpl = $this->personModel->getNumberOfPeople();
        $this->view->profiless = $progileModel->fetchAll();
        $this->view->groups = $this->groupModel->fetchAll();
        $this->view->projects = $caseModel->fetchAll();

        $this->view->company = $this->personModel->fetchRecord();
        $this->view->additional = $this->customTagModel->fetchAll();
        $this->view->phones = $this->personModel->fetchPhones();
        $this->view->emails = $this->personModel->fetchEmails();
        $this->view->addresses = $this->personModel->fetchAdresses();
        $this->view->websites = $this->personModel->fetchWebsites();
        $this->view->notes = $this->personModel->getNotes();
    }

    /**
     * List of all available contacts filtered and paginated
     */
    public function getcontactsAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        if (isset($data['filters'])) {
            $this->view->data = $this->personModel->fetchAllConatacts($data['count'], 50, $data['filters']);
        } else {
            $this->view->data = $this->personModel->fetchAllConatacts($data['count']);
        }
    }

    /**
     * Import data in CRM from excel
     */
    public function importAction() {
        $form = new Form_Import();
        $request = $this->getRequest();
        $data = $request->getParams();
        if ($form->isValid($data)) {
            if ($_FILES) {
                $db = Zend_Registry::get('db');
                $adapter = new Zend_File_Transfer_Adapter_Http();
                // a call to $form->getValues() has been previously made
                foreach ($adapter->getFileInfo() as $file => $info) {
                    if ($adapter->isUploaded($file)) {
                        $dbconf = $db->getConfig();
                        $modelImport = new Model_Import();
                        $idUser = $modelImport->getCurrentUser()->id;
                        $FileRenamed = 'import.xls';
                        $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $dbconf['dbname'] . DIRECTORY_SEPARATOR . $idUser . DIRECTORY_SEPARATOR;
                        $name = $adapter->getFileName($file);
                        $fname = $path . $FileRenamed;
                        $adapter->addFilter(new Zend_Filter_File_Rename(array('target' => $fname, 'overwrite' => true)), null, $file);
                        if (file_exists($path)) {
                            ini_set('max_execution_time', 0);
                            $adapter->setDestination($path);
                        } else {
                            if (mkdir($path, 0777, true)) {
                                ini_set('max_execution_time', 0);
                                $adapter->setDestination($path);
                            }
                        }
                        $adapter->receive($file);
                        $this->_redirect('/contact/importoptions');
                    }
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Import specific nomenclatures and data from excel
     */
    public function importoptionsAction() {
        $db = Zend_Registry::get('db');
        $dbconf = $db->getConfig();
        $modelImport = new Model_Import();
        $idUser = $modelImport->getCurrentUser()->id;
        $fname = $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'import' . DIRECTORY_SEPARATOR . $dbconf['dbname'] . DIRECTORY_SEPARATOR . $idUser . DIRECTORY_SEPARATOR . 'import.xls';
        $data = $this->getRequest()->getParams();

        if (!isset($data['currentrow'])) {
            $currentRow = 1;
        } else {
            $currentRow = $data['currentrow'];
        }

        $inputFileType = 'Excel5';

        /**  Create a new Reader of the type defined in $inputFileType  * */
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        /**  Load $inputFileName to a PHPExcel Object  * */
        $objReader->setReadDataOnly(true);
        $filter = new PHPExcel_Filterarow();
        $filter->setRows($currentRow);
        $objReader->setReadFilter($filter);

        $objPHPExcel = $objReader->load($fname);
        $objWorksheet = $objPHPExcel->getActiveSheet();

        $lastColumm = $objPHPExcel->setActiveSheetIndex(0)->getHighestColumn();
        $this->view->currentrow = $currentRow;

        $rowIterator = $objPHPExcel->getActiveSheet()->getRowIterator();
        $array_data = array();
        foreach ($rowIterator as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(true); // Loop all cells, even if it is not set
            // if(1 == $row->getRowIndex ()) continue;//skip first row
            $rowIndex = $row->getRowIndex();

            foreach ($cellIterator as $j => $cell) {
                $array_data[$rowIndex][$j] = $cell->getValue();
            }
        }
        if ($array_data) {
            $this->view->data = $array_data;
        }
    }

    /**
     * Import comapny, persons from excel in system 
     */
    public function importdataAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        $error = $this->importModel->importdata($data, $this->companyModel, $this->personModel);
        if ($error) {
            $this->view->data = $error;
        }
    }

    /**
     * Delete contact
     */
    public function deletecontactsAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        $this->personModel->deletecontacts($data);
        $this->companyModel->deletecontacts($data);
    }

    /**
     * Get specific info for company and/or person
     */
    public function getinfoAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        $this->view->data = $this->personModel->fetch();
        $this->view->data['company'] = $this->companyModel->getinfo($data);
    }

    /**
     * Chege access and permission to specific contact or person records
     */
    public function changepermissionAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        if (isset($data['access'])) {
            $permission = explode('&', $data['access']);

            $accessArr = explode('=', $permission['0']);
            $groupArr = explode('=', $permission['1']);

            $access = $accessArr['1'];
            $group = $groupArr['1'];

            switch ($access) {
                case 0 :
                case 1 :
                    $tmpdata['group'] = null;
                    break;
                case 2 :
                    $tmpdata['group'] = $group;
                    break;
            }

            $group = $tmpdata['group'];

            if (isset($data["contact"])) {
                $newdata = array();

                foreach ($data["contact"] as $key => $value) {
                    $newdata['id'] = $value["id"];
                    $newdata['access'] = $access;
                    $newdata['group'] = $group;

                    if ($value["type"] == 1) {
                        $this->personModel->changePermission($newdata);
                    } else {
                        $this->companyModel->changePermission($newdata);
                    }
                }
            }
        }

        $this->view->data = 1;
    }

    /**
     * Chege access and permission to ALL contacts
     */
    public function changepermissioncontactsAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        if (isset($data['access'])) {
            $permission = explode('&', $data['access']);

            $accessArr = explode('=', $permission['0']);
            $groupArr = explode('=', $permission['1']);

            $access = $accessArr['1'];
            $group = $groupArr['1'];

            switch ($access) {
                case 0 :
                case 1 :
                    $tmpdata['group'] = null;
                    break;
                case 2 :
                    $tmpdata['group'] = $group;
                    break;
            }

            $group = $tmpdata['group'];

            $this->personModel->changePermissionAllContacts($access, $group);
        }

        $this->view->data = 1;
    }

    /**
     * Return all notes with specific tag
     */
    public function shownotesbytagAction() {
        $tag = $this->getRequest()->getParam('tag');
        $tagName = $this->getRequest()->getParam('tagname');

        if ($tag && $tagName) {
            $this->view->result = $this->activityModel->showNotesByTag($tag);

            $this->view->tag = $tagName;
        }
    }

    /**
     * Helper method to list all persons in autocomplete element in form
     */
    public function personautocompleterAction() {
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $value = $request->getParam('query', '');
        $this->view->result = $this->personModel->searchValueForMerga($value);
    }

    /**
     * Helper method to list all copanies in autocomplete element in form
     */
    public function companyautocompleterAction() {
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $value = $request->getParam('query', '');
        $this->view->result = $this->companyModel->searchValueForMerga($value);
    }

}
