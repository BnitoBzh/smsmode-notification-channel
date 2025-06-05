<?php

namespace BnitoBzh\Notifications\Tests\Unit\Messages;

use BnitoBzh\Notifications\Messages\SmsmodeMessage;
use DateTimeImmutable;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Illuminate\Support\Str;

class SmsmodeMessageTest extends TestCase
{
    public function testConstructor()
    {
        // Test default constructor
        $message = new SmsmodeMessage();
        $this->assertEmpty($message->content);
        $this->assertEquals(SmsmodeMessage::ENCODING_GSM7, $message->encoding);
        $this->assertFalse($message->stop);

        // Test constructor with parameters
        $message = new SmsmodeMessage('Test content', SmsmodeMessage::ENCODING_UNICODE, true);
        $this->assertEquals('Test content', $message->content);
        $this->assertEquals(SmsmodeMessage::ENCODING_UNICODE, $message->encoding);
        $this->assertTrue($message->stop);
    }

    public function testContent()
    {
        $message = new SmsmodeMessage();
        $result = $message->content('Hello world');
        
        $this->assertEquals('Hello world', $message->content);
        $this->assertSame($message, $result);
    }

    public function testStop()
    {
        $message = new SmsmodeMessage();
        $result = $message->stop(true);
        
        $this->assertTrue($message->stop);
        $this->assertSame($message, $result);
    }

    public function testEncoding()
    {
        $message = new SmsmodeMessage();
        
        $result = $message->encoding(SmsmodeMessage::ENCODING_UNICODE);
        $this->assertEquals(SmsmodeMessage::ENCODING_UNICODE, $message->encoding);
        $this->assertSame($message, $result);
        
        $result = $message->encoding(SmsmodeMessage::ENCODING_GSM7);
        $this->assertEquals(SmsmodeMessage::ENCODING_GSM7, $message->encoding);
        $this->assertSame($message, $result);
    }

    public function testEncodingWithInvalidValue()
    {
        $message = new SmsmodeMessage();
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid encoding');
        
        $message->encoding('INVALID_ENCODING');
    }

    public function testUnicode()
    {
        $message = new SmsmodeMessage();
        $result = $message->unicode();
        
        $this->assertEquals(SmsmodeMessage::ENCODING_UNICODE, $message->encoding);
        $this->assertSame($message, $result);
    }

    public function testGsm7()
    {
        $message = new SmsmodeMessage();
        $message->encoding(SmsmodeMessage::ENCODING_UNICODE);
        $result = $message->gsm7();
        
        $this->assertEquals(SmsmodeMessage::ENCODING_GSM7, $message->encoding);
        $this->assertSame($message, $result);
    }

    public function testReference()
    {
        $message = new SmsmodeMessage();
        $result = $message->reference('REF123');
        
        $this->assertEquals('REF123', $message->reference);
        $this->assertSame($message, $result);
    }

    public function testReferenceWithTooShortValue()
    {
        $message = new SmsmodeMessage();
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid client reference');
        
        $message->reference('AB');
    }

    public function testReferenceWithTooLongValue()
    {
        $message = new SmsmodeMessage();
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid client reference');
        
        $message->reference(str_repeat('A', 141));
    }

    public function testSender()
    {
        $message = new SmsmodeMessage();
        $result = $message->sender('COMPANY');
        
        $this->assertEquals('COMPANY', $message->sender);
        $this->assertSame($message, $result);
    }

    public function testSenderWithTooShortValue()
    {
        $message = new SmsmodeMessage();
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid sender ID');
        
        $message->sender('');
    }

    public function testSenderWithTooLongValue()
    {
        $message = new SmsmodeMessage();
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('invalid sender ID');
        
        $message->sender('ABCDEFGHIJKL'); // 12 chars
    }

    public function testDate()
    {
        $message = new SmsmodeMessage();
        $date = (new DateTimeImmutable())->modify('+1 day');
        $result = $message->date($date);
        
        $this->assertEquals($date, $message->date);
        $this->assertSame($message, $result);
    }

    public function testDateWithPastDate()
    {
        $message = new SmsmodeMessage();
        $date = (new DateTimeImmutable())->modify('-1 day');
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Future date no more than 10 years older than now');
        
        $message->date($date);
    }

    public function testDateWithTooFutureDate()
    {
        $message = new SmsmodeMessage();
        $date = (new DateTimeImmutable())->modify('+11 years');
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Future date no more than 10 years older than now');
        
        $message->date($date);
    }

    public function testCallbackUrl()
    {
        $message = new SmsmodeMessage();
        $url = 'https://example.com/callback';
        $result = $message->callbackUrl($url);
        
        $this->assertEquals($url, $message->callbackUrl);
        $this->assertSame($message, $result);
    }

    public function testCallbackUrlWithTooLongValue()
    {
        $message = new SmsmodeMessage();
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('URL too long');
        
        $message->callbackUrl(str_repeat('a', 256));
    }

    public function testCallbackMOUrl()
    {
        $message = new SmsmodeMessage();
        $url = 'https://example.com/mo-callback';
        $result = $message->callbackMOUrl($url);
        
        $this->assertEquals($url, $message->callbackMOUrl);
        $this->assertSame($message, $result);
    }

    public function testCallbackMOUrlWithTooLongValue()
    {
        $message = new SmsmodeMessage();
        
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('URL too long');
        
        $message->callbackMOUrl(str_repeat('a', 256));
    }

    public function testChannel()
    {
        $message = new SmsmodeMessage();
        $uuid = Str::uuid();
            
        $result = $message->channel($uuid);
        
        $this->assertEquals($uuid, $message->channel);
        $this->assertSame($message, $result);
    }

    public function testChannelWithInvalidUuid()
    {
        $message = new SmsmodeMessage();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid channel ID');
        
        $message->channel('invalid-uuid');
    }

    public function testCampaign()
    {
        $message = new SmsmodeMessage();
        $uuid = Str::uuid();

        $result = $message->campaign($uuid);
        
        $this->assertEquals($uuid, $message->campaign);
        $this->assertSame($message, $result);
    }

    public function testCampaignWithInvalidUuid()
    {
        $message = new SmsmodeMessage();
            
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid campaign ID');
        
        $message->campaign('invalid-uuid');
    }
}