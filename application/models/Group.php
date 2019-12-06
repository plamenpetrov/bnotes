<?php

class Model_Group extends Model_Abstract {

    protected $_dbTableClass = 'Model_DbTable_Group';
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
     * Get all profiles from specific group
     * @return string
     */
    public function fetch() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $select = $db->select()
                ->from(array('g' => 'groups'), array('gid' => 'id', 'groupname' => 'name'))
                ->joinLeft(array('gp' => 'profile_group'), 'g.id = gp.id_group', '')
                ->joinLeft(array('p' => 'profile'), 'p.id = gp.id_profile', array('pid' => 'id', 'firstname', 'lastname'))
                ->where('g.id = ? ', $this->_id);
        $result = $db->fetchAll($select);
        foreach ($result as $row) {
            $groups["id"] = $row["gid"];
            $groups["groupname"] = $row["groupname"];
            $groups["people"][$row["pid"]] = $row["firstname"] . ' ' . $row["lastname"];
        }
        return $groups;
    }

    /**
     * Get all groups
     * @return type
     */
    public function fetchAll() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $select = $db->select()
                ->from(array('g' => 'groups'), array('gid' => 'id', 'gname' => 'name'))
                ->joinLeft(array('gp' => 'profile_group'), 'g.id = gp.id_group', '')
                ->joinLeft(array('p' => 'profile'), 'p.id = gp.id_profile', array('pid' => 'id', 'firstname', 'lastname'));
        $result = $db->fetchAll($select);
        $groups = null;
        foreach ($result as $row) {
            $groups[$row["gid"]]["id"] = $row["gid"];
            $groups[$row["gid"]]["name"] = $row["gname"];
            $groups[$row["gid"]]["people"][$row["pid"]]["firstname"] = $row["firstname"];
            $groups[$row["gid"]]["people"][$row["pid"]]["lastname"] = $row["lastname"];
        }
        //Zend_Debug::dump($groups);die;
        return $groups;
    }

    /**
     * Create new group
     * @param type $data
     * @return boolean
     */
    public function add($data) {
        $table = $this->getTable();
        $name['name'] = $data['groupname'];
        $id = $table->insert($name);
        $db = $table->getAdapter();

        try {
            foreach ($data['people'] as $i => $v) {
                $bind["id_group"] = $id;
                $bind["id_profile"] = $v;
                $db->insert('profile_group', $bind);
            }
            return true;
        } catch (Exception $e) {
            
        }
        return false;
    }

    /**
     * Edit group
     * @param type $data
     * @return boolean
     */
    public function edit($data) {
        $table = $this->getTable();
        $name['name'] = $data['groupname'];
        $table->update($name, 'id = ' . $data["id"]);
        $db = $table->getAdapter();

        try {
            $where = $db->quoteInto('id_group = ?', $data["id"]);
            $db->delete('profile_group', $where);
            foreach ($data['people'] as $i => $v) {
                $bind["id_group"] = $data["id"];
                $bind["id_profile"] = $v;
                $db->insert('profile_group', $bind);
            }
            return true;
        } catch (Exception $e) {
            
        }
        return false;
    }

    /**
     * Delete group
     * @return boolean
     */
    public function delete() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        try {
            $where = $db->quoteInto('id_group = ?', $this->_id);
            $db->delete('profile_group', $where);
            return $table->delete('id = ' . $this->_id);
        } catch (Exception $e) {
            
        }
        return false;
    }

}
