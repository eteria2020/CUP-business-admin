<?php
namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class GroupsControllerFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface|ServiceLocatorAwareInterface $serviceLocator
     * @return GroupsController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();
        $businessService = $sl->get('BusinessCore\Service\BusinessService');
        $groupService = $sl->get('BusinessCore\Service\GroupService');
        $authService = $sl->get('zfcuser_auth_service');
        $translator = $sl->get('translator');

        $groupForm = $serviceLocator->getServiceLocator()->get('Application\Form\GroupForm');

        return new GroupsController($translator, $businessService, $groupService, $authService, $groupForm);
    }
}