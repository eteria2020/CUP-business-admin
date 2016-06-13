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
        $sharedServiceManager = $serviceLocator->getServiceLocator();
        $businessInvoiceService = $sharedServiceManager->get('BusinessCore\Service\BusinessInvoiceService');
        $pdfService = $sharedServiceManager->get('BusinessCore\Service\PdfService');
        $datatableService = $sharedServiceManager->get('BusinessCore\Service\DatatableService');

        return new InvoicesController(
            $businessInvoiceService,
            $pdfService,
            $datatableService
        );
    }
}
