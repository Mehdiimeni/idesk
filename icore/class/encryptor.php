<?php
<<<<<<< HEAD

class Encryptor
{
    private string $cipher = 'aes-256-cbc';
    private string $key;
    private string $hmacKey;
    private int $ivLength;

    public function __construct(string $key)
    {
        $this->key = hash('sha256', $key, true);
        $this->hmacKey = hash('sha256', 'hmac_' . $key, true);
        $this->ivLength = openssl_cipher_iv_length($this->cipher);
    }

    public function encrypt(string $data): string
    {
        $iv = random_bytes($this->ivLength);

        $encryptedData = openssl_encrypt(
            $data,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        if ($encryptedData === false) {
            throw new Exception('Encryption failed.');
        }

        $payload = $iv . $encryptedData;

        $hmac = hash_hmac('sha256', $payload, $this->hmacKey, true);

        return rtrim(strtr(base64_encode($hmac . $payload), '+/', '-_'), '=');
    }

    public function decrypt(string $encryptedData): ?string
    {
        $raw = base64_decode(
            strtr($encryptedData, '-_', '+/'),
            true
        );

        if ($raw === false) {
            return null;
        }

        if (strlen($raw) < 32 + $this->ivLength) {
            return null;
        }

        $hmac = substr($raw, 0, 32);
        $payload = substr($raw, 32);

        $calculatedHmac = hash_hmac('sha256', $payload, $this->hmacKey, true);

        if (!hash_equals($hmac, $calculatedHmac)) {
            return null;
        }

        $iv = substr($payload, 0, $this->ivLength);
        $encryptedRaw = substr($payload, $this->ivLength);

        $decrypted = openssl_decrypt(
            $encryptedRaw,
            $this->cipher,
            $this->key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $decrypted === false ? null : $decrypted;
    }
}
?>
=======
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
>>>>>>> 5591029... some change
