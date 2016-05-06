<?php
namespace Application\Controller;

use BusinessCore\Entity\Webuser;
use BusinessCore\Service\BusinessTimePackageService;

use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

class TimePackagesController extends AbstractActionController
{
    /**
     * @var BusinessTimePackageService
     */
    private $businessTimePackageService;

    /**
     * EmployeesController constructor.
     * @param BusinessTimePackageService $businessTimePackageService
     */
    public function __construct(BusinessTimePackageService $businessTimePackageService)
    {
        $this->businessTimePackageService = $businessTimePackageService;
    }

    public function timePackagesAction()
    {
        return new ViewModel([
            'business' => $this->identity()->getBusiness()
        ]);
    }
    public function buyAction()
    {
        $business = $this->identity()->getBusiness();
        $timePackageId = $this->params()->fromRoute('id', 0);
        if ($timePackageId != 0) {
            $this->businessTimePackageService->buyTimePackage($business, $timePackageId);
            $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate("Pacchetto minuti acquistato con sucesso"));

            return $this->redirect()->toRoute('time-packages');
        }

        return new ViewModel([
            'business' => $business,
            'packages' => $this->businessTimePackageService->findBuyablePackages($business)
        ]);
    }
}
