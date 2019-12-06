<?php

class Model_Task extends Model_Abstract {

    protected $_dbTableClass = 'Model_DbTable_Task';
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
     * Get list of all tasks that are not finished
     * @return type
     */
    public function fetchAll() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('author = ? or owner = ?', $this->getCurrentUser()->id);
        $select = $db->select()
                ->from(array('t' => 'task'))
                ->joinLeft(array('tc' => 'nmcl_taskcategory'), 'tc.id = t.id_category', array('category' => 'name'))
                ->joinLeft(array('p' => 'profile'), 'p.id = t.owner', array('ownername' => new Zend_Db_Expr("CONCAT(p.firstname, ' ', p.lastname)")))
                ->where($where)
                ->where('is_finished = ?', 0)
                ->order('duedate desc');


        return $db->fetchAll($select);
    }

    /**
     * Get task categories
     * @return type
     */
    public function fetchCategory() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $select = $db->select()->from('nmcl_taskcategory', array('id', 'name'));
        return $db->fetchAll($select);
    }

    /**
     * Get task due date
     * @return type
     */
    public function fetchTaskDue() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $select = $db->select()->from('nmcl_taskdue', array('id', 'name'));
        return $db->fetchAll($select);
    }

    /**
     * Create new task
     * @param type $data
     * @return type
     */
    public function add($data) {
        $newdata['author'] = $this->getCurrentUser()->id;
        $newdata['public'] = $data['publ'];
        $newdata['owner'] = $data['owner'];
        //$newdata['duedate'] = date("Y-m-d H:i:s", strtotime($data['due']));
        $newdata['name'] = $data['name'];
        $newdata['id_category'] = $data['cat'];
        if (isset($data['id_person']))
            $newdata['id_person'] = $data['id_person'];
        if (isset($data['id_company']))
            $newdata['id_company'] = $data['id_company'];
        if (isset($data['id_case']))
            $newdata['id_case'] = $data['id_case'];

        $day_of_week = date('N');

        if (!$data['due']) {
            switch ($data['id_taskdue']) {
                case '1':
                    $newdata['duedate'] = date("Y-m-d H:i:s");
                    break;
                case '2':
                    $newdata['duedate'] = date("Y-m-d H:i:s", time() + 86400);
                    break;
                case '3':
                    if ($day_of_week == '7') {
                        $newdata['duedate'] = date("Y-m-d H:i:s");
                    } else {
                        $newdata['duedate'] = date("Y-m-d H:i:s", time() + (7 - $day_of_week) * 86400);
                    }
                    break;
                case '4':
                    $newdata['duedate'] = date("Y-m-d H:i:s", time() + (14 - $day_of_week) * 86400);
                    break;
                case '5':

                    break;
                case '6':

                    break;
                default:
                    $newdata['duedate'] = date("Y-m-d H:i:s");
                    break;
            }
        } else {
            $newdata['duedate'] = date("Y-m-d H:i:s", strtotime($data['due']));
            $newdata['id_taskdue'] = 6;
        }

        $newdata['id_taskdue'] = $data['id_taskdue'];

        return $this->getTable()->insert($newdata);
    }

    /**
     * Delete task
     * @param type $id
     * @return type
     */
    public function delete($id) {
        return $this->getTable()->delete('id = ' . $id);
    }

    /**
     * Mark task as finished
     * @param type $id
     * @return type
     */
    public function finish($id) {
        $table = $this->getTable();
        $db = $table->getAdapter();

        $where = $db->quoteInto('id = ?', $id);

        return $table->update(array('is_finished' => 1), $where);
    }

    /**
     * Get total number of tasks
     * @return type
     */
    public function getTaskNumber() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('author = ? or owner = ?', $this->getCurrentUser()->id);
        $select = $db->select()->from('task', 'count(id)')->where($where);
        return $db->fetchOne($select);
    }

    /**
     * Get task data
     * @return boolean
     */
    public function getTask() {
        $table = $this->getTable();
        $db = $table->getAdapter();
        $where = $db->quoteInto('id = ?', $this->_id);
        $select = $db->select()->from('task')->where($where);

        $task = $db->query($select)->fetchAll();

        if ($task) {
            if (isset($task['0']['duedate'])) {
                $task['0']['duedate'] = date("Y-m-d", strtotime($task['0']['duedate']));
            }
            return $task['0'];
        } else {
            return false;
        }
    }

    /**
     * Edit task
     * @param type $data
     * @return type
     */
    public function edit($data) {
        $table = $this->getTable();
        $db = $table->getAdapter();

        $newdata['author'] = $this->getCurrentUser()->id;
        $newdata['public'] = $data['publ'];
        $newdata['owner'] = $data['owner'];
        //$newdata['duedate'] = date("Y-m-d H:i:s", strtotime($data['due']));
        $newdata['name'] = $data['name'];
        $newdata['id_category'] = $data['cat'];
        if (isset($data['id_person']) && $data['id_person'] <> 'null')
            $newdata['id_person'] = $data['id_person'];
        if (isset($data['id_company']) && $data['id_company'] <> 'null')
            $newdata['id_company'] = $data['id_company'];
        if (isset($data['id_case']) && $data['id_case'] <> 'null')
            $newdata['id_case'] = $data['id_case'];

        $day_of_week = date('N');

        if (!$data['due']) {
            switch ($data['id_taskdue']) {
                case '1':
                    $newdata['duedate'] = date("Y-m-d H:i:s");
                    break;
                case '2':
                    $newdata['duedate'] = date("Y-m-d H:i:s", time() + 86400);
                    break;
                case '3':
                    if ($day_of_week == '7') {
                        $newdata['duedate'] = date("Y-m-d H:i:s");
                    } else {
                        $newdata['duedate'] = date("Y-m-d H:i:s", time() + (7 - $day_of_week) * 86400);
                    }
                    break;
                case '4':
                    $newdata['duedate'] = date("Y-m-d H:i:s", time() + (14 - $day_of_week) * 86400);
                    break;
                case '5':

                    break;
                case '6':

                    break;
                default:
                    $newdata['duedate'] = date("Y-m-d H:i:s");
                    break;
            }
        } else {
            $newdata['duedate'] = date("Y-m-d H:i:s", strtotime($data['due']));
            $newdata['id_taskdue'] = 6;
        }

        $newdata['id_taskdue'] = $data['id_taskdue'];

        $where = $db->quoteInto('id = ?', $this->_id);

        try {
            return $table->update($newdata, $where);
        } catch (Exception $e) {
            Zend_Debug::dump($e->getMessage());
            die;
        }
    }

    /**
     * Filter task by specific criterias
     * @param type $data
     * @return type
     */
    public function filtertask($data) {
        //Zend_Debug::dump($data);die;

        $table = $this->getTable();
        $db = $table->getAdapter();

        $select = $db->select()
                ->from(array('t' => 'task'), 't.id')
                ->joinLeft(array('tc' => 'nmcl_taskcategory'), 'tc.id = t.id_category', '')
                ->joinLeft(array('p' => 'profile'), 'p.id = t.owner', '')
                ->where('is_finished = ?', 0)
                ->order('duedate desc');


        if (isset($data['type']) && $data['type'] <> 0) {
            switch ($data['type']) {
                case 1:
                    $whereType = $db->quoteInto('duedate > ?', date('Y-m-d'));
                    $whereType = $whereType . $db->quoteInto(' or id_taskdue = ?', 5);
                    break;
                case 2:
                    $whereType = $db->quoteInto('is_finished = ?', 1);
                    break;
                case 3:
                    $whereType = $db->quoteInto('duedate = ?', date('Y-m-d'));
                    break;
                default:
                    break;
            }
            $select->where($whereType);
        }

        if (isset($data['author']) && $data['author'] <> 0) {
            $whereAuthor = $db->quoteInto('author = ? or owner = ?', $data['author']);
            $select->where($whereAuthor);
        }

        if (isset($data['category']) && $data['category'] <> 0) {
            $whereCategory = $db->quoteInto('id_category = ?', $data['category']);
            $select->where($whereCategory);
        }

        return $db->fetchAll($select);
    }

    /**
     * Get task grouped by type, owner for dashboard
     * @return type
     */
    public function taskList() {
        $tasks = $this->fetchAll();

        $tasktoday = null;
        $taskOverdue = null;
        $taskFuture = null;

        $taskCategory = array();
        $taskCategoryArray = array();

        $owners = array();
        $ownersArray = array();

        foreach ($tasks as $task) {
            switch ($task) {
                case date('Y-m-d', strtotime($task['duedate'])) == date('Y-m-d') && $task['duedate']:
                    $tasktoday[] = $task;
                    break;
                case date('Y-m-d', strtotime($task['duedate'])) < date('Y-m-d') && $task['duedate']:
                    $taskOverdue[] = $task;
                    break;
                case date('Y-m-d', strtotime($task['duedate'])) > date('Y-m-d') && $task['duedate']:
                    $taskFuture[] = $task;
                default:
                    if (!isset($task['duedate']) && $task['id_taskdue'] == 5) {
                        $taskFuture[] = $task;
                    }
                    break;
            }

            if (!in_array($task['id_category'], $taskCategory)) {
                array_push($taskCategory, $task['id_category']);
                $tmpArr = array('id' => $task['id_category'], 'name' => $task['category']);
                array_push($taskCategoryArray, $tmpArr);
            }

            if (!in_array($task['owner'], $owners)) {
                array_push($owners, $task['owner']);
                $tmpArr = array('id' => $task['owner'], 'name' => $task['ownername']);
                array_push($ownersArray, $tmpArr);
            }
        }

        $user = $this->getCurrentUser();

        if (!in_array($user->id, $owners)) {
            array_push($ownersArray, array('id' => $user->id, 'name' => $user->firstname . ' ' . $user->lastname));
        }

        $tasks['taskOverdue'] = $taskOverdue;
        $tasks['taskFuture'] = $taskFuture;
        $tasks['tasktoday'] = $tasktoday;
        $tasks['ownersArray'] = $ownersArray;
        $tasks['taskCategoryArray'] = $taskCategoryArray;

        return $task;
    }

}
