<?php

namespace League\OAuth2\Client\Test\Provider;

use League\OAuth2\Client\Provider\ClockInterface;


/**
 * A clock which must be initialised, and may be changed at any time.
 */
class ProgrammableClock implements ClockInterface
{

    /**
     * @var \DateTimeImmutable|null
     */
    protected $time = null;

    /**
     * @inheritdoc
     */
    public function now()
    {
        if (!isset($this->time)) {
            throw new \LogicException('Time must be set explicitly');
        }
        return $this->time;
    }

    /**
     * Sets the current time.
     *
     * @param \DateTimeImmutable|null the current time.
     * @return self
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * @param int $time seconds since epoch
     * @return self
     */
    public static function newFromTimestamp($time)
    {
        return (new self())
            ->setTime(new \DateTimeImmutable('@' . $time));
    }
}
