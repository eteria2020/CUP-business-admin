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
     * @return TimePackagesController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $authService = $sharedServiceManager->get('zfcuser_auth_service');
        $businessTimePackageService = $sharedServiceManager->get('BusinessCore\Service\BusinessTimePackageService');

        return new TimePackagesController($businessTimePackageService, $authService);
    }
}
