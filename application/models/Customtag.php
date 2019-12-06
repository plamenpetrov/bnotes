<?php

class Model_Customtag extends Model_Abstract {

    protected $_dbTableClass = 'Model_DbTable_Customtag';
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
     * Create tag
     * @param type $data
     * @return type
     */
    public function add($data) {
        if (isset($data['val'])) {
            $table = $this->getTable();
            $db = $table->getAdapter();
            $newdata['label'] = $data['val'];
            $lastrow = $table->fetchRow('1=1', 'id desc');
            if ($lastrow) {
                $lastrow = $lastrow->toArray();
                $num = $lastrow['id'] + 1;
                $newdata['name'] = 'col' . $num;
            } else {
                $newdata['name'] = 'col1';
            }
            try {
                $db->beginTransaction();
                $insert = $table->insert($newdata);
                $sql = 'ALTER TABLE `person` ADD COLUMN `' . $newdata['name'] . '` VARCHAR(255) NULL;';
                $db->query($sql);
                $sql = 'ALTER TABLE `company` ADD COLUMN `' . $newdata['name'] . '` VARCHAR(255) NULL;';
                $db->query($sql);
                $db->commit();
                return $insert;
            } catch (Exception $e) {
                $db->rollBack();
            }
        }
    }

    /**
     * Remove tag
     * @param type $data
     * @return boolean
     */
    public function remove($data) {
        if (isset($data['id'])) {
            $table = $this->getTable();
            $db = $table->getAdapter();
            $where = $db->quoteInto('id = ?', $data['id']);
            $row = $table->fetchRow($where);
            if ($row) {
                $row = $row->toArray();
                try {
                    $db->beginTransaction();

                    $sql = 'ALTER TABLE `person` DROP COLUMN `' . $row['name'] . '`;';
                    $db->query($sql);
                    $sql = 'ALTER TABLE `company` DROP COLUMN `' . $row['name'] . '`;';
                    $db->query($sql);

                    $delete = $table->delete($where);
                    $db->commit();
                    return $delete;
                } catch (Exception $e) {
                    $db->rollBack();
                }
            } else {
                
            }
        }
        return false;
    }

    /**
     * Rename tag
     * @param type $data
     * @return boolean
     */
    public function rename($data) {
        if (isset($data['id']) && isset($data['val'])) {
            $table = $this->getTable();
            $newdata['label'] = $data['val'];
            $where = $table->getAdapter()->quoteInto('id = ?', $data['id']);
            return $table->update($newdata, $where);
        }
        return false;
    }

}
