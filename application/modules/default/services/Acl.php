<?php
class Default_Service_Acl extends Issues_ServiceAbstract
{
    /**
     * _roleService
     * 
     * @var Default_Service_Role
     */
    protected $_roleService;

    /**
     * _acl 
     * 
     * @var Zend_Acl
     */
    protected $_acl;

    /**
     * __construct 
     * 
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->_mapper = Zend_Registry::get('Default_DiContainer')->getAclMapper();
        $this->_roleService = Zend_Registry::get('Default_DiContainer')->getRoleService();

        $this->_acl = new Zend_Acl();

        $this->_setupRoles();
        $this->_setupResources();
        $this->_loadAclRecords();
    }

    /**
     * _setupRoles 
     * 
     * @return void
     */
    protected function _setupRoles()
    {
        $this->_roles = $this->_roleService->getAllRoles();
        foreach ($this->_roles as $i) {
            $this->_acl->addRole($i);
        }
        $currentRoles = Zend_Registry::get('Default_DiContainer')->getUserService()
            ->getIdentity()->getRoles();
        
        $this->_acl->addRole(new Zend_Acl_Role('user'), $currentRoles);
    }

    /**
     * _setupResources 
     * 
     * @return void
     */
    protected function _setupResources()
    {
        $this->_acl->addResource(new Zend_Acl_Resource('issue'));
        $this->_acl->addResource(new Zend_Acl_Resource('comment'));
        $this->_acl->addResource(new Zend_Acl_Resource('label'));
        $this->_acl->addResource(new Zend_Acl_Resource('milestone'));
        $this->_acl->addResource(new Zend_Acl_Resource('project'));
        $this->_acl->addResource(new Zend_Acl_Resource('role'));
        $this->_acl->addResource(new Zend_Acl_Resource('user'));
    }

    /**
     * _loadAclRecords 
     * 
     * @return void
     */
    protected function _loadAclRecords()
    {
        $records = $this->_mapper->getAllRecords();
        foreach ($records as $i) {
            if ($i->getType() == 'allow') {
                $this->_acl->allow($i->getRoleId(), $i->getResource(), $i->getAction());
            } else {
                $this->_acl->deny($i->getRoleId(), $i->getResource(), $i->getAction());
            }
        }
    }

    /**
     * isAllowed 
     * 
     * @param mixed $role 
     * @param mixed $resource 
     * @param mixed $action 
     * @return void
     */
    public function isAllowed($role, $resource, $action)
    {
        return $this->_acl->isAllowed($role, $resource, $action);
    }
}
