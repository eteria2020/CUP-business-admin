<?php
namespace Application\Controller;

use BusinessCore\Entity\Business;
use BusinessCore\Entity\Webuser;
use BusinessCore\Service\BusinessTimePackageService;
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
     * @var BusinessTimePackageService
     */
    private $businessTimePackageService;

    /**
     * EmployeesController constructor.
     * @param Translator $translator
     * @param BusinessTimePackageService $businessTimePackageService
     * @param DatatableService $datatableService
     * @param AuthenticationService $authService
     */
    public function __construct(
        Translator $translator,
        BusinessTimePackageService $businessTimePackageService,
        DatatableService $datatableService,
        AuthenticationService $authService
    ) {
        $this->translator = $translator;
        $this->datatableService = $datatableService;
        $this->authService = $authService;
        $this->businessTimePackageService = $businessTimePackageService;
    }

    public function timePackagesAction()
    {
        return new ViewModel([
            'business' => $this->getCurrentBusiness()
        ]);
    }
    public function buyAction()
    {
        $timePackageId = $this->params()->fromRoute('id', 0);
        if ($timePackageId != 0) {

            $this->businessTimePackageService->buyTimePackage($this->getCurrentBusiness(), $timePackageId);
            $this->flashMessenger()->addSuccessMessage($this->translator->translate("Pacchetto minuti acquistato con sucesso"));

            return $this->redirect()->toRoute('time-packages');
        }

        return new ViewModel([
            'business' => $this->getCurrentBusiness(),
            'packages' => $this->businessTimePackageService->getBuyablePackages()
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
