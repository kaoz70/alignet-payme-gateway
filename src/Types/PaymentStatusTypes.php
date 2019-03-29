<?php

namespace Kaoz70\PayMe\Types;


abstract class PaymentStatusTypes
{
    public static $CANCELED = 'canceled';
    public static $DENIED = 'denied';
    public static $REJECTED = 'rejected';
    public static $INVALID_VERIFICATION = 'invalid_verification';
}
