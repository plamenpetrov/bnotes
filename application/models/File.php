<?php

class Model_File extends Model_Abstract {

    protected $_dbTableClass = 'Model_DbTable_File';
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
     * Store file to note
     * @param type $data
     * @return boolean
     */
    public function add($data) {
        $table = $this->getTable();
        if (isset($data['file']) && $data['file'] && isset($data['id_note']) && $data['id_note']) {
            $newdata['file'] = $data['file'];
            $newdata['id_note'] = $data['id_note'];
            if (isset($data['type']))
                $newdata['type'] = $data['type'];
            return $table->insert($newdata);
        }
        return false;
    }

    /**
     * Get all files for specific note
     * @param type $id
     * @return type
     */
    public function fetchByNote($id) {
        $table = $this->getTable();
        $result = $table->fetchAll('id_note = ' . $id);

        if ($result) {
            return $result->toArray();
        }
        return null;
    }

    /**
     * Delete file for note
     * @param type $id
     */
    public function deleteByNote($id) {
        $table = $this->getTable();
        $result = $table->fetchAll('id_note = ' . $id);
        $array = array();
        if ($result) {
            foreach ($result as $file) {

                if (unlink(PUBLIC_PATH . str_replace('/', DIRECTORY_SEPARATOR, $file['file']))) {
                    $table->delete('id = ' . $file['id']);
                }
            }
        }
    }

}
