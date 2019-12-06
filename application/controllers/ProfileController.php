<?php

class ProfileController extends Zend_Controller_Action {

    public $profileModel;

    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/auth/logout/');
        }

        $this->profileModel = new Model_Profile();
    }

    /**
     * List of all users
     */
    public function indexAction() {
        $data = $this->getRequest()->getParams();
        if (isset($data['msg'])) {
            $this->view->msg = $data['msg'];
        }

        if (isset($data['error'])) {
            $this->view->error = $data['error'];
        }

        $this->view->profiles = $this->profileModel->fetchAll();
    }

    /**
     * Make user admin or chenge it to regular user
     */
    public function makeadminAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        if ($data['admin'] == $this->profileModel->isAdmin()) {
            $this->view->data = $this->profileModel->makeadmin($data['id']);
        } else {
            $this->view->data = $this->profileModel->removeadmin($data['id']);
        }
    }

    /**
     * Edit specific user data
     */
    public function editAction() {
        $request = $this->getRequest();
        $data = $request->getPost();

        $form = new Form_Profile();

        if ($request->isPost()) {

            if ($form->isValid($data)) {
                //Zend_Debug::dump($data);die;
                try {
                    $this->profileModel->save($data);
                    $this->view->confirm = 'Profile saved sucessfully';
                } catch (Exception $e) {
                    $this->view->msg = $e->getMessage();
                }
            }
        }

        $user = $this->profileModel->getCurrentUser()->toArray();
        $form->populate($user);
        $this->view->form = $form;
    }

    /**
     * Create new user if request is post, otherwise show form
     */
    public function addAction() {
        $request = $this->getRequest();
        $form = new Form_Register();
        $data = $request->getPost();
        if ($this->getRequest()->isPost()) {
            if ($form->isValid($data)) {
                if ($this->profileModel->add($data)) {
                    $this->_forward('index', 'profile', 'default', array('confirm' => 'Successful registration.'));
                } else {
                    $this->_forward('index', 'profile', 'default', array('msg' => 'Registration failed.'));
                }
            }
        }
        $this->view->form = $form;
    }
}
