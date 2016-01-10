<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class Redirection
 * @package Sharoff\Zadarma\Response
 *
 * @method getSipId() string
 * @method getCondition() string
 * @method getDestination() string
 * @method getDestinationValue() string
 */
Class Redirection extends Response {

    protected $sip_id;
    protected $status;
    protected $condition;
    protected $destination;
    protected $destination_value;

    function __construct($sip_id, $status, $condition, $destination, $destination_value) {
        $this->sip_id             = $sip_id;
        $this->status            = $status;
        $this->condition         = $condition;
        $this->destination       = $destination;
        $this->destination_value = $destination_value;
    }

    function getStatus() {
        return ('on' === $this->status);
    }

}