<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProviderOrderReceived implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $providerUserId,
        public readonly int $assignmentId,
        public readonly string $serviceId,
        public readonly string $serviceType,
        public readonly float $distanceKm,
        public readonly float $matchingScore,
    ) {}

    /**
     * @return array<Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('users.'.$this->providerUserId)];
    }

    public function broadcastAs(): string
    {
        return 'provider.order.received';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'provider_order_received',
            'message' => 'There is a new order for you.',
            'assignment_id' => $this->assignmentId,
            'service_id' => $this->serviceId,
            'service_type' => $this->serviceType,
            'distance_km' => $this->distanceKm,
            'matching_score' => $this->matchingScore,
        ];
    }
}
