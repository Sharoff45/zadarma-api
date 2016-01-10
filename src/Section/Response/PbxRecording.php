<?php
namespace Sharoff\Zadarma\Response;

/**
 * Class PbxRecording
 * @package Sharoff\Zadarma\Response
 *
 * @method getId() string
 * @method getEmails() @returns array
 */
Class PbxRecording extends Response {

    protected $id;
    protected $is_recording;
    protected $emails;

    function __construct($id, $is_recording, $emails) {
        $this->id           = $id;
        $this->is_recording = $is_recording;
        $ex                 = explode(',', $emails);
        $this->emails       = array_map('trim', (array)$ex);
    }

    function getIsRecording() {
        return ('on' === $this->is_recording);
    }

}