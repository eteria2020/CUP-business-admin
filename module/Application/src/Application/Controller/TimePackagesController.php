<?php
namespace Application\Controller;

use BusinessCore\Entity\Business;
use BusinessCore\Entity\Webuser;
use BusinessCore\Exception\PackageNotBuyableException;
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
        /** @var Business $business */
        $business = $this->identity()->getBusiness();
        $timePackageId = $this->params()->fromRoute('id', 0);
        if ($timePackageId != 0) {
            $timePackage = $this->businessTimePackageService->findTimePackageById($timePackageId);
            if ($business->canBuyTimePackage($timePackage)) {
                if ($business->payWithCreditCard()) {
                    if ($business->hasActiveContract()) {
                        $timePackagePayment = $this->businessTimePackageService->createPackagePayment($business, $timePackage);
                        $this->businessTimePackageService->payTimePackage($business, $timePackagePayment);
                        $this->flashMessenger()->addSuccessMessage($this->translatorPlugin()->translate("Acquisto pacchetto minuti preso in carico"));
                    } else {
                        $this->flashMessenger()->addErrorMessage($this->translatorPlugin()->translate("Prima di poter acquistare pacchetti minuti con carta di credito devi pagare l'iscrizione dalla tua dashboard"));
                    }
                } else {
                    $this->flashMessenger()->addWarningMessage($this->translatorPlugin()->translate("Pacchetto minuti aggiunto alla lista pagamenti, potrai utilizzarlo solamente quando il suo pagamento verrà confermato da Sharengo"));
                }
            } else {
                $this->flashMessenger()->addErrorMessage($this->translatorPlugin()->translate("Si é verificato un errore"));
            }
            return $this->redirect()->toRoute('time-packages');
        }

        return new ViewModel([
            'business' => $business,
            'packages' => $this->businessTimePackageService->findBuyablePackages($business)
        ]);
    }
}
