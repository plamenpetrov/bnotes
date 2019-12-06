<?php

class UserController extends Zend_Controller_Action {

    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/auth/logout/');
        }
    }

    /**
     * Edit current user data - profile data
     */
    public function editAction() {
        $data = $this->getRequest()->getPost();

        $form = new Form_User();
        $userModel = new Model_User();

        if ($request->isPost()) {

            if ($form->isValid($data)) {
                //Zend_Debug::dump($data);die;
                try {
                    if ($userModel->save($data)) {
                        $this->view->confirm = 'Password changed sucessfully';
                    } else {
                        $this->view->msg = 'Wrong password';
                    }
                } catch (Exception $e) {
                    $this->view->msg = $e->getMessage();
                }
            }
        }
        $user = $userModel->getCurrentUser()->toArray();
        $form->populate($user);
        $this->view->form = $form;
    }

}
