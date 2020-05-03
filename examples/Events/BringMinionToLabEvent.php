<?php

namespace Luezoid\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Luezoid\Models\Mission;

class BringMinionToLabEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     *
     * @param Mission $mission
     */
    public function __construct(Mission $mission)
    {
        // do the magic here to bring the Minion to Lab; Maybe tell Agnes & she'll get the reason to grab the Minion & present him in fron of Gru :p
        Log::info("All right! Bringing Sergeant '{$mission->lead_by->name}' to Gru's Lab for mission '{$mission->name}' at your command at once..");
    }
}
