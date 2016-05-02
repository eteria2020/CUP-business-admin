<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TimePackagesControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface|ServiceLocatorAwareInterface $serviceLocator
     * @return TripsController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();
        $authService = $sl->get('zfcuser_auth_service');
        $datatableService = $sl->get('BusinessCore\Service\DatatableService');
        $businessTimePackageService = $sl->get('BusinessCore\Service\BusinessTimePackageService');
        $translator = $sl->get('translator');

        return new TimePackagesController($translator, $businessTimePackageService, $datatableService, $authService);
    }

}