<?php

class LocationManager
{
    private static $instance;
    private $ip;
    private $language;

    private function __construct()
    {
        $this->ip = $_SERVER['REMOTE_ADDR'];
        $this->language = $this->getLanguageByIP($this->ip);
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getIP()
    {
        return $this->ip;
    }

    public function getLanguage()
    {
        return $this->language;
    }

    private function getLanguageByIP($ip)
    {
        $countryLanguageMap = [
            'UK' => 'en', 
            'IR' => 'fa', 
            'UAE' => 'ar', 
            'FR' => 'fr'
        
        ];

        $countryCode = $this->getCountryCodeByIP($ip);
        if (isset($countryLanguageMap[$countryCode])) {
            return $countryLanguageMap[$countryCode];
        }

        return 'fa';
    }

    private function getCountryCodeByIP($ip)
    {
 
         $apiUrl = "http://ip-api.com/json/{$ip}";
         $response = file_get_contents($apiUrl);
         $data = json_decode($response, true);
         if (isset($data['countryCode'])) {
           return $data['countryCode'];
         }else{
            return 'IR';
         }
    }
}
