<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NewPatientDate extends Event
{
    use SerializesModels;

    public $date;

    /**
     * Create a new event instance.
     *
     * @param $date
     */
    public function __construct($date)
    {
        $this->date = $date;
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
