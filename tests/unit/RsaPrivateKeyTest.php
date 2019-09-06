<?php

namespace App\Tests\unit;

use App\Platformsh\RsaPrivateKey;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

class RsaPrivateKeyTest extends TestCase
{

    /**
     * @doesNotPerformAssertions
     * @dataProvider validKeyProvider
     */
    public function testKeys(string $key)
    {
        new RsaPrivateKey($key);
    }

    public function validKeyProvider()
    {
        return [
            // Standard key format
            [ '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDgXlNbWAqXwSwCbdtkYk61vyHYopBqC53bakYv7XgOtqgOk9Kk
f3v3jpQpdpDx6eiFTOkYW3KyGn+EjPjqDXhZUJ7NSex4SPiGAQjQ9B3fEvYs+LMr
-----END RSA PRIVATE KEY-----' ],
            // Key with in single line
            // phpcs:disable Generic.Files.LineLength
            [ '-----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDgXlNbWAqXwSwCbdtkYk61vyHYopBqC53bakYv7XgOtqgOk9Kkf3v3jpQpdpDx6eiFTOkYW3KyGn+EjPjqDXhZUJ7NSex4SPiGAQjQ9B3fEvYs+LMr
-----END RSA PRIVATE KEY-----' ],
            // phpcs:enable
            // Missing header and footer
            [ 'MIICXAIBAAKBgQDgXlNbWAqXwSwCbdtkYk61vyHYopBqC53bakYv7XgOtqgOk9Kk
f3v3jpQpdpDx6eiFTOkYW3KyGn+EjPjqDXhZUJ7NSex4SPiGAQjQ9B3fEvYs+LMr' ]
        ];
    }

    /**
     * @dataProvider invalidKeyProvider
     */
    public function testInvalidKeys(string $key)
    {
        $this->expectException(\UnexpectedValueException::class);
        $rsaKey = new RsaPrivateKey($key);
    }

    public function invalidKeyProvider()
    {
        return [
            // Invalid preceeding spave
            [ ' -----BEGIN RSA PRIVATE KEY-----
MIICXAIBAAKBgQDgXlNbWAqXwSwCbdtkYk61vyHYopBqC53bakYv7XgOtqgOk9Kk
f3v3jpQpdpDx6eiFTOkYW3KyGn+EjPjqDXhZUJ7NSex4SPiGAQjQ9B3fEvYs+LMr
-----END RSA PRIVATE KEY-----' ],
            // Missing newlines between header key and footer
            // phpcs:ignore Generic.Files.LineLength
            [ '-----BEGIN RSA PRIVATE KEY-----MIICXAIBAAKBgQDgXlNbWAqXwSwCbdtkYk61vyHYopBqC53bakYv7XgOtqgOk9Kkf3v3jpQpdpDx6eiFTOkYW3KyGn+EjPjqDXhZUJ7NSex4SPiGAQjQ9B3fEvYs+LMr-----END RSA PRIVATE KEY-----' ],
            // Spaces not newlines between header key and footer
            // phpcs:ignore Generic.Files.LineLength
            [ '-----BEGIN RSA PRIVATE KEY----- MIICXAIBAAKBgQDgXlNbWAqXwSwCbdtkYk61vyHYopBqC53bakYv7XgOtqgOk9Kkf3v3jpQpdpDx6eiFTOkYW3KyGn+EjPjqDXhZUJ7NSex4SPiGAQjQ9B3fEvYs+LMr -----END RSA PRIVATE KEY-----' ]
        ];
    }

    public function testKeyReadWrite()
    {
        $key = new RsaPrivateKey('somekey1234');

        $filesystem = vfsStream::setup('dir');
        $path = vfsStream::url('dir/key');
        $key->toFile($path);

        RsaPrivateKey::fromFile($path);

        $file = $filesystem->getChild('key');
        $this->assertTrue($file->isReadable($file->getUser(), $file->getGroup()));
        $this->assertFalse($file->isReadable($file->getUser() + 1, $file->getGroup()));
    }
}
