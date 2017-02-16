<?php

namespace Application\Controller;

use BusinessCore\Entity\Business;
use BusinessCore\Entity\BusinessFare;
use Doctrine\ORM\EntityNotFoundException;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class FaresController extends AbstractActionController
{
    public function faresAction()
    {
        /** @var Business $business */
        $business = $this->identity()->getBusiness();
        $businessFare = $business->getActiveBusinessFare();

        return new ViewModel([
            'business' => $business,
            'businessFare' => $businessFare
        ]);
    }
}
