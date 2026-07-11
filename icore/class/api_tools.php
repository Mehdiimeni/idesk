<?php
class IAPI
{
    private $mainUrl;
    private $type;
    private static $instance = null;
    private $tokenGenerator;

    public function __construct($mainUrl, $type)
    {
        $this->mainUrl = $mainUrl;
        $this->type = $type;
    }

    public static function getInstance($mainUrl, $type)
    {
        if (self::$instance === null) {
            self::$instance = new self($mainUrl, $type);
        }
        return self::$instance;
    }


    private function startCurl()
    {
        return curl_init();
    }

    private function closeCurl($curl)
    {
        curl_close($curl);
    }

    private function execCurl($curl)
    {
        if (curl_error($curl)) {
            return "cURL Error #:" . curl_error($curl);
        }
        return curl_exec($curl);
    }

    private function prepareCurlOptions($url, $fields, $customRequest = 'POST')
    {
        $arr_token = array('token' => $this->tokenGenerator);
        $fields = array_merge($fields, $arr_token);
        $curl = $this->startCurl();
        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $customRequest,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($fields),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
        ];
        curl_setopt_array($curl, $options);
        return $curl;
    }

    public function getGeneralApi($apiUrl)
    {
        $fields = ['url' => $this->mainUrl, 'protecol' => 'https://'];
        return $this->prepareAndExecuteCurl('POST', $apiUrl, $fields);
    }

    public function getPostApi($apiUrl, $fields)
    {
        return $this->prepareAndExecuteCurl('POST', $apiUrl, $fields);
    }

    public function deleteApi($apiUrl)
    {
        return $this->prepareAndExecuteCurl('DELETE', $apiUrl, []);
    }

    public function patchApi($apiUrl, $fields)
    {
        return $this->prepareAndExecuteCurl('PATCH', $apiUrl, $fields);
    }

    private function prepareAndExecuteCurl($requestType, $apiUrl, $fields)
    {
        $url = $this->mainUrl . $this->type . '/' . $apiUrl . '.php';
        $curl = $this->prepareCurlOptions($url, $fields, $requestType);
        $this->closeCurl($curl);
        return $this->execCurl($curl);

    }

    /**
     * @param mixed $tokenGenerator 
     * @return self
     */
    public function setTokenGenerator($tokenGenerator): self
    {
        $this->tokenGenerator = $tokenGenerator;
        return $this;
    }
}