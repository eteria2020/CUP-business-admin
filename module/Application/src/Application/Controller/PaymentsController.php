<?php
namespace Application\Controller;

use Application\Form\GroupForm;
use BusinessCore\Entity\Group;
use BusinessCore\Entity\Webuser;
use BusinessCore\Service\GroupService;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

class PaymentsController extends AbstractActionController
{

    public function paymentsAction()
    {
        return new ViewModel([
            'business' => $this->identity()->getBusiness()
        ]);
    }

    public function creditCardAction()
    {
        return new ViewModel([
            'business' => $this->identity()->getBusiness()
        ]);
    }
}
