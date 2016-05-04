<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class TripsControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface|ServiceLocatorAwareInterface $serviceLocator
     * @return TripsController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $authService = $sharedServiceManager->get('zfcuser_auth_service');
        $datatableService = $sharedServiceManager->get('BusinessCore\Service\DatatableService');
        $businessTripService = $sharedServiceManager->get('BusinessCore\Service\BusinessTripService');
        $translator = $sharedServiceManager->get('translator');

        return new TripsController($translator, $businessTripService, $datatableService, $authService);
    }
}
