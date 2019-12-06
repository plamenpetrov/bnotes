
<?php

class Model_User extends Model_Abstract {

    protected $_dbTableClass = 'Model_DbTable_User';
    protected $_user_id;
    public $_record;

    public function __construct($user_id = null) {
        parent::__construct();
        if (null != $user_id) {
            $table = $this->getTable();
            $select = $table->select()->where('id_profile = ?', $user_id);
            $user = $table->fetchRow($select)->toArray();
            $this->setUser_id($user['id_profile']);
            $this->_record = $user;
        }
    }

    /**
     * Get current user data
     * @return type
     */
    public function toArray() {
        if (null != $this->getUser_id()) {
            return $this->fetch();
        }
    }

    /**
     * Find user session 
     * @param type $hash
     * @return boolean
     */
    public function getUserByHash($hash) {
        $db2 = Zend_Registry::get('db2');
        $select = $db2->select()->from(array('s' => 'session'), 'id')->join(array('u' => 'user'), 'u.id = s.id_user', array('username', 'password'))->where('s.session_salt = ? ', $hash)->order('id desc');
        $auth = $db2->fetchAll($select);
        if ($auth) {
            $condition = array(
                'id_user <> ?' => $auth['0']['id']
            );
            $db2->delete('session', $condition);
            return $auth['0'];
        }
        return false;
    }

    /**
     * 
     * @param type $id
     */
    public function setUser_id($id) {
        $this->_user_id = $id;
    }

    /**
     * 
     * @return type
     */
    public function getUser_id() {
        return $this->_user_id;
    }

    /**
     * Create new user
     * @param type $data
     * @return boolean
     */
    public function save($data) {
        $id = $data['id'];
        $oldPass = $data['oldpassword'];
        $data = $this->unsetNonTableFields($data);
        $userId = $this->getCurrentUser()->id;
        $this->setUser_id($userId);
        $username = $this->fetch()->toArray();
        if ($username["password"] === $oldPass) {
            $db2 = Zend_Registry::get('db2');
            $where = $db2->quoteInto('username = ?', $username['username']);
            if ($db2->update('user', $data, $where)) {
                $where2 = $this->getTable()->getAdapter()->quoteInto('id_profile = ?', $id);
                $this->getTable()->update($data, $where2);
                return true;
            }
        } else {
            return false;
        }
        return false;
    }

    /**
     * Fetch user data by profile id
     * @return type
     */
    public function fetch() {
        return $this->getTable()->fetchRow('id_profile = ' . $this->_user_id);
    }

}
