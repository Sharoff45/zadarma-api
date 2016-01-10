<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class Balance
 * @package Sharoff\Zadarma\Response
 *
 * @method getBalance() string
 * @method getCurrency() string
 */
Class Balance extends Response {

    protected $balance;
    protected $currency;

    function __construct($balance, $currency) {
        $this->balance  = $balance;
        $this->currency = $currency;
    }

    function __toString() {
        return (string)$this->balance;
    }

}