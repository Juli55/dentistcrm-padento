<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ValidationFailed extends Event
{
    use SerializesModels;

    /**
     * @var
     */
    public $input;

    /**
     * Create a new event instance.
     */
    public function __construct($input)
    {
        $this->input = $input;
    }

    /**
     * Get the channels the event should be broadcast on.
     *
     * @return array
     */
    public function broadcastOn()
    {
        return [];
    }
}
