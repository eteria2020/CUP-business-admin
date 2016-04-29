<?php
namespace Application\Controller;

use BusinessCore\Entity\Business;
use BusinessCore\Entity\Webuser;
use BusinessCore\Service\BusinessTripService;
use BusinessCore\Service\DatatableService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

class TimePackagesController extends AbstractActionController
{
    /**
     * @var Translator
     */
    private $translator;

    /**
     * @var AuthenticationService
     */
    private $authService;
    /**
     * @var DatatableService
     */
    private $datatableService;
    /**
     * @var BusinessTripService
     */
    private $businessTripService;

    /**
     * EmployeesController constructor.
     * @param Translator $translator
     * @param BusinessTripService $businessTripService
     * @param DatatableService $datatableService
     * @param AuthenticationService $authService
     */
    public function __construct(
        Translator $translator,
        BusinessTripService $businessTripService,
        DatatableService $datatableService,
        AuthenticationService $authService
    ){
        $this->translator = $translator;
        $this->datatableService = $datatableService;
        $this->authService = $authService;
        $this->businessTripService = $businessTripService;
    }

    public function timePackagesAction()
    {

        return new ViewModel([
            'business' => $this->getCurrentBusiness()
        ]);
    }
    public function buyAction()
    {
        return new ViewModel([
            'business' => $this->getCurrentBusiness()
        ]);
    }

    /**
     * @return Business
     */
    private function getCurrentBusiness()
    {
        $user = $this->authService->getIdentity();
        if ($user instanceof Webuser) {
            return $user->getBusiness();
        } else {
            throw new AuthenticationEventException("Errore di autenticazione");
        }
    }
}
