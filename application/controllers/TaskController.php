<?php

class TaskController extends Zend_Controller_Action {

    public $taskModel;

    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/auth/logout/');
        }

        $this->taskModel = new Model_Task();
    }

    /**
     * List of all task filtered by user group, permission and grouped as task status - finished, overdue, futured or for today
     */
    public function indexAction() {
        $data = $this->getRequest()->getParams();
        if (isset($data['msg'])) {
            $this->view->msg = $data['msg'];
        }
        if (isset($data['error'])) {
            $this->view->error = $data['error'];
        }

        $tasks = $this->taskModel->taskList();

        $this->view->tasks = $tasks;
        $this->view->taskOverdue = $tasks['taskOverdue'];
        $this->view->taskFuture = $tasks['taskFuture'];
        $this->view->tasktoday = $tasks['tasktoday'];
        $this->view->owners = $tasks['ownersArray'];
        $this->view->categories = $tasks['taskCategoryArray'];
    }

    /**
     * Create new task
     */
    public function addAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        if (isset($data['name']) && isset($data['due']) && isset($data['cat']) && isset($data['publ'])) {
            $insert['id'] = $taskModel->add($data);
            $this->view->data = $insert;
        }
    }

    public function editAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();
        if (isset($data['id']) && isset($data['name']) && isset($data['due']) && isset($data['cat']) && isset($data['publ'])) {
            $this->taskModel->setId($data['id']);
            $this->view->data = $this->taskModel->edit($data);
        }
    }

    /**
     * Delete task
     */
    public function deleteAction() {
        $this->_helper->layout()->disableLayout();
        $id = $this->getRequest()->getParam('id');
        $this->view->num = $this->taskModel->delete($id);
    }

    /**
     * Create category task
     */
    public function addcategoryAction() {
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $data = $request->getParams();
        if (isset($data['cat']) && $data['cat']) {
            $cat = new Model_DbTable_Taskcategory();
            try {
                $data['id'] = $cat->insert(array('name' => $data['cat']));
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
     * Ajax call to return user tasks
     */
    public function gettaskAction() {
        $this->_helper->layout()->disableLayout();
        $id = $this->getRequest()->getParam('id');

        if ($id) {
            $this->taskModel->setId($id);
            $this->view->data = $this->taskModel->getTask();
        }
    }

    /**
     * Mark task as finished
     */
    public function finishAction() {
        $this->_helper->layout()->disableLayout();
        $id = $this->getRequest()->getParam('id');
        $this->view->num = $this->taskModel->finish($id);
    }

    /**
     * Filter task by speific criteria
     */
    public function filtertaskAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        unset($data['module']);
        unset($data['controller']);
        unset($data['action']);

        $this->view->filtertasks = $this->taskModel->filtertask($data);
    }

}
