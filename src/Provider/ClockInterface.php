<?php

namespace League\OAuth2\Client\Provider;

/**
 * A common interface for accessing the clock.
 *
 * NOTE: We don't have a return type hint because this library still supports PHP 5.6.
 * @see https://github.com/php-fig/fig-standards/blob/master/proposed/clock.md
 */
interface ClockInterface
{
    /**
     * Return the current time as a DateTimeImmutable Object.
     *
     * @return \DateTimeImmutable
     */
    public function now(); /* \DateTimeImmutable */
}
