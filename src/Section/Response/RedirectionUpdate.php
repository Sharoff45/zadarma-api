<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class RedirectionUpdate
 * @package Sharoff\Zadarma\Response
 *
 * @method getSipId() string
 * @method getDestination() string
 */
Class RedirectionUpdate extends Response {

    protected $sip_id;
    protected $destination;

    function __construct($sip_id, $destination) {
        $this->sip_id      = $sip_id;
        $this->destination = $destination;
    }

}