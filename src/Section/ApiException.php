<?php
namespace Sharoff\Zadarma;

Class ApiException extends \Exception {

    function notSetClientId() {
        $this->message = 'Не указан CLIENT_ID';
        return $this;
    }

    function notSetClientSecret() {
        $this->message = 'Не указан CLIENT_SECRET';
        return $this;
    }

    function methodNotFound($method) {
        $this->message = 'Обращение к несуществующему методу [' . $method . ']';
        return $this;
    }

    function methodNotAllowed($method, $type) {
        $this->message = 'Обращение методу [' . $method . '] невозможно через [' . $type . ']';
        return $this;
    }

    function errorGet($method, $error) {
        $this->message = 'Обращение методу [' . $method . '] завершилось ошибкой: ' . $error;
        return $this;
    }

    function apiKey() {
        $this->message = 'Не указан ключ приложения';
        return $this;
    }

    function apiSecret() {
        $this->message = 'Не указан secret приложения';
        return $this;
    }

    function errorHttp($message) {
        $this->message = 'Ошибка: ' . $message;
        return $this;
    }

    function errorMessage($message) {
        if (is_null($message)) {
            $this->message = 'Неизвестная ошибка';
        } else {
            $this->message = 'Ошибка: ' . $message;
        }
        return $this;
    }

}