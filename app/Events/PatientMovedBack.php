<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PatientMovedBack extends Event
{
    use SerializesModels;

    public $patient;

    /**
     * Create a new event instance.
     *
     * @param $patient
     */
    public function __construct($patient)
    {
        $this->patient = $patient;
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
