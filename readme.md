#Класс для работы с ZADARMA.COM API

##Установка
~~~sh
composer require sharoff/zadarma-api
~~~

### Подключаем autoload
~~~php
<?php 
require_once __DIR__ . '/../vendor/autoload.php';
~~~

###Задаем ключи приложения для работы через API
~~~php
use Sharoff\Zadarma\Api;
Api::factory()
   ->setTesting() // для проведения тестов
   ->setKey('********************')
   ->setSecret('********************');
~~~

###Получение баланса
~~~php
$data = Api::factory()
           ->getBalance();
var_dump($data);
~~~

###Получение стоимости звонка на указанный номер
Номер может быть в любом формате, скрипт оставит только цифры
~~~php
$data = Api::factory()
           ->getPrice('+7 (922) 555-33-22');
var_dump($data);
~~~

###Запрос callback
~~~php
$data = Api::factory()
           ->requestCallback('sip_id', '+7 (555) 333-22-11');
var_dump($data);
~~~

###Получение списока SIP-номеров
~~~php
$data = Api::factory()
           ->getSip();
var_dump($data);
~~~

###Получение баланса
~~~php
$data = Api::factory()
           ->getBalance();
var_dump($data);
~~~

###Указание CallerID
~~~php
$data = Api::factory()
           ->setSipCallerId('sip_id', '75553332211')
var_dump($data);
~~~

###Получение текущих переадресаций по SIP-номерам пользователя
$id - выбор конкретного SIP id
~~~php
$data = Api::factory()
           ->getSipRedirection($id = null);
var_dump($data);
~~~

###Получение текущих переадресаций по SIP-номерам пользователя
$id - выбор конкретного SIP id
~~~php
$data = Api::factory()
           ->putPbxRecording(100, 'mail@example.com')
var_dump($data);
~~~

