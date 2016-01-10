<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class Sip
 * @package Sharoff\Zadarma\Response
 *
 * @method getId() string
 * @method getDisplayName() string
 * @method getLines() string
 */
Class Sip extends Response {

    protected $id;
    protected $display_name;
    protected $lines;

    function __construct($id, $display_name, $lines) {
        $this->id           = $id;
        $this->display_name = $display_name;
        $this->lines        = $lines;
    }

    function __toString() {
        return (string)$this->display_name;
    }

}