<?php
class PaymentService
{
    private $baseUrl;
    private $cookie;
    private $corporationPin;
    private $callbackUrl;

    public function __construct($callbackUrl = '')
    {
        $this->baseUrl = 'https://pna.shaparak.ir/mhipg/api/Payment';
        $this->cookie = 'cookiesession1=678B2875BDF02342BF323F0C82CA0720';
        $this->corporationPin = 'YQWEZUYP';
        $this->callbackUrl = $callbackUrl;
    }

    private function executeCurlRequest($url, $postData)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Cookie: ' . $this->cookie
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    public function sendPaymentRequest($amount, $orderId, $additionalData = '', $originator = '')
    {
        $postData = json_encode(
            array(
                "CorporationPin" => $this->corporationPin,
                "Amount" => $amount,
                "OrderId" => $orderId,
                "CallBackUrl" => $this->callbackUrl,
                "AdditionalData" => $additionalData,
                "Originator" => $originator
            )
        );

        return $this->executeCurlRequest($this->baseUrl . '/NormalSale', $postData);
    }

    public function confirmPayment($token)
    {
        $postData = json_encode(
            array(
                "CorporationPin" => $this->corporationPin,
                "Token" => $token
            )
        );

        return $this->executeCurlRequest($this->baseUrl . '/confirm', $postData);
    }
}


?>