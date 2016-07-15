<?php
namespace Application\Controller;

use BusinessCore\Entity\Business;
use BusinessCore\Service\BusinessTimePackageService;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

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
        /** @var Business $business */
        $business = $this->identity()->getBusiness();
        return new ViewModel([
            'businessTimePackages' => $business->getBusinessTimePackages()
        ]);
    }
    public function buyAction()
    {
        $business = $this->identity()->getBusiness();
        $timePackageId = $this->params()->fromRoute('id', 0);
        if ($timePackageId != 0) {
            $timePackage = $this->businessTimePackageService->findTimePackageById($timePackageId);
            $this->businessTimePackageService->buyTimePackage($business, $timePackage);
            $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate("Pacchetto minuti acquistato con sucesso"));

            return $this->redirect()->toRoute('time-packages');
        }

        return new ViewModel([
            'business' => $business,
            'packages' => $this->businessTimePackageService->findBuyablePackages($business)
        ]);
    }
}
