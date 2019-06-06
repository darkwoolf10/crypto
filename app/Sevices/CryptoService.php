<?php

namespace App\Sevices;

/**
 * Class CryptoService
 * @package App\Sevices
 */
class CryptoService implements CryptoInterface
{

    private $method;

    public function __construct(?string $method)
    {
        $this->method = $method;
    }

    /**
     * @param string $data
     * @param string $secret_key
     * @param string $secret_iv
     * @return string
     */
    public function encrypt(string $data, string $secret_key, string $secret_iv): string
    {
        $output = openssl_encrypt($data, $this->method, $secret_key, 0, $secret_iv);

        return base64_encode($output);
    }

    public function decrypt(string $data,string $secret_key, string $secret_iv): string
    {
        $output =  openssl_decrypt($data, $this->method, $secret_key, 0, $secret_iv);

        return base64_encode($output);
    }
}