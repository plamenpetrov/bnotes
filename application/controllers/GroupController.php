<?php

class GroupController extends Zend_Controller_Action {

    public $groupModel;

    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/auth/logout/');
        }

        $this->groupModel = new Model_Group();
    }

    /**
     * List of all groups
     */
    public function indexAction() {
        $data = $this->getRequest()->getParams();
        if (isset($data['msg'])) {
            $this->view->msg = $data['msg'];
        }

        if (isset($data['error'])) {
            $this->view->error = $data['error'];
        }

        $this->view->groups = $this->groupModel->fetchAll();
    }

    /**
     * Create new group. If post request is presented the store group else show form
     */
    public function addAction() {
        $request = $this->getRequest();
        $form = new Form_Group();
        $data = $request->getPost();
        if ($this->getRequest()->isPost()) {
            if ($this->groupModel->add($data)) {
                $this->_forward('index', 'group', 'default', array('confirm' => 'Group added'));
            } else {
                $this->_forward('index', 'group', 'default', array('msg' => 'Group not added'));
            }
        }
        $this->view->form = $form;
    }

    /**
     * Edit group.
     */
    public function editAction() {
        $request = $this->getRequest();
        $form = new Form_Group();
        $data = $request->getParams();
        if (isset($data["id"])) {
            $this->groupModel->setId($data["id"]);
            if ($request->isPost()) {
                if ($this->groupModel->edit($data)) {
                    $this->_forward('index', 'group', 'default', array('confirm' => 'Group added'));
                } else {
                    $this->_forward('index', 'group', 'default', array('msg' => 'Group not added'));
                }
            }

            $values = $this->groupModel->fetch();
            $form->populate($values);
            $form->people->setValue(array_keys($values["people"]));
            $this->view->form = $form;
        }
    }

    /**
     * Delete specific group
     */
    public function deleteAction() {
        $request = $this->getRequest();
        $data = $request->getParams();
        if (isset($data["id"])) {
            $this->groupModel->setId($data["id"]);
            if ($this->groupModel->delete()) {
                $this->_forward('index', 'group', 'default', array('confirm' => 'Group deleted'));
            } else {
                $this->_forward('index', 'group', 'default', array('msg' => 'Group not deleted'));
            }
        }
        $this->_forward('index', 'group', 'default', array('msg' => 'Group not deleted'));
    }

}
