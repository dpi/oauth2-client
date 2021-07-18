<?php

namespace League\OAuth2\Client\Provider;

interface ClockAwareInterface
{
    /**
     * Get the clock.
     *
     * @return ClockInterface
     */
    public function getClock();
}
