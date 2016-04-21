<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class EmployeesControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface|ServiceLocatorAwareInterface $serviceLocator
     * @return EmployeesController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();
        $businessService = $sl->get('BusinessCore\Service\BusinessService');
        $authService = $sl->get('zfcuser_auth_service');
        $translator = $sl->get('translator');

        return new EmployeesController($translator, $businessService, $authService);
    }

}