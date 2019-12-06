<?php

class Model_Profile extends Model_Abstract {

    protected $_dbTableClass = 'Model_DbTable_Profile';
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
     * Set user admin role
     * @param type $id
     * @return type
     */
    public function makeadmin($id) {
        $where = $this->getTable()->getAdapter()->quoteInto('id = ?', $id);
        return $this->getTable()->update(array('admin' => 1), $where);
    }

    /**
     * Restrict user from admin access
     * @param type $id
     * @return type
     */
    public function removeadmin($id) {
        $where = $this->getTable()->getAdapter()->quoteInto('id = ?', $id);
        return $this->getTable()->update(array('admin' => 0), $where);
    }

    /**
     * Get user data
     * @return type
     */
    public function fetch() {
        return $this->getTable()->fetchRow('id = ' . $this->_id);
    }

    /**
     * List of all users
     * @return type
     */
    public function fetchAll() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $select = $db->select()->from('profile');


        $profiles = $db->fetchAll($select);
        foreach ($profiles as $i => $profile) {
            $select = $db->select()->from(array('pg' => 'profile_group'))
                    ->join(array('g' => 'groups'), 'pg.id_group = g.id')
                    ->where('pg.id_profile = ?', $profile['id']);
            //Zend_Debug::dump($select->__toString());
            $profiles[$i]['groups'] = $db->fetchAll($select);
        }
        return $profiles;
    }

    /**
     * Update user data
     * @param type $data
     */
    public function save($data) {
        $data = $this->unsetNonTableFields($data);
        $id = $data ['id'];
        unset($data ['id']);
        //Zend_Debug::dump($data);die;
        $where = $this->getTable()->getAdapter()->quoteInto('id = ?', $id);
        $this->getTable()->update($data, $where);
    }

    /**
     * List of all users only firstname and family
     * @return type
     */
    public function fetchPairs() {
        $table = $this->getTable();
        $db = $table->getDefaultAdapter();
        $select = $db->select()->from('profile', array(
            'id',
            'name' => new Zend_Db_Expr("CONCAT(firstname, ' ', lastname)")
                )
        );
        return $db->fetchPairs($select);
    }

    /**
     * Create new user
     * @param type $data
     * @return boolean
     */
    public function add($data) {

        $db2 = Zend_Registry::get('db2');
        $select = $db2->select()->from('user', 'id')->where('username = ?', $data["username"]);
        if ($db2->fetchOne($select)) {
            return false;
        }
        $dbname = Zend_Registry::get('config')->database->params->dbname;
        $select = $db2->select()->from('company', 'id')->where('`database` = ?', $dbname);
        $companyId = $db2->fetchOne($select);
        if ($companyId) {
            try {
                $username = $data["username"];
                $password = $data["password"];
                $db2->insert('user', array(
                    'id_company' => $companyId,
                    'username' => $username,
                    'password' => $password
                ));
                $data = $this->unsetNonTableFields($data);
                $data['id_role'] = 1;
                $data['email'] = $username;
                $idProfile = $this->getTable()->insert($data);
                return $this->getTable()->getAdapter()->insert('user', array(
                            'id_profile' => $idProfile,
                            'username' => $username,
                            'password' => $password
                ));
            } catch (Exception $e) {
                Zend_Debug::dump($e->getMessage());
                die;
                return false;
            }
        }
        return false;
    }

}
