<?php

class Model_Company extends Model_Abstract {

    protected $_dbTableClass = 'Model_DbTable_Company';
    protected $_id;

    public function __construct($user_id = null) {
        
    }

    /**
     * @return the $_id
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * @param field_type $_id
     */
    public function setId($_id) {
        $this->_id = $_id;
    }

    /**
     * Merge companies if they are possibly duplicated
     * @param type $id
     * @param type $newid
     */
    public function merge($id, $newid) {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $newdata = $table->fetchRow('id = ' . $newid)->toArray();
        $olddata = $table->fetchRow('id = ' . $id)->toArray();
        $bind['id_company'] = $id;

        //update task
        $where = $db->quoteInto('id_company = ?', $newid);
        $db->update('task', $bind, $where);

        //update case_person
        $where = $db->quoteInto('id_company = ?', $newid);
        $db->update('case_company', $bind, $where);

        //update note
        $where = $db->quoteInto('id_company = ?', $newid);
        $db->update('note', $bind, $where);

        //update person_address
        $where = $db->quoteInto('id_company = ?', $newid);
        $db->update('company_address', $bind, $where);

        //update person_email
        $where = $db->quoteInto('id_company = ?', $newid);
        $db->update('company_email', $bind, $where);

        //update person_meta
        $where = $db->quoteInto('id_company = ?', $newid);
        $db->update('company_meta', $bind, $where);

        //update person_phone
        $where = $db->quoteInto('id_company = ?', $newid);
        $db->update('company_phone', $bind, $where);

        //update person_website
        $where = $db->quoteInto('id_company = ?', $newid);
        $db->update('company_website', $bind, $where);


        //update tag_ref
        $bindtr['id_ref'] = $id;
        $where = $db->quoteInto('id_ref = ? and type = 2', $newid);
        $db->update('tag_ref', $bindtr, $where);

        //update person
        $where = $db->quoteInto('id_company = ?', $newid);
        $db->update('person', $bind, $where);

        //update company
        foreach ($newdata as $k => $v) {
            if (!$v)
                $newdata[$k] = $olddata[$k];
        }

        unset($newdata['id']);
        $where = $db->quoteInto('id = ?', $id);
        $table->update($newdata, $where);
        $where = $db->quoteInto('id = ?', $newid);
        $table->delete($where);
    }

    /**
     * Return array phones for sepecific company
     * @return type
     */
    public function fetchPhones() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_company = ?', $this->_id);
        $sql = $db->select()->from('company_phone')->where($where);
        return $db->fetchAll($sql);
    }

    /**
     * Return number of people for specific company
     * @return type
     */
    public function getNumberOfPeople() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_company = ?', $this->_id);
        $select = $db->select()->from('person', 'id')->where($where);

        return $db->fetchAll($select);
    }

    /**
     * Return array emails for sepecific company
     * @return type
     */
    public function fetchEmails() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_company = ?', $this->_id);
        $sql = $db->select()->from('company_email')->where($where);
        return $db->fetchAll($sql);
    }

    /**
     * Return array addresses for sepecific company
     * @return type
     */
    public function fetchAdresses() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_company = ?', $this->_id);
        $sql = $db->select()->from('company_address')->where($where);

        $addresses = $db->fetchAll($sql);

        if ($addresses) {
            $coutries = $db->query('select id, name from nmcl_country')->fetchAll();
            $address_type = $db->query('select id, name from nmcl_addresstype')->fetchAll();

            foreach ($addresses as $key => $value) {
                $addresses[$key]['country'] = $coutries;
                $addresses[$key]['address_type'] = $address_type;
            }
        }

        return $addresses;
    }

    /**
     * Return array websites for sepecific company
     * @return type
     */
    public function fetchWebsites() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_company = ?', $this->_id);
        $sql = $db->select()->from('company_website')->where($where);
        return $db->fetchAll($sql);
    }

    /**
     * Create new company
     * @param type $data
     * @return type
     */
    public function add($data) {
        if (isset($data['company'])) {
            $data['name'] = $data['company'];
            $data['author'] = $this->getCurrentUser()->id;
            $newdata = $this->unsetNonTableFields($data);
            $table = $this->getTable();
            $Company = $table->fetchRow('name = \'' . $data['name'] . '\'');
            if ($Company) {
                $Company = $Company->toArray();
                return $Company['id'];
            }
            return $table->insert($newdata);
        }
    }

    /**
     * Return array of companies base on user permissions
     * @return type
     */
    public function getCompanies() {
        $userId = $this->getCurrentUser()->id;
        $table = $this->getTable();
        $db = $table->getAdapter();
        $select = $db->select()->from(array('c' => 'company'), array('c.id', 'c.name'))
                ->joinLeft(array('g' => 'groups'), 'c.public = g.id', '')
                ->joinLeft(array('pg' => 'profile_group'), 'pg.id_group = g.id', '')
                ->where('author = ? or public = 0 or pg.id_profile = ?', $userId);
        return $db->fetchAll($select);
    }

    /**
     * Return array of companies base on user permissions
     * @return type
     */
    public function fetchPairs() {
        $userId = $this->getCurrentUser()->id;
        $table = $this->getTable();
        $db = $table->getAdapter();
        $select = $db->select()->from(array('c' => 'company'), array('c.id', 'c.name'))
                ->joinLeft(array('g' => 'groups'), 'c.public = g.id', '')
                ->joinLeft(array('pg' => 'profile_group'), 'pg.id_group = g.id', '')
                ->where('author = ? or public = 0 or pg.id_profile = ?', $userId);
        return $db->fetchPairs($select);
    }

    /**
     * Return data for specific company
     * @return type
     */
    public function fetchRecord() {
        $table = $this->getTable();
        return $table->fetchRow('id = ' . $this->_id)->toArray();
    }

    /**
     * Get all notes for specific company
     * @param type $start
     * @param type $limit
     * @return type
     */
    public function getNotes($start = 0, $limit = 6) {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('n.id_company = ?', $this->_id);
        $select = $db->select()->from(array('n' => 'note'))
                        ->join(array('p' => 'profile'), 'p.id = n.author', array('id_profile' => 'id', 'profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
                        ->join(array('c' => 'company'), 'c.id = n.id_company', array('id_company' => 'id', 'company' => 'name', 'cemail' => 'email', 'avatar'))
                        ->joinLeft(array('per' => 'person'), 'per.id = n.id_person', array('id_person' => 'id', 'personfirstname' => 'firstname', 'personlastname' => 'lastname', 'title'))
                        ->where($where)
                        ->order('n.id desc')->limit($limit, $start);
        $comments = $db->fetchAll($select);
        foreach ($comments as $i => $comment) {
            $fileModel = new Model_File();
            $comments[$i]['files'] = $fileModel->fetchByNote($comment['id']);
        }
        return $comments;
    }

    /**
     * Delete all notes for company
     * @return type
     */
    public function deleteNotes() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_company = ?', $this->_id);
        $select = $db->select()->from('note')->where($where);
        $notes = $db->fetchAll($select);
        $modelActivity = new Model_Activity();
        foreach ($notes as $note) {
            $modelActivity->setId($note['id']);

            try {
                $modelActivity->deleteNote($note['id']);
            } catch (Exception $e) {
                
            }
        }
        return $db->delete('note', $where);
    }

    /**
     * Delete company
     */
    public function delete() {
        $table = $this->getTable();
        $company = $table->fetchRow('id = ' . $this->_id)->toArray();
        $db = $table->getAdapter();
        $dbconf = $db->getConfig();
        $location = PUBLIC_PATH . $company['avatar'];

        if ($table->delete('id = ' . $this->_id)) {
            try {
                unlink($location);
            } catch (Exception $e) {
                
            }
        }
    }

    /**
     * Get company data
     * @return type
     */
    public function fetch() {
        $table = $this->getTable();
        return $table->fetchRow('id = ' . $this->_id)->toArray();
    }

    /**
     * Edit company
     * @param type $data
     * @return boolean
     */
    public function edit($data) {
        $table = $this->getTable();
        $db = $table->getAdapter();

        $where = $db->quoteInto('id_company = ?', $data["id"]);

        $db->delete('company_phone', $where);
        $db->delete('company_email', $where);
        $db->delete('company_website', $where);
        $db->delete('company_address', $where);

        if (isset($data['phone1']) && $data['phone1']) {
            foreach ($data['phone1'] as $phone) {
                $bind = array('id_company' => $data["id"], 'phone' => $phone, 'phone_type' => 1);
                $db->insert('company_phone', $bind);
            }
        }
        if (isset($data['email1']) && $data['email1']) {
            foreach ($data['email1'] as $email) {
                $bind = array('id_company' => $data["id"], 'name' => $email, 'email_type' => 1);
                $db->insert('company_email', $bind);
            }
        }
        if (isset($data['website1']) && $data['website1']) {
            foreach ($data['website1'] as $email) {
                $bind = array('id_company' => $data["id"], 'name' => $email, 'website_type' => 1);
                $db->insert('company_website', $bind);
            }
        }
        if (isset($data['address1']) && $data['address1']) {

            $company_address = array();
            $company_address_columns = array('address1', 'city1', 'state1', 'zip1', 'country1', 'address_type1');

            foreach ($data as $key => $value) {
                if (in_array($key, $company_address_columns)) {
                    foreach ($value as $k => $v) {
                        $company_address[$k][substr($key, 0, -1)] = $v;
                    }
                }
            }

            foreach ($company_address as $company) {
                $company['id_company'] = $data["id"];
                $db->insert('company_address', $company);
            }
        }

        if (isset($data['access'])) {
            switch ($data['access']) {
                case 0 :
                case 1 :
                    $newdata['group'] = null;
                    break;
                case 2 :
                    $newdata['group'] = $data['group'];
                    break;
            }

            $data['group'] = $newdata['group'];
        }

        $data = $this->unsetNonTableFields($data);
        $where = $db->quoteInto('id = ?', $data['id']);
        $table->update($data, $where);
        return true;
    }

    /**
     * Change permission to company
     * @param type $data
     * @return boolean
     */
    public function changePermission($data) {
        $table = $this->getTable();
        $db = $table->getAdapter();

        $data = $this->unsetNonTableFields($data);
        $where = $db->quoteInto('id = ?', $data['id']);

        $table->update($data, $where);
        return true;
    }

    /**
     * Search company for merge
     * @param type $value
     * @return type
     */
    public function searchValueForMerga($value) {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto("name like ?", '%' . $value . '%');

        $select = $db->select()->from('company', array('id', 'name'))->where($where);

        $data = $db->fetchAll($select);
        $result['query'] = $value;
        $result['data'] = array();
        $result['suggestions'] = array();
        if ($data) {
            foreach ($data as $i => $row) {
                $result['data'][$i] = $row['id'];
                $result['suggestions'][$i] = $row['name'];
            }
        }
        return $result;
    }

    /**
     * Save files to company
     * @param type $data
     */
    public function saveFiles($data) {
        if ($_FILES) {
            $db = Zend_Registry::get('db');
            $adapter = new Zend_File_Transfer_Adapter_Http();
            // a call to $form->getValues() has been previously made
            foreach ($adapter->getFileInfo() as $file => $info) {
                if ($adapter->isUploaded($file)) {
                    $dbconf = $db->getConfig();
                    $FileRenamed = $data["id"] . '.' . substr($info['type'], 6);

                    $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $dbconf['dbname'] . DIRECTORY_SEPARATOR . $data["id"] . DIRECTORY_SEPARATOR;
                    $location = '/avatar/' . $dbconf['dbname'] . '/' . $data["id"] . '/' . $FileRenamed;
                    $name = $adapter->getFileName($file);
                    $fname = $path . $FileRenamed;

                    $data['avatar'] = $location;
                    /**
                     *  Let's inject the renaming filter
                     */
                    $adapter->addFilter(new Zend_Filter_File_Rename(array('target' => $fname, 'overwrite' => true)), null, $file);
                    /**
                     * And then we call receive manually
                     */
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
                }
            }
        }
    }

    /**
     * Save files to company record
     * @param type $data
     */
    public function saveRecordFiles($id_note, $data) {
        if ($_FILES) {

            $db = Zend_Registry::get('db');
            $adapter = new Zend_File_Transfer_Adapter_Http();
            // a call to $form->getValues() has been previously made
            foreach ($adapter->getFileInfo() as $file => $info) {
                if ($adapter->isUploaded($file)) {
                    $dbconf = $db->getConfig();
                    $FileRenamed = $info['name']; //md5($info['name']).'.'.substr($info['type'], 6);
                    $path = PUBLIC_PATH . DIRECTORY_SEPARATOR . 'notes' . DIRECTORY_SEPARATOR . $dbconf['dbname'] . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . $id_note . DIRECTORY_SEPARATOR;
                    $location = '/notes/' . $dbconf['dbname'] . '/files/' . $id_note . '/' . $FileRenamed;
                    $name = $adapter->getFileName($file);
                    $fname = $path . $FileRenamed;
                    /**
                     *  Let's inject the renaming filter
                     */
                    $adapter->addFilter(new Zend_Filter_File_Rename(array('target' => $fname, 'overwrite' => true)), null, $file);
                    /**
                     * And then we call receive manually
                     */
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
                    $modelFile = new Model_File();
                    if (substr($info['type'], 0, 5) == 'image') {
                        $modelFile->add(array('id_note' => $id_note, 'file' => $location, 'type' => 1));
                    } elseif (substr($info['name'], -3) == 'pdf') {
                        $modelFile->add(array('id_note' => $id_note, 'file' => $location, 'type' => 2));
                    } elseif (substr($info['name'], -3) == 'xls') {
                        $modelFile->add(array('id_note' => $id_note, 'file' => $location, 'type' => 3));
                    } elseif (substr($info['name'], -3) == 'doc') {
                        $modelFile->add(array('id_note' => $id_note, 'file' => $location, 'type' => 4));
                    } elseif (substr($info['name'], -3) == 'ppt') {
                        $modelFile->add(array('id_note' => $id_note, 'file' => $location, 'type' => 5));
                    } else {
                        $modelFile->add(array('id_note' => $id_note, 'file' => $location));
                    }
                }
            }
        }
    }

    /**
     * Delete company and all related notes
     * @param type $data
     */
    public function deletecontacts($data) {
        if (isset($data['company']) && $data['company']) {
            foreach ($data['company'] as $company) {
                $this->setId($company);
                $this->deleteNotes();
                $this->delete();
            }
        }
    }

    /**
     * Get info for company
     * @param type $data
     * @return type
     */
    public function getinfo($data) {
        if (isset($data['id_company']) && $data['id_company']) {
            $this->setId($data['id_company']);
            return $this->fetch();
        }
    }

}
