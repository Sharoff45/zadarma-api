<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class RedirectionStatus
 * @package Sharoff\Zadarma\Response
 *
 * @method getSipId() string
 */
Class RedirectionStatus extends Response {

    protected $sip_id;
    protected $status;

    function __construct($sip_id, $status) {
        $this->sip_id = $sip_id;
        $this->status = $status;
    }

    function getStatus() {
        return ('on' === $this->status);
    }

}