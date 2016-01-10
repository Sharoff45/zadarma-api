<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class SipCaller
 * @package Sharoff\Zadarma\Response
 *
 * @method getSipId() string
 * @method getCallerId() string
 */
Class SipCaller extends Response {

    protected $sip_id;
    protected $caller_id;

    function __construct($sip_id, $caller_id) {
        $this->sip_id    = $sip_id;
        $this->caller_id = $caller_id;
    }

    function __toString() {
        return (string)$this->caller_id;
    }

}