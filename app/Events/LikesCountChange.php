<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LikesCountChange implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $likes;
    /**
     * Create a new event instance.
     */
    public function __construct($id, $type, $request, $response)
    {
        $user = (object)[];
        $user->id = $request->user()->id;
        $user->name = $request->user()->name;
        $user->image = $request->user()->image;
        $user->last_action = $request->user()->last_action;

        $data = (object)[];
        $data->likeable_id = $id;
        $data->likeable_type = $type;
        $data->user = $user;
        $data->response = $response;

        $this->likes = $data;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('likes-count-change'),
        ];
    }
}