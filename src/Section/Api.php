<?php
namespace Sharoff\Zadarma;


use Sharoff\Zadarma\Helper\HelperPhone;
use Sharoff\Zadarma\Response\Balance;
use Sharoff\Zadarma\Response\PbxInternal;
use Sharoff\Zadarma\Response\PbxRecording;
use Sharoff\Zadarma\Response\Price;
use Sharoff\Zadarma\Response\Redirection;
use Sharoff\Zadarma\Response\RedirectionStatus;
use Sharoff\Zadarma\Response\RedirectionUpdate;
use Sharoff\Zadarma\Response\RequestCallback;
use Sharoff\Zadarma\Response\Sip;
use Sharoff\Zadarma\Response\SipCaller;
use Sharoff\Zadarma\Response\SmsStatus;

Class Api {

    const PROD_URL    = 'https://api.zadarma.com';
    const SANDBOX_URL = 'https://api-sandbox.zadarma.com';

    // версия API
    const API_VERSION = 'v1';

    // доступные типы запроса
    const REQUEST_GET    = 'GET';
    const REQUEST_POST   = 'POST';
    const REQUEST_PUT    = 'PUT';
    const REQUEST_DELETE = 'DELETE';
    protected $request_types = [
        self::REQUEST_GET,
        self::REQUEST_POST,
        self::REQUEST_PUT,
        self::REQUEST_DELETE,
    ];

    // доступные методы работы с апи
    const METHOD_BALANCE                = 'info/balance';
    const METHOD_PRICE                  = 'info/price';
    const METHOD_CALLBACK               = 'request/callback';
    const METHOD_SIP                    = 'sip';
    const METHOD_SIP_CALLER_ID          = 'sip/callerid';
    const METHOD_SIP_REDIRECTION        = 'sip/redirection';
    const METHOD_PBX_INTERNAL           = 'pbx/internal';
    const METHOD_PBX_INTERNAL_RECORDING = 'pbx/internal/recording';
    const METHOD_SMS_SEND               = 'sms/send';
    const METHOD_STATISTICS             = 'statistics';
    const METHOD_STATISTICS_PBX         = 'statistics/pbx';

    // доступные типы запросов по методам
    protected $methods = [
        self::METHOD_BALANCE                => self::REQUEST_GET,
        self::METHOD_PRICE                  => self::REQUEST_GET,
        self::METHOD_CALLBACK               => self::REQUEST_GET,
        self::METHOD_SIP                    => self::REQUEST_GET,
        self::METHOD_SIP_CALLER_ID          => self::REQUEST_PUT,
        self::METHOD_SIP_REDIRECTION        => [
            self::REQUEST_GET,
            self::REQUEST_PUT,
        ],
        self::METHOD_PBX_INTERNAL           => self::REQUEST_GET,
        self::METHOD_PBX_INTERNAL_RECORDING => self::REQUEST_PUT,
        self::METHOD_SMS_SEND               => self::REQUEST_POST,
        self::METHOD_STATISTICS             => self::REQUEST_GET,
    ];

    // коды HTTP-ошибок
    const HTTP_ERROR_LIMIT = 429;
    protected $error_http_codes = [
        self::HTTP_ERROR_LIMIT => 'Превышен лимит запросов к API'
    ];

    protected static $instances = [];
    /**
     * Ключ API
     *
     * @var string
     */
    protected $api_key;

    /**
     * Секрет API
     *
     * @var string
     */
    protected $api_secret;

    /**
     * Является ли приложение тестовым
     *
     * @var bool
     */
    protected $is_testing = false;

    /**
     * @param string $id
     *
     * @return Api
     */
    static function factory($id = 'default') {
        $class = get_called_class();
        if (isset(static::$instances[$id . $class])) {
            return static::$instances[$id . $class];
        }
        $api                             = new $class();
        static::$instances[$id . $class] = $api;
        return $api;
    }

    /**
     * Задать ключ
     *
     * @param $key
     *
     * @return $this
     */
    function setKey($key) {
        $this->api_key = $key;
        return $this;
    }

    /**
     * Задать секретный ключ
     *
     * @param $secret
     *
     * @return $this
     */
    function setSecret($secret) {
        $this->api_secret = $secret;
        return $this;
    }

    /**
     * Переключение тестового режима
     *
     * @param bool $is_testing
     *
     * @return $this
     */
    function setTesting($is_testing = true) {
        $this->is_testing = (bool)$is_testing;
        return $this;
    }

    /**
     * получение баланса
     *
     * @return Balance
     * @throws ApiException
     */
    function getBalance() {
        $data = $this->apiGet(self::METHOD_BALANCE, [], self::REQUEST_GET);
        return new Balance($data->balance, $data->currency);
    }

    /**
     * стоимость звонка с учетом текущего тарифа пользователя.
     *
     * @param $phone - номер телефона
     *
     * @return Price
     * @throws ApiException
     */
    function getPrice($phone) {
        $phone = HelperPhone::normalize($phone);
        $data  = $this->apiGet(self::METHOD_PRICE, ['number' => $phone], self::REQUEST_GET);
        return new Price($data->info->prefix, $data->info->description, $data->info->price, $data->info->currency);
    }

    /**
     * запрос на callback
     * Подробнее о callback: https://zadarma.com/ru/services/calls/callback/
     *
     * @param      $from      - ваш номер телефона или SIP, или внутренний номер АТС, или номер сценария АТС, на
     *                        который
     *                        вызывается callback;
     * @param      $to        – номер телефона или SIP, которому звонят;
     * @param null $sip       – номер SIP-пользователя или внутренний номер АТС (например 100), через который
     *                        произойдет звонок. Будет использован CallerID этого номера, в
     *                        статистике будет отображаться данный номер SIP/АТС, если для указанного номера включена
     *                        запись звонков либо префиксы набора, они также будут задействованы; 
     * @param null $predicted – если указан этот флаг, то запрос является предикативным (система изначально звонит на
     *                        номер “to” и только если ему дозванивается, соединяет с вашим SIP либо телефонным
     *                        номером);
     *
     * @return RequestCallback
     * @throws ApiException
     */
    function requestCallback($from, $to, $sip = null, $predicted = null) {
        $params         = [];
        $params['from'] = $from;
        $params['to']   = HelperPhone::normalize($to);
        if (!is_null($sip)) {
            $params['sip'] = $sip;
        }
        if (!is_null($predicted)) {
            $params['predicted'] = $predicted;
        }
        $data = $this->apiGet(self::METHOD_CALLBACK, $params, self::REQUEST_GET);
        return new RequestCallback($data->from, $data->to, $data->time);
    }

    /**
     * список SIP-номеров
     *
     * @return array
     * @throws ApiException
     */
    function getSip() {
        $data = $this->apiGet(self::METHOD_SIP, [], self::REQUEST_GET);
        if (is_array($data->sips)) {
            $sip_list = [];
            foreach ($data->sips as $sip) {
                $sip_list[] = new Sip($sip->id, $sip->display_name, $sip->lines);
            }
            return [
                'left' => (int)$data->left,
                'sips' => $sip_list
            ];
        }
        return [];
    }

    /**
     * изменение CallerID
     *
     * @param $sip_id – SIP id, которому меняют CallerID
     * @param $number – номер, на который меняют в международном формате (из списка подтвержденных либо приобретенных
     *                номеров пользователя).
     *
     * @return SipCaller
     * @throws ApiException
     */
    function setSipCallerId($sip_id, $number) {
        $params = [
            'id'     => $this->prepareSipId($sip_id),
            'number' => HelperPhone::normalize($number)
        ];
        $data   = $this->apiGet(self::METHOD_SIP_CALLER_ID, $params, self::REQUEST_PUT);
        return new SipCaller($data->sip, $data->new_caller_id);
    }

    /**
     * отображение текущих переадресаций по SIP-номерам пользователя.
     *
     * @param null $id - выбор конкретного SIP id
     *
     * @return array
     * @throws ApiException
     */
    function getSipRedirection($id = null) {
        $params = [];
        if (!is_null($id)) {
            $params['id'] = $this->prepareSipId($id);
        }
        $data = $this->apiGet(self::METHOD_SIP_REDIRECTION, $params, self::REQUEST_GET);
        if (is_array($data->info)) {
            $redirection = [];
            foreach ($data->info as $info) {
                $redirection[] = new Redirection($info->sip_id, $info->status, $info->condition, $info->destination, $info->destination_value);
            }
            return $redirection;
        }
        return [];
    }

    /**
     * включение/выключение переадресации по номеру SIP.
     *
     * @param $sip_id - SIP id;
     * @param $status – выставляемый статус переадресации на выбранный SIP-номер. (on/off | true/false)
     *
     * @return RedirectionStatus
     * @throws ApiException
     */
    function putSipRedirectionStatus($sip_id, $status) {
        $params = [
            'id' => $this->prepareSipId($sip_id)
        ];
        if (is_bool($status)) {
            $params['status'] = ($status ? 'on' : 'off');
        } else {
            $params['status'] = ('on' == $status ? 'on' : 'off');
        }
        $data = $this->apiGet(self::METHOD_SIP_REDIRECTION, $params, self::REQUEST_PUT);
        return new RedirectionStatus($data->sip, $data->current_status);
    }

    /**
     * выключение переадресации по номеру SIP.
     *
     * @param $sip_id
     *
     * @return RedirectionStatus
     */
    function offSipRedirection($sip_id) {
        return $this->putSipRedirectionStatus($sip_id, false);
    }

    /**
     * включение  переадресации по номеру SIP.
     *
     * @param $sip_id
     *
     * @return RedirectionStatus
     */
    function onSipRedirection($sip_id) {
        return $this->putSipRedirectionStatus($sip_id, 'on');
    }

    const REDIRECTION_PHONE = 'phone';
    const REDIRECTION_PBX   = 'pbx';

    /**
     * изменение параметров переадресации.
     *
     * @param $sip_id
     * @param $type          – тип переадресации: phone – на телефон;
     *                       pbx - на АТС
     * @param $destination   – номер телефона.
     *
     * @return RedirectionUpdate
     * @throws ApiException
     */
    function updateSipRedirection($sip_id, $type, $destination) {
        $params = [
            'id'   => $this->prepareSipId($sip_id),
            'type' => $type
        ];
        if (self::REDIRECTION_PHONE == $type) {
            $params['destination'] = HelperPhone::normalize($destination);
        }
        $data = $this->apiGet(self::METHOD_SIP_REDIRECTION, $params, self::REQUEST_PUT);
        return new RedirectionUpdate($data->sip, $data->destination);
    }

    /**
     * отображение внутренних номеров АТС.
     *
     * @return PbxInternal
     * @throws ApiException
     */
    function getPbxInternal() {
        $data = $this->apiGet(self::METHOD_PBX_INTERNAL, [], self::REQUEST_GET);
        return new PbxInternal($data->pbx_id, $data->numbers);
    }

    /**
     * включение записи разговоров на внутреннем номере АТС.
     *
     * @param        $id
     * @param        $email - изменение электронного адреса, куда будут отправляться записи разговоров. Вы можете
     *                      указать до 3х email адресов.
     * @param string $status
     *
     * @return PbxRecording
     * @throws ApiException
     */
    function putPbxRecording($id, $email, $status = 'on') {
        $params = compact('id');
        if (is_bool($status)) {
            $params['status'] = ($status ? 'on' : 'off');
        } else {
            $params['status'] = ('on' == $status ? 'on' : 'off');
        }
        $params['email'] = implode(',', (array)$email);
        $data            = $this->apiGet(self::METHOD_PBX_INTERNAL_RECORDING, $params, self::REQUEST_PUT);
        return new PbxRecording($data->internal_number, $data->recording, $data->email);
    }

    /**
     * отправка SMS.
     *
     * @param      $to        - номер телефона, куда отправлять SMS
     * @param      $message   - сообщение (стандартные ограничения по длине SMS, в случае превышения лимита –
     *                        разбивается на несколько SMS);
     * @param null $caller_id - (опциональный) номер телефона, от кого будет отправлена SMS (можно отправлять только из
     *                        списка подтвержденных номеров пользователя).
     *
     * @return SmsStatus
     * @throws ApiException
     */
    function sendSms($to, $message, $caller_id = null) {
        $params = [
            'number'  => HelperPhone::normalize($to),
            'message' => $message
        ];
        if (!is_null($caller_id)) {
            $params['caller_id'] = $caller_id;
        }
        $data = $this->apiGet(self::METHOD_SMS_SEND, $params, self::REQUEST_POST);
        return new SmsStatus($data->messages, $data->cost, $data->currency);
    }


    /**
     * Отправка запроса на сервер zadarma.com
     *
     * @param        $method
     * @param array  $params
     * @param string $type
     *
     * @return mixed
     * @throws ApiException
     */
    protected function apiGet($method, array $params = [], $type = self::REQUEST_GET) {
        $ex = new ApiException();
        if (!$this->api_key) {
            throw $ex->apiKey();
        }
        if (!$this->api_secret) {
            throw $ex->apiSecret();
        }
        if (!isset($this->methods[$method])) {
            throw $ex->methodNotFound($method);
        }

        if (!in_array($type, (array)$this->methods[$method])) {
            throw $ex->methodNotAllowed($method, $type);
        }

        $method_url = '/' . self::API_VERSION . '/' . $method . '/';

        $url = ($this->is_testing ? self::SANDBOX_URL : self::PROD_URL) . $method_url;

        $params['format'] = 'json';
        ksort($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        if (self::REQUEST_GET == $type) {
            curl_setopt($ch, CURLOPT_URL, $url . '?' . http_build_query($params));
        } else {
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
        }
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->getAuthHeader($method_url, $params));

        $response = curl_exec($ch);

        $error      = curl_error($ch);
        $code       = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $header_len = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        curl_close($ch);

        $headers = mb_substr($response, 0, $header_len);
        $content = mb_substr($response, $header_len);

        if (isset($this->error_http_codes[$code])) {
            throw $ex->errorHttp($this->error_http_codes[$code]);
        }

        if ($error) {
            throw $ex->errorGet($method, $error);
        }
        $this->saveHeaders($headers);
        $data = json_decode($content);
        if ('success' == $data->status) {
            return $data;
        }

        throw $ex->errorMessage($data->message);
    }

    protected $remaining;
    protected $reset_time;

    protected function saveHeaders($headers) {
        $ex = explode(PHP_EOL, $headers);
        foreach ($ex as $header) {
            list($key, $value) = explode(':', $header, 2);
            switch ($key) {
                case 'X-RateLimit-Remaining':
                    $this->remaining = (int)$value;
                    break;
                case 'X-RateLimit-Reset':
                    $this->reset_time = date('Y-m-d H:i:s', $value);
                    break;
                default:

                    break;
            }
        }
    }

    /**
     * Остаток запросов
     * ВНИМАНИЕ! Вызом можно делать только после запроса к апи, иначе в результате будет false
     *
     * @return int|bool
     */
    function getLimitRemaining() {
        if ($this->remaining) {
            return $this->remaining;
        }
        return false;
    }

    /**
     * Получение времени обновления лимита
     * ВНИМАНИЕ! Вызом можно делать только после запроса к апи, иначе в результате будет false
     *
     * @return int|bool
     */
    function getResetTime() {
        if ($this->reset_time) {
            return $this->reset_time;
        }
        return false;
    }


    /**
     * Обработка SIP ID
     *
     * @param $sip_id
     *
     * @return string
     */
    protected function prepareSipId($sip_id) {
        return (int)$sip_id;
    }

    /**
     * Создание цифровой подписи запроса
     * (copied from https://github.com/zadarma/user-api-v1/blob/master/lib/Client.php)
     *
     * @param $method
     * @param $params
     *
     * @return array
     */
    protected function getAuthHeader($method, $params) {
        $paramsString = http_build_query($params);
        $signature    = base64_encode(hash_hmac('sha1', $method . $paramsString . md5($paramsString), $this->api_secret));
        return ['Authorization: ' . $this->api_key . ':' . $signature];
    }


}