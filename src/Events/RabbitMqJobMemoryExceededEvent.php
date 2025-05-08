<?php

namespace Housfy\LaravelQueueRabbitMQ\Events;

use Exception;
use Illuminate\Queue\Jobs\Job;

class RabbitMqJobMemoryExceededEvent
{
    /**
     * Create a new event instance.
     *
     * @param int $status
     * @param Job|null $job
     * @param Exception $exception
     */
    public function __construct(
        public int $status = 0,
        public Exception $exception,
        public ?Job $job,
        public int $memoryUsedInBytes,
    ) {
        $this->status = $status;
        $this->job = $job;
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

    public function memoryUsedInBytes(): int
    {
        return $this->memoryUsedInBytes;
    }
}