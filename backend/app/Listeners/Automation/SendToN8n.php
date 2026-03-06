<?php

namespace App\Listeners\Automation;

use App\Events\Automation\OrderPlaced;
use App\Events\Automation\ReviewSubmitted;
use App\Events\Automation\MessageReceived;
use App\Services\Automation\WebhookService;

class SendToN8n
{
    public function __construct(protected WebhookService $webhookService) {}

    public function handle(object $event): void
    {
        if ($event instanceof OrderPlaced) {
            $this->webhookService->dispatch(
                $event->order->user_id,
                'order_placed',
                $event->order->load(['items.product', 'customer'])->toArray()
            );
        }

        if ($event instanceof ReviewSubmitted) {
            $this->webhookService->dispatch(
                $event->review->user_id,
                'review_submitted',
                $event->review->load(['product', 'customer'])->toArray()
            );
        }

        if ($event instanceof MessageReceived) {
            $this->webhookService->dispatch(
                $event->message->user_id,
                'message_received',
                $event->message->toArray()
            );
        }
    }
}
