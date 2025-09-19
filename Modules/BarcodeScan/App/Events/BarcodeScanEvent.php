<?php

namespace Modules\BarcodeScan\App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\SerializesModels;

class BarcodeScanEvent implements ShouldBroadcast, ShouldQueue
{
    use SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        protected $barcode
    )
    {
        //
    }

    /**
     * Get the channels the event should be broadcast on.
     */
    public function broadcastOn(): array
    {
        $channels = [
            new Channel('barcodescan'),
        ];
        $users = User::all();

        foreach ($users as $user) {
            array_push($channels, new PrivateChannel('App.Models.User.' . $user->id));
        }

        return $channels;
    }

    public function broadcastWith()
    {
        return [
            'barcode' => $this->barcode->toArray(),
        ];
    }

    public function broadcastAs()
    {
        return 'BarcodeLogCreated';
    }

}
