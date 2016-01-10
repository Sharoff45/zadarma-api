<?php
namespace Sharoff\Zadarma\Response;

Class Response {

    function toArray() {
        return get_object_vars($this);
    }

    function __call($method, $args) {
        $property = $this->snake(mb_substr($method, 3));
        if (isset($this->$property)) {
            return $this->$property;
        }
        throw new \Exception('Обращение к несуществующему методу [' . $method . ']');
    }

    protected function snake($value) {
        if (!ctype_lower($value)) {
            $replace = '$1_$2';
            $value   = strtolower(preg_replace('/(.)([A-Z])/', $replace, $value));
        }
        return $value;
    }

}