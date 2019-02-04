<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class IndexControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface|ServiceLocatorAwareInterface $serviceLocator
     * @return EmployeesController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $languageService = $sharedServiceManager->get('LanguageService');

        $businessService = $sharedServiceManager->get('BusinessCore\Service\BusinessService');
        $translator = $languageService->getTranslator();

        return new IndexController($translator, $businessService);
    }
}
