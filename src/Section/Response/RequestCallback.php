<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class RequestCallback
 * @package Sharoff\Zadarma\Response
 *
 * @method getFrom() string
 * @method getTo() string
 * @method getTime() timestamp
 */
Class RequestCallback extends Response {

    protected $from;
    protected $to;
    protected $time;

    function __construct($from, $to, $time) {
        $this->from = $from;
        $this->to   = $to;
        $this->time = $time;
    }

}