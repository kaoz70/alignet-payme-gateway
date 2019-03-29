<?php

namespace Kaoz70\PayMe\Exceptions;


use Throwable;

class PaymentStatusException extends \Exception
{

    private $status;

    public function __construct($message, $status, $code = 0, Throwable $previous = null) {
        parent::__construct($message, $code, $previous);
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

}
