<?php

declare(strict_types=1);

namespace App\Platformsh;

use function Safe\chmod as chmod;
use function Safe\file_get_contents as file_get_contents;
use function Safe\file_put_contents as file_put_contents;

class RsaPrivateKey
{
    private const BEGIN_RSA_PRIVATE_KEY = "-----BEGIN RSA PRIVATE KEY-----";

    private const END_RSA_PRIVATE_KEY = "-----END RSA PRIVATE KEY-----";

    /* @var string */
    private $key;

    public function __construct(string $key)
    {
        $header_found_pos = strpos($key, self::BEGIN_RSA_PRIVATE_KEY);
        $footer_found_pos = strrpos($key, self::END_RSA_PRIVATE_KEY);
        if ($header_found_pos > 0) {
            throw new \UnexpectedValueException('Invalid header position');
        } elseif ($header_found_pos !== false &&
            strpos($key, "\n") !== strlen(self::BEGIN_RSA_PRIVATE_KEY)) {
            throw new \UnexpectedValueException('Header must be followed by a linebreak');
        } elseif ($footer_found_pos !== false  &&
            $footer_found_pos !== strlen($key) - strlen(self::END_RSA_PRIVATE_KEY)) {
            throw new \UnexpectedValueException('Invalid footer position');
        } elseif ($footer_found_pos !== false &&
            strrpos($key, "\n") !== (strlen($key) - strlen(self::END_RSA_PRIVATE_KEY) - 1)) {
            throw new \UnexpectedValueException('Footer must be preceeded by a linebreak');
        }

        if ($header_found_pos === false) {
            $key = self::BEGIN_RSA_PRIVATE_KEY . "\n" . $key;
        }

        if ($footer_found_pos === false) {
            $key .= "\n" . self::END_RSA_PRIVATE_KEY;
        }

        $this->key = $key;
    }

    public static function fromFile(string $path) : self
    {
        return new self(file_get_contents($path));
    }

    public function toFile(string $path) : void
    {
        file_put_contents($path, $this->key);
        chmod($path, 0600);
    }
}
