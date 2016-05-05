<?php
namespace Application\Controller;

use BusinessCore\Entity\BusinessTrip;
use BusinessCore\Service\BusinessTripService;
use BusinessCore\Service\DatatableService;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;

class TripsController extends AbstractActionController
{
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
     * @param BusinessTripService $businessTripService
     * @param DatatableService $datatableService
     */
    public function __construct(
        BusinessTripService $businessTripService,
        DatatableService $datatableService
    ) {
        $this->businessTripService = $businessTripService;
        $this->datatableService = $datatableService;
    }

    public function tripsAction()
    {
        return new ViewModel([
            'business' => $this->identity()->getBusiness()
        ]);
    }

    public function datatableAction()
    {
        $filters = $this->params()->fromPost();
        $business = $this->identity()->getBusiness();
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
