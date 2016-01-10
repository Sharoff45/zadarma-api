<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class Price
 * @package Sharoff\Zadarma\Response
 *
 * @method getPrefix() string
 * @method getDescription() string
 * @method getPrice() string
 * @method getCurrency() string
 */
Class Price extends Response {

    protected $prefix;
    protected $description;
    protected $price;
    protected $currency;

    function __construct($prefix, $description, $price, $currency) {
        $this->prefix      = $prefix;
        $this->description = $description;
        $this->price       = $price;
        $this->currency    = $currency;
    }

    function __toString() {
        return (string)$this->price;
    }

}