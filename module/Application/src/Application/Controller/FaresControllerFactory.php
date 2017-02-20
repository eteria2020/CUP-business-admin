<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class FaresControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface|ServiceLocatorAwareInterface $serviceLocator
     * @return FaresController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        return new FaresController();
    }
}
