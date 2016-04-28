<?php

namespace Application\Controller;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class InvoicesControllerFactory implements FactoryInterface
{
    /**
     * Create service
     *
     * @param ServiceLocatorInterface|ServiceLocatorAwareInterface $serviceLocator
     * @return InvoicesController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $sl = $serviceLocator->getServiceLocator();
        $businessInvoiceService = $sl->get('BusinessCore\Service\BusinessInvoiceService');
        $invoicePdfService = $sl->get('BusinessCore\Service\InvoicePdfService');
        $datatableService = $sl->get('BusinessCore\Service\DatatableService');
        $authService = $sl->get('zfcuser_auth_service');
        $translator = $sl->get('translator');

        return new InvoicesController(
            $translator,
            $businessInvoiceService,
            $invoicePdfService,
            $datatableService,
            $authService
        );
    }

}