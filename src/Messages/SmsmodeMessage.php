<?php

namespace BnitoBzh\Notifications\Messages;

use DateTimeImmutable;
use DateTimeInterface;
use Illuminate\Support\Str;
use InvalidArgumentException;

class SmsmodeMessage
{
    public const ENCODING_GSM7 = 'GSM7';
    public const ENCODING_UNICODE = 'UNICODE';

    /**
     * Text body of SMS
     */
    public string $content = '';

    /**
     * Encoding of SMS text, "GSM7" or "UNICODE"
     */
    public string $encoding = self::ENCODING_GSM7;

    /**
     * Inject STOP keyword (for marketing campaigns)
     */
    public bool $stop = false;

    /**
     * Field allowing to tag the message with a reference ID
     */
    public string|null $reference = null;

    /**
     * The sender ID. Alphanumeric, short code or virtual mobile number.
     * Sender ID must be authorized by the platform
     */
    public string|null $sender = null;

    /**
     * Date when the message has been sent or will be sent by the smsmode platform
     */
    public DateTimeInterface|null $date = null;

    /**
     * URL of endpoint, which will be called when getting a delivery report
     */
    public string|null $callbackUrl = null;

    /**
     * URL of endpoint, which will be called when getting an incoming message
     */
    public string|null $callbackMOUrl = null;

    /**
     * UUID of Campaign through the Message is sent.
     */
    public string|null $campaign = null;

    /**
     * UUID of Channel through the Message is sent.
     */
    public string|null $channel = null;

    public function __construct(string $content = '', string $encoding = self::ENCODING_GSM7, bool $stop = false)
    {
        $this->content = $content;
        $this->encoding = $encoding;
        $this->stop = $stop;
    }

    public function content(string $content): self
    {
        $this->content = $content;
        return $this;
    }

    public function stop(bool $stop): self
    {
        $this->stop = $stop;
        return $this;
    }

    public function encoding(string $encoding): self
    {
        if ($encoding !== self::ENCODING_GSM7 && $encoding !== self::ENCODING_UNICODE) {
            throw new InvalidArgumentException('Invalid encoding');
        }

        $this->encoding = $encoding;
        return $this;
    }

    public function unicode(): self
    {
        $this->encoding = self::ENCODING_UNICODE;
        return $this;
    }

    public function gsm7(): self
    {
        $this->encoding = self::ENCODING_GSM7;
        return $this;
    }

    public function reference(string $reference): self
    {
        if (strlen($reference) > 140 || strlen($reference) < 3) {
            throw new InvalidArgumentException('Invalid client reference');
        }

        $this->reference = $reference;
        return $this;
    }

    public function sender(string $from): self
    {
        if (strlen($from) > 11 || strlen($from) < 1) {
            throw new InvalidArgumentException('invalid sender ID');
        }

        $this->sender = $from;
        return $this;
    }

    public function date(DateTimeInterface $date): self
    {
        $now = new DateTimeImmutable();
        $tenYearsLater = $now->modify('+10 years');

        if ($date <= $now || $date > $tenYearsLater) {
            throw new InvalidArgumentException('Future date no more than 10 years older than now');
        }

        $this->date = $date;
        return $this;
    }

    public function callbackUrl(string $callbackUrl): self
    {
        if (strlen($callbackUrl) > 255) {
            throw new InvalidArgumentException('URL too long');
        }

        $this->callbackUrl = $callbackUrl;
        return $this;
    }

    public function callbackMOUrl(string $callbackUrl): self
    {
        if (strlen($callbackUrl) > 255) {
            throw new InvalidArgumentException('URL too long');
        }

        $this->callbackMOUrl = $callbackUrl;
        return $this;
    }

    public function channel(string $channel): self
    {
        if (!Str::isUuid($channel)) {
            throw new InvalidArgumentException('Invalid channel ID');
        }

        $this->channel = $channel;
        return $this;
    }

    public function campaign(string $campaign): self
    {
        if (!Str::isUuid($campaign)) {
            throw new InvalidArgumentException('Invalid campaign ID');
        }

        $this->campaign = $campaign;
        return $this;
    }
}
