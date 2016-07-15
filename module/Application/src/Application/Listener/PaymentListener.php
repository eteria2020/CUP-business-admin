<?php

namespace Application\Listener;

use BusinessCore\Service\ContractService;
use BusinessCore\Service\TransactionService;
use Zend\EventManager\EventInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\SharedListenerAggregateInterface;

class PaymentListener implements SharedListenerAggregateInterface
{
    /**
     * @var array
     */
    private $listeners = [];
    /**
     * @var TransactionService
     */
    private $transactionService;
    /**
     * @var ContractService
     */
    private $contractService;

    public function __construct(
        TransactionService $transactionService,
        ContractService $contractService
    ) {
        $this->transactionService = $transactionService;
        $this->contractService = $contractService;
    }

    public function attachShared(SharedEventManagerInterface $events)
    {
        $this->listeners[] = $events->attach(
            'Transaction',
            'paymentInitiated',
            [$this, 'paymentInitiated']
        );

        $this->listeners[] = $events->attach(
            'Transaction',
            'paymentOutcome',
            [$this, 'paymentOutcome']
        );

        $this->listeners[] = $events->attach(
            'Transaction',
            'contractInitiated',
            [$this, 'contractInitiated']
        );

        $this->listeners[] = $events->attach(
            'Transaction',
            'contractFinalized',
            [$this, 'contractFinalized']
        );
    }

    public function detachShared(SharedEventManagerInterface $events)
    {
        foreach ($this->listeners as $index => $callback) {
            if ($events->detach($index, $callback)) {
                unset($this->listeners[$index]);
            }
        }
    }

    public function paymentInitiated(EventInterface $e)
    {
        $params = $e->getParams();
        $requestPayment = $params['requestPayment'];
        $transaction = $params['transaction'];
        $payments = $requestPayment->getPayments();
        $this->transactionService->assignTransactionToPayments($payments, $transaction);
    }

    public function paymentOutcome(EventInterface $e)
    {
        $params = $e->getParams();
        $transaction = $params['transaction'];
        $outcome = $params['outcome'];
        $this->transactionService->transactionCompleted($outcome, $transaction);
    }

    public function contractInitiated(EventInterface $e)
    {
        $params = $e->getParams();
        $contract = $params['contract'];
        $this->contractService->createContract($contract);
    }

    public function contractFinalized(EventInterface $e)
    {
        $params = $e->getParams();
        $contract = $params['contract'];
        $this->contractService->createContract($contract);
    }
}
