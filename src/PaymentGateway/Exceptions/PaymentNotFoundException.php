<?php

namespace lukawar\BankKit\PaymentGateway\Exceptions;

/**
 * Thrown when a payment cannot be found in the gateway's system.
 */
class PaymentNotFoundException extends PaymentGatewayException {}
