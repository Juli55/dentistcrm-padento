<?php

namespace App\Events;

use App\Events\Event;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class PatientFormFilled extends Event
{
    use SerializesModels;

    public $email;
    public $name;
    public $plz;
    public $patient_id;

    /**
     * Create a new event instance.
     *
     * @param $name
     * @param $email
     * @param $plz
     * @param $patient_id
     */
    public function __construct($name, $email, $plz, $patient_id)
    {
        $this->email = $email;
        $this->name = $name;
        $this->plz = $plz;
        $this->patient_id = $patient_id;
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
