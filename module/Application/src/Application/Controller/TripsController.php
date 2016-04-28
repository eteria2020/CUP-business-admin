<?php
namespace Application\Controller;

use BusinessCore\Entity\Business;
use BusinessCore\Entity\BusinessTrip;
use BusinessCore\Entity\Webuser;
use BusinessCore\Service\BusinessService;
use BusinessCore\Service\BusinessTripService;
use BusinessCore\Service\DatatableService;
use Zend\Authentication\AuthenticationService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\Mvc\I18n\Translator;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use ZfcUser\Exception\AuthenticationEventException;

class TripsController extends AbstractActionController
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

    public function tripsAction()
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

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $business = $this->getCurrentBusiness();
        $searchCriteria = $this->datatableService->getSearchCriteria($filters);
        $businessTrips = $this->businessTripService->searchTripsByBusiness($business, $searchCriteria);
        $dataDataTable = $this->mapBusinessTripsToDatatable($businessTrips);
        $totalTrips = $this->businessTripService->getTotalTripsByBusiness($business);

        return new JsonModel([
            'draw'            => $this->params()->fromQuery('sEcho', 0),
            'recordsTotal'    => $totalTrips,
            'recordsFiltered' => count($dataDataTable),
            'data'            => $dataDataTable
        ]);
    }

    private function mapBusinessTripsToDatatable(array $businessTrips)
    {
        return array_map(function (BusinessTrip $businessTrip) {
            $trip = $businessTrip->getTrip();
            $employee = $trip->getEmployee();
            $groupName = is_null($businessTrip->getGroup()) ? '-' : $businessTrip->getGroup()->getName();
            return [
                'e' => [
                    'name' => $employee->getId(),
                    'surname' => $employee->getSurname(),
                ],
                'g' => [
                    'name' => $groupName,
                ],
                't' => [
                    'carPlate' => $trip->getCarPlate(),
                    'distance' => $trip->getKmEnd() - $trip->getKmBeginning(),
                    'duration' => date_diff($trip->getTimestampEnd(), $trip->getTimestampBeginning())->format("%i min."),
                    'parkSeconds' => $trip->getParkSeconds(),
                    'timestampBeginning' => $trip->getTimestampBeginning()->format('d-m-Y H:i:s'),
                ],
            ];
        }, $businessTrips);
    }
}
