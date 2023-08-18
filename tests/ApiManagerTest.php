<?php

namespace rdoepner\CleverReach\Tests;

use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use PHPUnit\Framework\TestCase;
use rdoepner\CleverReach\ApiManager;
use rdoepner\CleverReach\Http\Guzzle as HttpAdapter;

class ApiManagerTest extends TestCase
{
    /**
     * @var ApiManager
     */
    protected static $apiManager;

    /**
     * @var string
     */
    protected static $groupId;

    public static function setUpBeforeClass(): void
    {
        $httpAdapter = new HttpAdapter(
            [
                'access_token' => getenv('GROUP_ID'),
            ],
            (new Logger('debug'))->pushHandler(
                new StreamHandler(dirname(__DIR__) . '/var/log/api.log')
            )
        );

        $httpAdapter->authorize(
            getenv('CLIENT_ID'),
            getenv('CLIENT_SECRET')
        );

        self::$apiManager = new ApiManager($httpAdapter);
        self::$groupId = getenv('GROUP_ID');
    }

    public function testCreateSubscriber()
    {
        $response = self::$apiManager->createSubscriber(
            'john.doe@example.org',
            self::$groupId,
            false,
            [
                'salutation' => 'Mr.',
                'firstname' => 'John',
                'lastname' => 'Doe',
            ],
            [],
            'Random Source',
            ['tag']
        );

        $this->assertArrayHasKey('email', $response);
        $this->assertEquals('john.doe@example.org', $response['email']);
    }

    public function testUpdateSubscriber()
    {
        $response = self::$apiManager->updateSubscriber(
            'john.doe@example.org',
            self::$groupId,
            [
                'salutation' => 'Mr.',
                'firstname' => 'John',
                'lastname' => 'Doe',
            ],
            [],
            'Random Source',
            ['tag']
        );

        $this->assertArrayHasKey('email', $response);
        $this->assertEquals('john.doe@example.org', $response['email']);
    }

    public function testGetSubscriber()
    {
        $response = self::$apiManager->getSubscriber(
            'john.doe@example.org',
            self::$groupId
        );

        $this->assertArrayHasKey('email', $response);
        $this->assertEquals('john.doe@example.org', $response['email']);

        $response = self::$apiManager->getSubscriber(
            'jane.doe@example.org',
            self::$groupId
        );

        $this->assertArrayHasKey('error', $response);
        $this->assertArrayHasKey('code', $response['error']);
        $this->assertArrayHasKey('message', $response['error']);
        $this->assertEquals(404, $response['error']['code']);
        $this->assertEquals('Not Found: invalid receiver', $response['error']['message']);
    }

    public function testSetSubscriberStatus()
    {
        $response = self::$apiManager->setSubscriberStatus(
            'john.doe@example.org',
            self::$groupId,
            true
        );

        $this->assertTrue($response);

        $response = self::$apiManager->getSubscriber(
            'john.doe@example.org',
            self::$groupId
        );

        $this->assertArrayHasKey('active', $response);
        $this->assertTrue($response['active']);

        $response = self::$apiManager->setSubscriberStatus(
            'john.doe@example.org',
            self::$groupId,
            false
        );

        $this->assertTrue($response);

        $response = self::$apiManager->getSubscriber(
            'john.doe@example.org',
            self::$groupId
        );

        $this->assertArrayHasKey('active', $response);
        $this->assertFalse($response['active']);
    }

    public function testDeleteSubscriber()
    {
        $response = self::$apiManager->deleteSubscriber(
            'john.doe@example.org',
            self::$groupId
        );

        $this->assertTrue($response);

        $response = self::$apiManager->deleteSubscriber(
            'jane.doe@example.org',
            self::$groupId
        );

        $this->assertArrayHasKey('error', $response);

        $this->assertArrayHasKey('code', $response['error']);
        $this->assertEquals(404, $response['error']['code']);

        $this->assertArrayHasKey('message', $response['error']);
        $this->assertEquals('Not Found: invalid receiver', $response['error']['message']);
    }
}
