<?php

class IndexController extends Zend_Controller_Action {

    public function init() {
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_redirect('/auth/logout/');
        }
    }

    /**
     * This is a dashboard of app. When user come on this page after login need to see all activities based on persmission,
     * all changes in contacts, task and so on.
     */
    public function indexAction() {
        $noteModel = new Model_Activity();

        $this->view->data = $noteModel->fetchAll();
        $taskmodel = new Model_Task();
        $this->view->tasks = $taskmodel->fetchAll();

        $tasktoday = null;
        $taskOverdue = null;
        $taskFuture = null;

        foreach ($this->view->tasks as $task) {
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
        }

        $this->view->taskOverdue = $taskOverdue;
        $this->view->taskFuture = $taskFuture;
        $this->view->tasktoday = $tasktoday;
    }

    /**
     * Show email to import
     */
    public function getmailAction() {
        $this->_helper->layout()->disableLayout();
        $noteModel = new Model_Activity();
        $this->view->mails = $noteModel->getEmails();
    }

    /**
     * Autocomplete action for person autocomplete element
     */
    public function autocompleterAction() {
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $value = $request->getParam('query', '');
        $model = new Model_Person();
        $this->view->result = $model->searchValue($value);
    }

    /**
     * Get notes for project, person or company 
     */
    public function getnotesAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();

        switch ($data['location']) {
            case "projectrecord":
                $taskModel = new Model_Project();
                $taskModel->setId($data['id']);
                $this->view->data = $taskModel->getNotes($data['count']);
                break;
            case "personrecord":
                $taskModel = new Model_Person();
                $taskModel->setId($data['id']);
                $this->view->data = $taskModel->getNotes($data['count']);
                break;
            case "companyrecord":
                $taskModel = new Model_Company();
                $taskModel->setId($data['id']);
                $this->view->data = $taskModel->getNotes($data['count']);
                break;
            case "index":
                $taskModel = new Model_Activity();
                if (isset($data['author']) && $data['author']) {
                    $this->view->data = $taskModel->fetchAll($data['count'], 6, $data['author']);
                } else {
                    $this->view->data = $taskModel->fetchAll($data['count']);
                }
                break;
            default:
                $taskModel = new Model_Activity();
                $this->view->data = $taskModel->fetchAll($data['count']);
                break;
        }
    }

    /**
     * Create comment with File to person, company or project
     */
    public function commentAction() {
        $request = $this->getRequest();
        $data = $request->getParams();
        if (isset($data['id']) && $data['id']) {
            $modelActivity = new Model_Activity();
            $modelActivity->setId($data['id']);
            $idMain = $modelActivity->fetchMain();
            $modelActivity->setId($idMain);
            $commentForm = new Form_Comment();

            if ($request->isPost()) {
                $formData = $request->getPost();
                if ($commentForm->isValid($formData)) {
                    $formData['pid'] = $idMain;
                    $modelNote = new Model_Activity();
                    $id = $modelNote->add($formData);
                    $modelNote->saveFiles($id);
                }
            }
            $activity = $modelActivity->fetch();
            if ($activity["id_person"]) {
                $personModel = new Model_Person();
                $personModel->setId($activity["id_person"]);
                $activity['person'] = $personModel->fetch();
            }
            if ($activity["id_company"]) {
                $companyModel = new Model_Company();
                $companyModel->setId($activity["id_company"]);
                $activity['company'] = $companyModel->fetch();
            }
            $caseModel = new Model_Project();
            if ($activity["id_case"]) {
                $caseModel->setId($activity["id_case"]);
                $activity['case'] = $caseModel->fetch()->toArray();
            }
            $this->view->projects = $caseModel->fetchAll();

            $activity['comments'] = $modelActivity->fetchComments();
            //Zend_Debug::dump($activity);
            $this->view->activity = $activity;

            unset($activity['note']);
            $commentForm->populate($activity);
            $this->view->cform = $commentForm;
        } else {
            $this->_redirect('/');
        }
    }

    /**
     * Edit note
     */
    public function editnoteAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();
        if (isset($data['id'])) {
            $taskModel = new Model_Activity();
            $data['result'] = $taskModel->updateNote($data);
        } else {
            $data['result'] = 0;
        }
        $this->view->data = $data;
    }

    /**
     * Delete note
     */
    public function deletenoteAction() {
        $this->_helper->layout()->disableLayout();
        $data = $this->getRequest()->getParams();
        if (isset($data['id'])) {
            $taskModel = new Model_Activity();
            $this->view->data = $taskModel->deleteNote($data['id']);
        }
    }

    /**
     * Edit not from fancybox dialog box
     */
    public function editfancynoteAction() {
        $this->_helper->layout()->disableLayout();
        $request = $this->getRequest();
        $data = $request->getParams();
        $taskModel = new Model_Activity();
        $id_note = $data['id'];
        $taskModel->setId($id_note);
        $this->view->note = $taskModel->fetch();
        if ($request->isPost()) {

            $update = $taskModel->edit($data);
            $taskModel->saveFiles($id_note);
            $this->_redirect('/');
        }

        $caseModel = new Model_Project();
        $groupModel = new Model_Group();
        $profileModel = new Model_Profile();

        $this->view->profiles = $profileModel->fetchAll();
        $this->view->groups = $groupModel->fetchAll();
        $this->view->projects = $caseModel->fetchAll();
    }

    /**
     * Search notes by part of content
     */
    public function searchnotesAction() {
        $data = $this->getRequest()->getParams();

        if (isset($data['search_word'])) {
            $modelActivity = new Model_Activity();
            $this->view->result = $modelActivity->searchNotes($data['search_word']);
        }
    }

}
