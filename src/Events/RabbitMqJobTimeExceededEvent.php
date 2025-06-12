<?php

namespace Housfy\LaravelQueueRabbitMQ\Events;

use Exception;
use Illuminate\Queue\Jobs\Job;

class RabbitMqJobTimeExceededEvent
{
    public function __construct(
        public int $status = 0,
        public Exception $exception,
        public ?Job $job,
        public float $elapsedTime = 0,
    ) {
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getJob(): ?Job
    {
        return $this->job;
    }

    public function exception(): Exception
    {
        return $this->exception;
    }

    // Elapsed time (in seconds) that the worker took from the moment it started running
    // until it exceeded the maximum allowed time.
    public function elapsedTime(): float
    {
        return $this->elapsedTime;
    }
}