<?php 
namespace Todoist\Test\Unit;

use PHPUnit\Framework\TestCase;
use Todoist\Infra\Encoders\EncoderArgon2Id;

class EncoderArgon2IdTest extends TestCase
{
    public function test_password_must_be_hashed()
    {
        $encoder = new EncoderArgon2Id();

        $this->assertNotEquals($encoder->encode('password'), 'password');
    }

    public function test_password_must_be_unhashed()
    {
        $encoder = new EncoderArgon2Id();

        $hash = $encoder->encode('password');
        $decoded = $encoder->decode('password',$hash);

        $this->assertTrue($decoded);
    }
}