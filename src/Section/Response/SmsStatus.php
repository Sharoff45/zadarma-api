<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class SmsStatus
 * @package Sharoff\Zadarma\Response
 *
 * @method getMessages() string
 * @method getCost() string
 * @method getCurrency() string
 */
Class SmsStatus extends Response {

    protected $messages;
    protected $cost;
    protected $currency;

    function __construct($messages, $cost, $currency) {
        $this->messages = (int)$messages;
        $this->cost     = (float)$cost;
        $this->currency = $currency;
    }

    function __toString() {
        return (string)$this->cost;
    }

}