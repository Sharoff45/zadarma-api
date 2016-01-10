<?php
namespace Sharoff\Zadarma\Helper;

class HelperPhone {

    static protected $phones;

    static function normalize($phone) {
        $p = preg_replace('/\D/', '', $phone);

        return $p;
    }

    static function country($phone) {
        $p = self::normalize($phone);
        if (!self::$phones) {
            self::$phones = include __DIR__ . '/config/phones.php';
        }
        $codes = array_keys(self::$phones);
        rsort($codes);
        foreach ($codes as $code) {
            if ($code == mb_substr($p, 0, mb_strlen($code))) {
                return self::$phones[$code]['code'];
            }
        }
        return null;
    }

    static function denormalize($phone) {
        $p = self::normalize($phone);
        if (!self::$phones) {
            self::$phones = include __DIR__ . '/config/phones.php';
        }
        $config = self::$phones;
        if (!count($config)) {
            return $p;
        }
        $codes = array_keys($config);
        rsort($codes);
        foreach ($codes as $code) {
            if ($code == mb_substr($p, 0, mb_strlen($code))) {
                $mask        = $config[$code]['mask'];
                $phone_array = str_split(mb_substr($p, mb_strlen($code)));
                $mask_array  = str_split($mask);
                foreach ($mask_array as $key => $symbol) {
                    if ('#' == $symbol) {
                        $mask_array[$key] = array_shift($phone_array);
                    }
                }
                return implode('', $mask_array);
            }
        }

        return null;
    }
}

