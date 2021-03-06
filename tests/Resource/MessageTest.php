<?php

use Beepsend\Client;
use Beepsend\Connector\Curl;

class MessageTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test sending messages
     */
    public function testSending() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/send/', 'POST', [
                        'from' => 'Beepsend',
                        'to' => '46736007518',
                        'message' => 'Hello World! 你好世界!',
                        'encoding' => 'UTF-8'
                    ])
                    ->once()
                    ->andReturn([
                        'info' => [
                            'http_code' => 200,
                            'Content-Type' => 'application/json'
                        ],
                        'response' => json_encode([
                            'id' => ['07595980013893439611559146736007518'],
                            'to' => '46736007518',
                            'from' => 'Beepsend'
                        ])
                    ]);

        $client = new Client('abc123', $connector);
        $message = $client->message->send('46736007518', 'Beepsend', 'Hello World! 你好世界!');

        $this->assertInternalType('array', $message);
        $this->assertEquals(['07595980013893439611559146736007518'], $message['id']);
        $this->assertEquals('46736007518', $message['to']);
        $this->assertEquals('Beepsend', $message['from']);
    }

    /**
     * Test sending messages to groups
     */
    public function testGroup() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/sendouts/', 'POST', [
                        'sms'=> [
                            'from'=>'beepsend',
                            'groups'=> [1,2],
                            'body'=>'you rock!',
                            'encoding'=>'UTF-8'
                            ]
                        ])
                    ->once()
                    ->andReturn([
                        'info' => [
                            'http_code' => 201,
                            'Content-Type' => 'application/json'
                        ],
                        'response' => json_encode([
                        'sms' => [
                            'from' => 'beepsend',
                            'groups' => [1,2],
                            'body' => 'You rock!',
                            "encoding" => 'UTF-8'
                            ]
                        ])
                    ]);

        $client = new Client('abc123', $connector);
        $message = $client->message->group([1,2], 'beepsend', 'you rock!');

        $this->assertInternalType('array', $message);
        $this->assertInternalType('array', $message['sms']['groups']);
        $this->assertEquals('beepsend', $message['sms']['from']);
    }

    /**
     * Test sending messages to groups and multiple recipients
     */
    public function testSendouts() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/sendouts/', 'POST', [
                        'sms'=> [
                            'from'=>'beepsend',
                            'groups'=> [1,2],
                            'to'=> ['46702123456', '46702789456'],
                            'body'=>'Lets talk about honey badgers!',
                            'encoding'=>'UTF-8'
                            ]
                        ])
                    ->once()
                    ->andReturn([
                        'info' => [
                            'http_code' => 201,
                            'Content-Type' => 'application/json'
                        ],
                        'response' => json_encode([
                        'sms' => [
                            'from' => 'beepsend',
                            'groups'=> [1,2],
                            'to'=> ['46702123456', '46702789456'],
                            'body'=>'Lets talk about honey badgers!',
                            "encoding" => 'UTF-8'
                            ]
                        ])
                    ]);

        $client = new Client('abc123', $connector);
        $message = $client->message->sendouts([1,2], ['46702123456', '46702789456'],'beepsend', 'Lets talk about honey badgers!');
        $this->assertInternalType('array', $message);
        $this->assertInternalType('array', $message['sms']['groups']);
        $this->assertEquals('beepsend', $message['sms']['from']);
    }

    /**
     * Test sending binary messages
     */
    public function testBinary() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/send/', 'POST', [
                        'from' => 'Beepsend',
                        'to' => '46736007518',
                        'message' => 'Binaryworld',
                        'message_type' => 'binary'
                    ])
                    ->once()
                    ->andReturn([
                        'info' => [
                            'http_code' => 200,
                            'Content-Type' => 'application/json'
                        ],
                        'response' => json_encode([
                            'id' => ['07595980013893439611559146736007518'],
                            'to' => '46736007518',
                            'from' => 'Beepsend',
                        ])
                    ]);

        $client = new Client('abc123', $connector);
        $message = $client->message->binary('46736007518', 'Beepsend', 'Binaryworld');

        $this->assertInternalType('array', $message);
        $this->assertEquals(['07595980013893439611559146736007518'], $message['id']);
        $this->assertEquals('46736007518', $message['to']);
        $this->assertEquals('Beepsend', $message['from']);
    }

    /**
     * Test getting details of sent messages
     */
    public function testLookup() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/sms/12345', 'GET', array())
                    ->once()
                    ->andReturn(array(
                        'info' => array(
                            'http_code' => 200,
                            'Content-Type' => 'application/json'
                        ),
                        'response' => json_encode(array(
                            'id' => 12345,
                            'to' => array(
                                'address' => 46736007518,
                                'ton' => 1,
                                'npi' => 1
                            ),
                            'from' => array(
                                'address' => 'Beepsend',
                                'ton' => 1,
                                'npi' => 1
                            ),
                            'dlr' => array(
                                'status' => 'DELIVRD',
                                'error' => 0
                            ),
                            'price' => 0.068
                        ))
                    ));

        $client = new Client('abc123', $connector);
        $message = $client->message->lookup(12345);

        $this->assertInternalType('array', $message);
        $this->assertEquals(12345, $message['id']);
        $this->assertEquals(46736007518, $message['to']['address']);
        $this->assertEquals('Beepsend', $message['from']['address']);
        $this->assertEquals('DELIVRD', $message['dlr']['status']);
        $this->assertEquals(0.068, $message['price']);
    }

    /**
     * Test getting details for multiple messages
     */
    public function testMultipleLookup() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/sms/', 'GET', array())
                    ->once()
                    ->andReturn(array(
                        'info' => array(
                            'http_code' => 200,
                            'Content-Type' => 'application/json'
                        ),
                        'response' => json_encode(array(
                            array(
                                'id' => 12345,
                                'to' => array(
                                    'address' => 46736007518,
                                    'ton' => 1,
                                    'npi' => 1
                                ),
                                'from' => array(
                                    'address' => 'Beepsend',
                                    'ton' => 1,
                                    'npi' => 1
                                ),
                                'dlr' => array(
                                    'status' => 'DELIVRD',
                                    'error' => 0
                                ),
                                'price' => 0.068
                            )
                        ))
                    ));

        $client = new Client('abc123', $connector);
        $message = $client->message->multipleLookup();

        $this->assertInternalType('array', $message);
        $this->assertEquals(12345, $message[0]['id']);
        $this->assertEquals(46736007518, $message[0]['to']['address']);
        $this->assertEquals('Beepsend', $message[0]['from']['address']);
        $this->assertEquals('DELIVRD', $message[0]['dlr']['status']);
        $this->assertEquals(0.068, $message[0]['price']);
    }

    /**
     * Test getting batches
     */
    public function testBatches() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/sendouts/', 'GET', array())
                    ->once()
                    ->andReturn(array(
                        'info' => array(
                            'http_code' => 200,
                            'Content-Type' => 'application/json'
                        ),
                        'response' => json_encode(array(
                            array(
                                'id' => 3,
                                'label' => 'My custom name for my batch',
                                'date_created' => 1386777418,
                                'last_used' => 1387442294
                            ),
                            array(
                                'id' => 4,
                                'label' => 'batch testing',
                                'date_created' => 1387457467,
                                'last_used' => 1387457467
                            )
                        ))
                    ));

        $client = new Client('abc123', $connector);
        $message = $client->message->getSendouts();

        $this->assertInternalType('array', $message);
        $this->assertEquals(3, $message[0]['id']);
        $this->assertEquals('My custom name for my batch', $message[0]['label']);
        $this->assertEquals(1386777418, $message[0]['date_created']);
        $this->assertEquals(1387442294, $message[0]['last_used']);
        $this->assertEquals(4, $message[1]['id']);
        $this->assertEquals('batch testing', $message[1]['label']);
        $this->assertEquals(1387457467, $message[1]['date_created']);
        $this->assertEquals(1387457467, $message[1]['last_used']);
    }

    /**
     * Test getting paginated overview of messages in a batch, complete with sent and recieved message body.
     */
    public function testTwoWayBatches() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/batches/123/messages/', 'GET', array(
                        'count' => 200,
                        'offset' => 0
                    ))
                    ->once()
                    ->andReturn(array(
                        'info' => array(
                            'http_code' => 200,
                            'Content-Type' => 'application/json'
                        ),
                        'response' => json_encode(array(
                            array(
                                'mt_sms_id' => 889000680270500421,
                                'mt_body' => 'What is your name?',
                                'mo_sms_id' => 889000680270500422,
                                'mo_body' => 'Sir Lancelot',
                                'dlr_stat' => 'DELIVRD'
                            )
                        ))
                    ));

        $client = new Client('abc123', $connector);
        $messages = $client->message->twoWayBatch(123);

        $this->assertInternalType('array', $messages);
        $this->assertEquals(889000680270500421, $messages[0]['mt_sms_id']);
        $this->assertEquals('What is your name?', $messages[0]['mt_body']);
        $this->assertEquals(889000680270500422, $messages[0]['mo_sms_id']);
        $this->assertEquals('Sir Lancelot', $messages[0]['mo_body']);
        $this->assertEquals('DELIVRD', $messages[0]['dlr_stat']);
    }

    /**
     * Test call for estimation
     */
    public function testEstimationCost() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/sms/costestimate/', 'POST', array(
                        'to' => 46736007518,
                        'message' => 'Hello World! 你好世界!',
                        'encoding' => 'UTF-8'
                    ))
                    ->once()
                    ->andReturn(array(
                        'info' => array(
                            'http_code' => 200,
                            'Content-Type' => 'application/json'
                        ),
                        'response' => json_encode(array(
                            'total_cost' => 0.013,
                            'to' => array(46736007518)
                        ))
                    ));

        $client = new Client('abc123', $connector);
        $message = $client->message->estimateCost(46736007518, 'Hello World! 你好世界!');

        $this->assertInternalType('array', $message);
        $this->assertEquals(0.013, $message['total_cost']);
        $this->assertEquals(46736007518, $message['to'][0]);
    }

    /**
     * Test call for group estimation
     */
    public function testGroupEstimationCost() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/sms/costestimate/', 'POST', array(
                        'groups' => array(11, 34),
                        'message' => 'Hello World! 你好世界!',
                        'encoding' => 'UTF-8'
                    ))
                    ->once()
                    ->andReturn(array(
                        'info' => array(
                            'http_code' => 200,
                            'Content-Type' => 'application/json'
                        ),
                        'response' => json_encode(array(
                            'total_cost' => 358.57,
                            'groups' => array(
                                11 => 13.45,
                                34 => 345.12
                            )
                        ))
                    ));

        $client = new Client('abc123', $connector);
        $message = $client->message->estimateCostGroup(array(11, 34), 'Hello World! 你好世界!');

        $this->assertInternalType('array', $message);
        $this->assertEquals(358.57, $message['total_cost']);
        $this->assertEquals(13.45, $message['groups'][11]);
        $this->assertEquals(345.12, $message['groups'][34]);
    }

    /**
     * Test listing your user conversations
     */
    public function testConversations() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/conversations/', 'GET', array())
                    ->once()
                    ->andReturn(array(
                        'info' => array(
                            'http_code' => 200,
                            'Content-Type' => 'application/json'
                        ),
                        'response' => json_encode(array(
                            array(
                                'id' => '46736007500-46736000005',
                                'to' => 46736007500,
                                'from' => array(
                                    'number' => 46736000005,
                                    'contact' => array(
                                        'id' => 10,
                                        'firstname' => 'Foo',
                                        'lastname' => 'Bar'
                                    )
                                ),
                                'body' => 'Hi. This is a test message',
                                'timestamp' => 1383225355
                            )
                        ))
                    ));

        $client = new Client('abc123', $connector);
        $conversations = $client->message->conversations();

        $this->assertInternalType('array', $conversations);
        $this->assertEquals('46736007500-46736000005', $conversations[0]['id']);
        $this->assertEquals(46736007500, $conversations[0]['to']);
        $this->assertEquals(46736000005, $conversations[0]['from']['number']);
        $this->assertEquals(10, $conversations[0]['from']['contact']['id']);
        $this->assertEquals('Foo', $conversations[0]['from']['contact']['firstname']);
        $this->assertEquals('Bar', $conversations[0]['from']['contact']['lastname']);
        $this->assertEquals('Hi. This is a test message', $conversations[0]['body']);
        $this->assertEquals(1383225355, $conversations[0]['timestamp']);
    }

    /**
     * Test list all messages sent back and forth in to a single contact/number.
     */
    public function testFullConversations() {
        $connector = \Mockery::mock(new Curl());
        $connector->shouldReceive('call')
                    ->with(BASE_API_URL . '/' . API_VERSION . '/conversations/123', 'GET', array())
                    ->once()
                    ->andReturn(array(
                        'info' => array(
                            'http_code' => 200,
                            'Content-Type' => 'application/json'
                        ),
                        'response' => json_encode(array(
                            array(
                                'id' => '46736007500-46736000005',
                                'to' => 46736007500,
                                'from' => array(
                                    'number' => 46736000005,
                                    'contact' => array(
                                        'id' => 10,
                                        'firstname' => 'Foo',
                                        'lastname' => 'Bar'
                                    )
                                ),
                                'body' => 'Hi. This is a test message',
                                'timestamp' => 1383225355,
                                'items' => array(
                                    'id' => 12345,
                                    'batch' => null,
                                    'body' => 'Hello world!'
                                )
                            )
                        ))
                    ));

        $client = new Client('abc123', $connector);
        $conversations = $client->message->fullConversation('123');

        $this->assertInternalType('array', $conversations);
        $this->assertEquals('46736007500-46736000005', $conversations[0]['id']);
        $this->assertEquals(46736007500, $conversations[0]['to']);
        $this->assertEquals(46736000005, $conversations[0]['from']['number']);
        $this->assertEquals(10, $conversations[0]['from']['contact']['id']);
        $this->assertEquals('Foo', $conversations[0]['from']['contact']['firstname']);
        $this->assertEquals('Bar', $conversations[0]['from']['contact']['lastname']);
        $this->assertEquals('Hi. This is a test message', $conversations[0]['body']);
        $this->assertEquals(1383225355, $conversations[0]['timestamp']);
        $this->assertEquals(12345, $conversations[0]['items']['id']);
        $this->assertEquals(null, $conversations[0]['items']['batch']);
        $this->assertEquals('Hello world!', $conversations[0]['items']['body']);
    }

    public function tearDown() {
        \Mockery::close();
    }

}