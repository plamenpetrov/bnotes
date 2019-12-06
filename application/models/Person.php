<?php

class Model_Person extends Model_Abstract {

    protected $_dbTableClass = 'Model_DbTable_Person';
    protected $id;

    /**
     * @return the $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Delete all notes for specific person and then delete person
     * @return type
     */
    public function deleteNotes() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_person = ?', $this->id);
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
     * Search for person
     * @param type $value
     * @return type
     */
    public function searchValue($value) {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto("name like ?", '%' . $value . '%');
        $where2 = $db->quoteInto("firstname like ? or lastname like ?", '%' . $value . '%');
        $sql1 = $db->select()->from('person', array('id', 'type' => new Zend_Db_Expr("1"), 'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)")))->where($where2);
        $sql2 = $db->select()->from('company', array('id', 'type' => new Zend_Db_Expr("2"), 'name'))->where($where);
        $sql3 = $db->select()->from('case', array('id', 'type' => new Zend_Db_Expr("3"), 'name'))->where($where);
        $select = $db->select()
                ->union(array($sql1, $sql2, $sql3))
                ->order("id");
        $data = $db->fetchAll($select);
        $result['query'] = $value;
        $result['data'] = array();
        $result['suggestions'] = array();
        if ($data) {
            foreach ($data as $i => $row) {
                $result['data'][$i] = $row['id'] . $row['type'];
                $result['suggestions'][$i] = $row['name'];
            }
        }
        return $result;
    }

    /**
     * @param field_type $id
     */
    public function setId($id) {
        $this->id = $id;
    }

    /**
     * Return array of all person in specific company
     * @param type $id
     * @param type $limitend
     * @param type $limitstart
     * @return type
     */
    public function fetchByCompany($id, $limitend = 0, $limitstart = 6) {
        $userId = $this->getCurrentUser()->id;
        $table = $this->getTable();
        $db = $table->getAdapter();
        $selectPerson = $db->select()
                ->from(array('p' => 'person'), array('p.id', 'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)"), 'type' => new Zend_Db_Expr("1"), 'phone' => 'p.phone1', 'p.email'))
                ->where('id_company = ?', $id)
                ->order('id desc');
        //->limit($limitend, $limitstart);
        return $db->fetchAll($selectPerson);
    }

    /**
     * List of person phones
     * @return type
     */
    public function fetchPhones() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_person = ?', $this->id);
        $sql = $db->select()->from('person_phone')->where($where);
        return $db->fetchAll($sql);
    }

    /**
     * List of person emails
     * @return type
     */
    public function fetchEmails() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_person = ?', $this->id);
        $sql = $db->select()->from('person_email')->where($where);
        return $db->fetchAll($sql);
    }

    /**
     * Get person addresses
     * @return type
     */
    public function fetchAdresses() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_person = ?', $this->id);
        $sql = $db->select()->from('person_address')->where($where);

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
     * List of person websites
     * @return type
     */
    public function fetchWebsites() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id_person = ?', $this->id);
        $sql = $db->select()->from('person_website')->where($where);
        return $db->fetchAll($sql);
    }

    /**
     * Merge possible duplicated persons
     * @param type $id
     * @param type $newid
     */
    public function merge($id, $newid) {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $newdata = $table->fetchRow('id = ' . $newid)->toArray();
        $olddata = $table->fetchRow('id = ' . $id)->toArray();
        $bind['id_person'] = $id;

        //update task
        $where = $db->quoteInto('id_person = ?', $newid);
        $db->update('task', $bind, $where);

        //update case_person
        $where = $db->quoteInto('id_person = ?', $newid);
        $db->update('case_person', $bind, $where);

        //update note
        $where = $db->quoteInto('id_person = ?', $newid);
        $db->update('note', $bind, $where);

        //update person_address
        $where = $db->quoteInto('id_person = ?', $newid);
        $db->update('person_address', $bind, $where);

        //update person_email
        $where = $db->quoteInto('id_person = ?', $newid);
        $db->update('person_email', $bind, $where);

        //update person_meta
        $where = $db->quoteInto('id_person = ?', $newid);
        $db->update('person_meta', $bind, $where);

        //update person_phone
        $where = $db->quoteInto('id_person = ?', $newid);
        $db->update('person_phone', $bind, $where);

        //update person_website
        $where = $db->quoteInto('id_person = ?', $newid);
        $db->update('person_website', $bind, $where);


        //update tag_ref
        $bindtr['id_ref'] = $id;
        $where = $db->quoteInto('id_ref = ? and type = 1', $newid);
        $db->update('tag_ref', $bindtr, $where);

        //update person
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
     * List of all person based on user access and paginate
     * @param type $limit
     * @return type
     */
    public function fetchAll($limit = 20) {
        $userId = $this->getCurrentUser()->id;
        $table = $this->getTable();
        $db = $table->getAdapter();
        $select = $db->select()->from(array('p' => 'person'), array('p.id', 'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)")))
                ->join(array('c' => 'company'), 'p.id_company = c.id', array('company' => 'c.name'))
                ->joinLeft(array('g' => 'groups'), 'p.publ = g.id', '')
                ->joinLeft(array('pg' => 'profile_group'), 'pg.id_group = g.id', '')
                ->where('p.author = ? or p.publ = 0 or pg.id_profile = ?', $userId)
                ->order('p.id desc')
                ->limit($limit);
        return $db->fetchAll($select);
    }

    /**
     * Return list of contacts tagged by specific tag
     * @param type $strTag
     * @param type $limitstart
     * @param type $limitend
     * @return type
     */
    public function fetchContagtsByTag($strTag, $limitstart = 0, $limitend = 30) {
        $userId = $this->getCurrentUser()->id;
        $table = $this->getTable();
        $db = $table->getAdapter();

        $tags = explode(',', $strTag);

        //Zend_debug::Dump($tags);die;

        $selectPerson = $db->select()->from(array('tr' => 'tag_ref'), '')
                ->join(array('p' => 'person'), 'p.id = tr.id_ref and tr.type = 1', array('p.id', 'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)"), 'type' => new Zend_Db_Expr("1")))
                ->where('tr.id_tag = ?', $tags['0']);

        $selectCompany = $db->select()->from(array('tr' => 'tag_ref'), '')
                ->join(array('c' => 'company'), 'c.id = tr.id_ref and tr.type = 2', array('c.id', 'c.name', 'type' => new Zend_Db_Expr("0")))
                ->where('tr.id_tag = ?', $tags['0']);

        if (count($tags) > 1) {
            unset($tags['0']);

            foreach ($tags as $key => $tag) {
                $inner = $db->select()
                        ->from(array('trNew' => 'tag_ref'), array(
                            'id_tag',
                            'id_ref',
                            'type'
                        ))
                        ->where('trNew.id_tag = ?', $tag);

                $selectPerson->join(array('trNewPerson' . $key => $inner), 'trNewPerson' . $key . '.id_ref = tr.id_ref', '');
                $selectCompany->join(array('trNewCompany' . $key => $inner), 'trNewCompany' . $key . '.id_ref = tr.id_ref', '');
            }
            $select = $db->select()->union(array($selectPerson, $selectCompany))
                    ->order('name asc')
                    ->limit($limitend, $limitstart);
            //Zend_debug::Dump($select->__toString());die;
        }

        $select = $db->select()->union(array($selectPerson, $selectCompany))
                ->order('name asc')
                ->limit($limitend, $limitstart);
        //Zend_debug::Dump($select->__toString());die;
        return $db->fetchAll($select);
    }

    /**
     * Get total number of contacts
     * @return type
     */
    public function getContactsNumber() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $select = $db->select()->from(array('p' => 'person'), array('count(p.id) as num'));
        $select1 = $db->select()->from(array('c' => 'company'), array('count(c.id) as num'));
        return $db->fetchOne($select) + $db->fetchOne($select1);
    }

    /**
     * Delete person
     */
    public function delete() {
        $table = $this->getTable();
        $table->delete('id = ' . $this->id);
    }

    /**
     * Search for specific contact. Return paginated result based on search criteria
     * @param type $limitstart
     * @param type $limitend
     * @param type $filters
     * @return type
     */
    public function fetchAllConatacts($limitstart = 0, $limitend = 50, $filters = null) {

        $userId = $this->getCurrentUser()->id;
        $table = $this->getTable();
        $db = $table->getAdapter();
        $selectPerson = $db->select()->from(array('p' => 'person'), array('p.id', 'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)"), 'type' => new Zend_Db_Expr("1"), 'p.email', 'phone' => 'p.phone1', 'companyname' => 'c.name'))->join(array('c' => 'company'), 'c.id = p.id_company', array('avatar', 'id_company' => 'id'));
        $selectCompany = $db->select()->from(array('c' => 'company'), array('c.id', 'c.name', 'type' => new Zend_Db_Expr("0"), 'c.email', 'c.phone', 'companyname' => new Zend_Db_Expr("''"), 'c.avatar', 'id_company' => 'c.id'));

        $select = $db->select();

        if ($filters) {
            foreach ($filters as $k => $v) {
                if ($k == 'type') {
                    if ($v == 1) {
                        $selectCompany->where('0 = 1');
                    } else {
                        $selectPerson->where('0 = 1');
                    }
                    continue;
                }

                $is_required = $this->query('select contact_type from required_filters where name = "' . $k . '"');

                if (isset($is_required['0'])) {
                    if ($is_required['0']['contact_type'] == '1') {
                        $condP = $db->quoteInto('`p`.' . $k . ' like "%' . $v . '%"', '');
                        $selectPerson->where($condP);
                        $selectCompany->where('0 = 1');
                    } else {
                        if ($is_required['0']['contact_type'] == '2') {
                            $condP = $db->quoteInto('`p`.' . $k . ' like "%' . $v . '%"', '');
                            $selectPerson->where($condP);
                            $selectCompany->where('0 = 1');
                        } else {
                            $condC = $db->quoteInto('`c`.' . $k . ' like "%' . $v . '%"', '');
                            $condP = $db->quoteInto('`p`.' . $k . ' like "%' . $v . '%"', '');
                            $selectCompany->where($condC);
                            $selectPerson->where($condP);
                        }
                    }
                } else {
                    $condP = $db->quoteInto('`p`.' . $k . ' = ?', $v);
                    $condC = $db->quoteInto('`c`.' . $k . ' = ?', $v);

                    $selectPerson->where($condP);
                    $selectCompany->where($condC);
                }
            }
        }
        $select->union(array($selectPerson, $selectCompany))
                ->order('id desc')
                ->limit($limitend, $limitstart);

        //Zend_Debug::dump($select->__toString());die;
        $contact = $db->fetchAll($select);

        if ($contact) {
            $tagObj = new Model_Tag();

            foreach ($contact as $key => $value) {
                switch ($value['type']) {
                    case 0:
                        $contact[$key]['tags'] = $tagObj->fetchTags($value["id"], 2);
                        break;
                    case 1:
                        $contact[$key]['tags'] = $tagObj->fetchTags($value["id"], 1);
                        break;
                    default:
                        $contact[$key]['tags'] = $tagObj->fetchTags($value["id"], 1);
                        break;
                }
            }
        }
        //Zend_Debug::dump($contact);die;
        return $contact;
    }

    /**
     * Get latest persons
     * @return type
     */
    public function fetchLatest($number = 5) {
        $userId = $this->getCurrentUser()->id;
        $table = $this->getTable();
        $db = $table->getAdapter();
        $selectPerson = $db->select()->from(array('p' => 'person'), array('p.id', 'p.email', 'p.website', 'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)"), 'type' => new Zend_Db_Expr("1")))
                ->join(array('c' => 'company'), 'p.id_company = c.id', array('company' => 'c.name', 'id_company' => 'id'));
        //->joinLeft(array('g'=>'groups'),'p.publ = g.id','')
        //->joinLeft(array('pg'=>'profile_group'),'pg.id_group = g.id','')
        //->where('p.author = ? or p.publ = 0 or pg.id_profile = ?', $userId)
        $selectCompany = $db->select()->from(array('c' => 'company'), array('c.id', 'c.email', 'c.website', 'c.name', 'type' => new Zend_Db_Expr("2"), 'c.name', 'c.id'));
        $selectCase = $db->select()->from(array('proj' => 'case'), array('proj.id', 'proj.name', 'proj.name', 'proj.name', 'type' => new Zend_Db_Expr("3"), 'proj.name', 'proj.id'));

        $select = $db->select()->union(array($selectPerson, $selectCompany, $selectCase))
                ->order('id desc')
                ->limit($number);

        return $db->fetchAll($select);
    }

    /**
     * Edit person data
     * @param type $data
     * @return boolean
     */
    public function edit($data) {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $data['id_company'] = $data['company'];
        $where = $db->quoteInto('id_person = ?', $data["id"]);


        $db->delete('person_phone', $where);
        $db->delete('person_email', $where);
        $db->delete('person_website', $where);
        $db->delete('person_address', $where);

        if (isset($data['phone']) && $data['phone']) {
            foreach ($data['phone'] as $phone) {
                $bind = array('id_person' => $data["id"], 'phone' => $phone, 'phone_type' => 1);
                $db->insert('person_phone', $bind);
            }
        }
        if (isset($data['email1']) && $data['email1']) {
            foreach ($data['email1'] as $email) {
                $bind = array('id_person' => $data["id"], 'name' => $email, 'email_type' => 1);
                $db->insert('person_email', $bind);
            }
        }
        if (isset($data['website1']) && $data['website1']) {
            foreach ($data['website1'] as $email) {
                $bind = array('id_person' => $data["id"], 'name' => $email, 'website_type' => 1);
                $db->insert('person_website', $bind);
            }
        }
        if (isset($data['address1']) && $data['address1']) {
            $person_address = array();
            $person_address_columns = array('address1', 'city1', 'state1', 'zip1', 'country1', 'address_type1');

            foreach ($data as $key => $value) {
                if (in_array($key, $person_address_columns)) {
                    foreach ($value as $k => $v) {
                        $person_address[$k][substr($key, 0, -1)] = $v;
                    }
                }
            }

            foreach ($person_address as $person) {
                $person['id_person'] = $data["id"];
                $db->insert('person_address', $person);
            }

            /*
              foreach($data['address1'] as $email)
              {
              $bind = array('id_person'=>$data["id"], 'address'=>$email, 'address_type'=>1);
              $db->insert('person_address', $bind);
              }
             */
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
        //Zend_Debug::dump($data);die;
        $data = $this->unsetNonTableFields($data);
        $where = $db->quoteInto('id = ?', $data['id']);

        $table->update($data, $where);
        return true;
    }

    /**
     * Return data for specific person
     * @return type
     */
    public function fetch() {
        $table = $this->getTable();
        return $table->fetchRow('id = ' . $this->id)->toArray();
    }

    /**
     * Get person data and related company data
     * @return type
     */
    public function fetchRecord() {
        $table = $this->getTable();
        $person = $table->fetchRow('id = ' . $this->id)->toArray();
        $db = $table->getAdapter();
        if (isset($person['id'])) {
            $selectCompany = $db->select()->from('company')->where('id = ' . $person['id_company']);
            $person['company'] = $db->fetchRow($selectCompany);
        }
        return $person;
    }

    /**
     * Get all notes for person
     * @param type $start
     * @param type $limit
     * @return type
     */
    public function getNotes($start = 0, $limit = 6) {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('n.id_person = ?', $this->id);
        $select = $db->select()->from(array('n' => 'note'))
                        ->join(array('p' => 'profile'), 'p.id = n.author', array('id_profile' => 'id', 'profile' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
                        ->join(array('per' => 'person'), 'per.id = ' . $this->id, array('id_person' => 'id', 'personfirstname' => 'firstname', 'personlastname' => 'lastname', 'title'))
                        ->joinLeft(array('c' => 'company'), 'c.id = n.id_company', array('id_company' => 'id', 'company' => 'name', 'cemail' => 'email', 'avatar'))
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
     * Add person
     * @param type $data
     * @return type
     */
    public function add($data) {

        if (isset($data['firstname']) && isset($data['lastname']) && isset($data['id_company'])) {
            $data['author'] = $this->getCurrentUser()->id;
            $newdata = $this->unsetNonTableFields($data);

            $table = $this->getTable();
            $person = $table->fetchRow('firstname = \'' . $data['firstname'] . '\' and lastname = \'' . $data['lastname'] . '\' and id_company = \'' . $data['id_company'] . '\' ');
            if ($person) {
                $person = $person->toArray();
                return $person['id'];
            }

            return $table->insert($newdata);
        }
    }

    /**
     * Change person permissions
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
     * Change permissions to all persons
     * @param type $access
     * @param type $group
     * @return boolean
     */
    public function changePermissionAllContacts($access, $group) {
        try {
            $table = $this->getTable();
            $db = $table->getAdapter();

            $db->beginTransaction();
            //$user = $this->getCurrentUser()->id
            //$where = $db->quoteInto('id_autohr = ?', $user);

            $sqlPerson = 'UPDATE person
					SET access = "' . $access . '"';

            $sqlCompany = 'UPDATE company
					SET access = "' . $access . '"';

            if ($group) {
                $sqlGroup = ', `group` = "' . $group . '"';
            } else {
                $sqlGroup = ', `group` = NULL';
            }

            $sqlPerson .= $sqlGroup;
            $sqlCompany .= $sqlGroup;

            $db->query($sqlPerson);
            $db->query($sqlCompany);

            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
            Zend_Debug::dump($e->getMessage());
            die;
        }

        return true;
    }

    /**
     * Get all accessible persons for current user
     * @return type
     */
    public function getPersons() {
        $userId = $this->getCurrentUser()->id;
        $table = $this->getTable();
        $db = $table->getAdapter();
        $select = $db->select()->from(array('p' => 'person'), array('p.id', 'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)")))
                ->joinLeft(array('g' => 'groups'), 'p.publ = g.id', '')
                ->joinLeft(array('pg' => 'profile_group'), 'pg.id_group = g.id', '')
                ->where('author = ? or publ = 0 or pg.id_profile = ?', $userId);
        return $db->fetchAll($select);
    }

    /**
     * Perform query to search for same persons to be merged
     * @param type $value
     * @return type
     */
    public function searchValueForMerga($value) {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto("name like ?", '%' . $value . '%');
        $where2 = $db->quoteInto("firstname like ? or lastname like ?", '%' . $value . '%');
        $select = $db->select()->from('person', array('id', 'type' => new Zend_Db_Expr("1"), 'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)")))->where($where2);

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
     * Add files to person
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
     * Delete contacts
     * @param type $data
     * @return boolean
     */
    public function deletecontacts($data) {
        if (isset($data['person']) && $data['person']) {
            foreach ($data['person'] as $person) {
                $this->personModel->setId($person);
                $this->personModel->deleteNotes();
                $this->personModel->delete();
            }
            return true;
        }

        return false;
    }

    /**
     * Get person info
     * @param type $data
     * @return type
     */
    public function getinfo($data) {
        if (isset($data['id_person']) && $data['id_person']) {
            $this->setId($data['id_person']);
            return $this->fetch();
        }
    }

}
