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
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $groupService = $sharedServiceManager->get('BusinessCore\Service\GroupService');
        $groupForm = $serviceLocator->getServiceLocator()->get('Application\Form\GroupForm');

        return new GroupsController($groupService, $groupForm);
    }
}
