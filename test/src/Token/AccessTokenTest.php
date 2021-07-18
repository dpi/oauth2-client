<?php

namespace League\OAuth2\Client\Test\Token;

use InvalidArgumentException;
use League\OAuth2\Client\Test\Provider\FrozenClock;
use League\OAuth2\Client\Test\Provider\ProgrammableClock;
use League\OAuth2\Client\Token\AccessToken;
use Mockery;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class AccessTokenTest extends TestCase
{

    /**
     * The current simulated time.
     *
     * @var int
     */
    const NOW = 1359504000;

    public function testInvalidRefreshToken()
    {
        $this->expectException(InvalidArgumentException::class);

        $token = $this->getAccessToken(['invalid_access_token' => 'none']);
    }

    protected function getAccessToken($options = [])
    {
        return new AccessToken($options);
    }

    public function testExpiresInCorrection()
    {
        // Correction happens in constructor so time needs to be set before
        // object is created.
        $options = [
            'access_token' => 'access_token',
            'expires_in' => 100,
            'clock' => new FrozenClock(),
        ];
        $token = $this->getAccessToken($options);

        $expires = $token->getExpires();

        $this->assertEquals(FrozenClock::NOW + 100, $expires);
    }

    public function testExpiresInCorrectionUsingSetTimeNow()
    {
        $options = [
            'access_token' => 'access_token',
            'expires_in' => 100,
            'clock' => new FrozenClock(),
        ];
        $token = $this->getAccessToken($options);

        $expires = $token->getExpires();

        $this->assertNotNull($expires);
        $this->assertEquals(FrozenClock::NOW + 100, $expires);
    }

    public function testExpiresPastTimestamp()
    {
        $options = [
            'access_token' => 'access_token',
            'expires' => self::NOW - 1,
            'clock' => ProgrammableClock::newFromTimestamp(self::NOW),
        ];
        $token = $this->getAccessToken($options);

        $this->assertTrue($token->hasExpired());

        $options = ['access_token' => 'access_token', 'expires' => 3600];
        $token = $this->getAccessToken($options);

        $this->assertFalse($token->hasExpired());
    }

    public function testGetRefreshToken()
    {
        $options = [
            'access_token' => 'access_token',
            'refresh_token' => uniqid()
        ];
        $token = $this->getAccessToken($options);

        $refreshToken = $token->getRefreshToken();

        $this->assertEquals($options['refresh_token'], $refreshToken);
    }

    public function testHasNotExpiredWhenPropertySetInFuture()
    {
        $clock = ProgrammableClock::newFromTimestamp(self::NOW);
        $token = Mockery::mock(AccessToken::class)->makePartial();

        $token
            ->shouldReceive('getClock')
            ->once()
            ->andReturn(ProgrammableClock::newFromTimestamp(self::NOW));

        $token
            ->shouldReceive('getExpires')
            ->once()
            ->andReturn(self::NOW + 1);

        $this->assertFalse($token->hasExpired());
    }

    public function testHasExpiredWhenPropertySetInPast()
    {
        $token = Mockery::mock(AccessToken::class)->makePartial();
        $token
            ->shouldReceive('getExpires')
            ->once()
            ->andReturn(self::NOW - 1);

        $this->assertTrue($token->hasExpired());
    }

    public function testCannotReportExpiredWhenNoExpirationSet()
    {
        $options = [
            'access_token' => 'access_token',
        ];
        $token = $this->getAccessToken($options);

        $this->expectException(RuntimeException::class);

        $hasExpired = $token->hasExpired();
    }

    public function testInvalidExpiresIn()
    {
         $options = [
            'access_token' => 'access_token',
            'expires_in' => 'TEXT',
         ];

         $this->expectException(InvalidArgumentException::class);

         $token = $this->getAccessToken($options);
    }


    public function testJsonSerializable()
    {
        $options = [
            'access_token' => 'mock_access_token',
            'refresh_token' => 'mock_refresh_token',
            'expires' => self::NOW + 3600,
            'resource_owner_id' => 'mock_resource_owner_id',
        ];

        $token = $this->getAccessToken($options);
        $jsonToken = json_encode($token);

        $this->assertEquals($options, json_decode($jsonToken, true));
    }

    public function testValues()
    {
        $options = [
            'access_token' => 'mock_access_token',
            'refresh_token' => 'mock_refresh_token',
            'expires' => self::NOW + 3600,
            'resource_owner_id' => 'mock_resource_owner_id',
            'custom_thing' => 'i am a test!',
        ];

        $token = $this->getAccessToken($options);

        $values = $token->getValues();

        $this->assertTrue(is_array($values));
        $this->assertArrayHasKey('custom_thing', $values);
        $this->assertSame($options['custom_thing'], $values['custom_thing']);
    }
}
