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
        $sl = $serviceLocator->getServiceLocator();
        $businessService = $sl->get('BusinessCore\Service\BusinessService');
        $businessTripService = $sl->get('BusinessCore\Service\BusinessTripService');
        $datatableService = $sl->get('BusinessCore\Service\DatatableService');
        $authService = $sl->get('zfcuser_auth_service');
        $translator = $sl->get('translator');

        return new TripsController($translator, $businessService, $businessTripService, $datatableService, $authService);
    }

}