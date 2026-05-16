<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CustomerOrderAccepted implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public readonly string $elderUserId,
        public readonly string $serviceId,
        public readonly int $assignmentId,
        public readonly string $providerType,
        public readonly string $providerName,
        public readonly string $providerPhone,
        public readonly float $distanceKm,
    ) {}

    /**
     * @return array<Channel>
     */
    public function broadcastOn(): array
    {
        return [new PrivateChannel('users.'.$this->elderUserId)];
    }

    public function broadcastAs(): string
    {
        return 'customer.order.accepted';
    }

    /**
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'type' => 'customer_order_accepted',
            'message' => 'A '.$this->providerType.' accepted your order.',
            'service_id' => $this->serviceId,
            'assignment_id' => $this->assignmentId,
            'provider' => [
                'type' => $this->providerType,
                'name' => $this->providerName,
                'phone' => $this->providerPhone,
                'distance_km' => $this->distanceKm,
            ],
        ];
    }
}
