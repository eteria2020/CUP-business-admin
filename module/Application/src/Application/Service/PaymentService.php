<?php

namespace Application\Service;

use BusinessCore\Entity\BusinessContract;
use BusinessCore\Entity\Transaction;
use MvlabsPayments\PaymentRequest\PaymentRequest;
use MvlabsPayments\Payments\Payment;
use Zend\EventManager\EventManager;

class PaymentService
{

    private $eventManager;
    /**
     * @var Payment
     */
    private $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
    }

    public function pay(PaymentRequest $request)
    {
        $this->payment->pay($request);


        $this->eventManager = new EventManager('Transaction');
        $transaction = new Transaction($request->amount()->cents(), $request->amount()->currency());

        if ($request->isFirstPayment()) {
            $contract = new BusinessContract($request->customer());
            $this->eventManager->trigger('contractInitiated', $this, [
                'contract' => $contract
            ]);

            //do things
            $contract->setPan("66666666");
            $contract->setPanExpiry("202007");

            $this->eventManager->trigger('contractFinalized', $this, [
                'contract' => $contract
            ]);
        }
        $this->eventManager->trigger('paymentInitiated', $this, [
            'transaction' => $transaction,
            'requestPayment' => $request
        ]);

        //try to pay transaction (possibly async)

        $this->eventManager->trigger('paymentOutcome', $this, [
            'transaction' => $transaction,
            'outcome' => "OK"
        ]);
    }
}
