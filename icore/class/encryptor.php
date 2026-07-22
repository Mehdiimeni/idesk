<?php
class Encryptor
{
    private $key; // کلید رمزگذاری
    private $cipher = 'aes-256-cbc'; // الگوریتم رمزگذاری
    private $iv_length; // طول بردار اولیه (IV)

    // سازنده کلاس
    public function __construct($key)
    {
        $this->key = $key;
        $this->iv_length = openssl_cipher_iv_length($this->cipher);
    }

    // تابع رمزگذاری
    public function encrypt($data)
    {
        $iv = openssl_random_pseudo_bytes($this->iv_length);
        $encryptedData = openssl_encrypt($data, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);

        // فشرده‌سازی داده‌ها و تبدیل به hex به جای base64
        $compressedData = gzcompress($iv . $encryptedData);
        return bin2hex($compressedData); // خروجی کوتاه‌تر از base64
    }

    // تابع رمزگشایی
    public function decrypt($encryptedData)
    {
        // تبدیل از hex به داده اصلی
        $data = hex2bin($encryptedData);

        // باز کردن فشرده‌سازی
        $decompressedData = gzuncompress($data);
        
        // جدا کردن IV و داده رمزگذاری شده
        $iv = substr($decompressedData, 0, $this->iv_length);
        $encryptedData = substr($decompressedData, $this->iv_length);

        return openssl_decrypt($encryptedData, $this->cipher, $this->key, OPENSSL_RAW_DATA, $iv);
    }
}
?>
