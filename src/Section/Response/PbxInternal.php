<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class PbxInternal
 * @package Sharoff\Zadarma\Response
 *
 * @method getId() string
 * @method getNumbers() string
 */
Class PbxInternal extends Response {

    protected $id;
    protected $numbers;

    function __construct($id, $numbers = []) {
        $this->id      = $id;
        $this->numbers = array_map('intval', (array)$numbers);
    }

}