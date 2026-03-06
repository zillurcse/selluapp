<?php

namespace App\Events\Automation;

use App\Models\ProductReview;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReviewSubmitted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $review;

    /**
     * Create a new event instance.
     */
    public function __construct(ProductReview $review)
    {
        $this->review = $review;
    }
}
