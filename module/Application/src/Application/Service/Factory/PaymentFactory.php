<?php

namespace Application\Service\Factory;

use MvlabsPayments\Payments\FirstPayment;
use MvlabsPayments\Payments\Payment;
use MvlabsPayments\Payments\TokenPayment;

class PaymentFactory
{
    public static function payment(FirstPayment $firstPayment, TokenPayment $tokenPayment)
    {
        return new Payment($firstPayment, $tokenPayment);
    }
}
