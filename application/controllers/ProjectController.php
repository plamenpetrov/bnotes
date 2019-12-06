<?php

class ProjectController extends Zend_Controller_Action {

    public $projectModel;

    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/auth/logout/');
        }

        $this->projectModel = new Model_Project();
    }

    /**
     * List all projects
     */
    public function indexAction() {
        $data = $this->getRequest()->getParams();
        if (isset($data['msg'])) {
            $this->view->msg = $data['msg'];
        }

        if (isset($data['error'])) {
            $this->view->error = $data['error'];
        }

        $this->view->projects = $this->projectModel->fetchAll();
    }

    /**
     * Create new project
     */
    public function createAction() {
        $request = $this->getRequest();
        $form = new Form_Project();
        $data = $request->getPost();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($data)) {
                if ($this->projectModel->add($data)) {
                    $this->_forward('index', 'project', 'default', array('msg' => 'Project added'));
                } else {
                    $this->_forward('index', 'project', 'default', array('error' => 'Project not added'));
                }
            }
        }
        $this->view->form = $form;
    }

    /**
     * Edit specific project
     */
    public function editAction() {
        $form = new Form_Project();
        $data = $this->getRequest()->getParams();
        $this->projectModel->setId($data["id"]);
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($data)) {
                if ($this->projectModel->update($data)) {
                    $this->_forward('index', 'project', 'default', array('msg' => 'Project saved'));
                } else {
                    $this->_forward('index', 'project', 'default', array('error' => 'Project not saved'));
                }
            }
        }
        $project = $this->projectModel->fetch()->toArray();
        $form->populate($project);
        $this->view->form = $form;
    }

    /**
     * Remove project
     */
    public function deleteAction() {
        $id = $this->getRequest()->getParam('id');
        $this->projectModel->deleteNotes($id);
        if ($this->projectModel->delete($id)) {
            $this->_forward('index', 'project', 'default', array('msg' => 'Project deleted'));
        } else {
            $this->_forward('index', 'project', 'default', array('error' => 'Project not deleted'));
        }
    }

    /**
     * Display all specific prject data
     */
    public function recordAction() {
        $request = $this->getRequest();
        $data = $request->getParams();

        if (isset($data['note'])) {
            $taskModel = new Model_Activity();
            $id_note = $taskModel->add($data);
            $taskModel->saveFiles($id_note);
        }

        if (isset($data["id"])) {
            $this->projectModel->setId($data["id"]);
            $this->view->project = $this->projectModel->fetch()->toArray();
            $this->view->notes = $this->projectModel->getNotes();

            $groupModel = new Model_Group();
            $progileModel = new Model_Profile();

            $this->view->profiles = $progileModel->fetchAll();
            $this->view->groups = $groupModel->fetchAll();
        }
    }

}
