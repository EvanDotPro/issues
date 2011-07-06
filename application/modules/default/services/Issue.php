<?php
class Default_Service_Issue extends Issues_ServiceAbstract 
{
    protected $_createForm;

    public function getCreateForm()
    {
        if (null === $this->_createForm) {
            $this->_createForm = new Default_Form_Issue_Create();
        }
        return $this->_createForm;
    }

    public function getIssueById($id)
    {
        return $this->_mapper->getIssueById($id);
    }

    public function getAllIssues()
    {
        return $this->_mapper->getAllIssues();
    }

    public function getIssuesByMilestone($milestone, $status = null)
    {
        return $this->_mapper->getIssuesByMilestone($milestone, $status);
    }

    public function filterIssues($status)
    {
        return $this->_mapper->filterIssues($status);
    }

    public function createFromForm(Default_Form_Issue_Create $form)
    {
        $acl = Zend_Registry::get('Default_DiContainer')->getAclService();
        if (!$acl->isAllowed('user', 'issue', 'create')) {
            return false;
        }

        $issue = new Default_Model_Issue();
        $issue->setTitle($form->getValue('title'))
            ->setDescription($form->getValue('description'))
            ->setStatus('open')
            ->setProject($form->getValue('project'))
            ->setCreatedBy(Zend_Auth::getInstance()->getIdentity());
        return $this->_mapper->insert($issue);
    }

    public function addLabelToIssue($issue, $label)
    {
        if (!($issue instanceof Default_Model_Issue)) {
            $issue = $this->_mapper->getIssueById($issue);
        }

        if (!$this->canLabelIssue($issue)) {
            return false;
        }

        if (!($label instanceof Default_Model_Label)) {
            $label = Zend_Registry::get('Default_DiContainer')->getLabelService()->getLabelById($label);
        }

        $this->_mapper->addLabelToIssue($issue, $label);
    }

    public function removeLabelFromIssue($issue, $label)
    {
        if (!($issue instanceof Default_Model_Issue)) {
            $issue = $this->_mapper->getIssueById($issue);
        }

        if (!$this->canLabelIssue($issue)) {
            return false;
        }

        if (!($label instanceof Default_Model_Label)) {
            $label = Zend_Registry::get('Default_DiContainer')->getLabelService()->getLabelById($label);
        }

        $this->_mapper->removeLabelFromIssue($issue, $label);
    }

    public function countIssuesByLabel(Default_Model_Label $label)
    {
        return $this->_mapper->countIssuesByLabel($label);
    }

    public function canLabelIssue($issue)
    {
        $acl = Zend_Registry::get('Default_DiContainer')->getAclService();
        if ($acl->isAllowed('user', 'issue', 'label-all')) {
            return true;
        }

        $identity = Zend_Registry::get('Default_DiContainer')->getUserService()
            ->getIdentity();

        if ($acl->isAllowed('user', 'issue', 'label-own')) {
            if ($identity->getUserId() == $issue->getCreatedBy()->getUserId()) {
                return true;
            }
        }

        return false;
    }
}

