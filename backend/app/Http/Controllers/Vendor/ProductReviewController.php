<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductReviewController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:website.view|settings.view', only: ['stats']),
        ];
    }

    public function stats(Request $request)
    {
        $userId = Auth::id();

        $baseQuery = ProductReview::where('user_id', $userId);

        $totalReviews = (clone $baseQuery)->count();
        $pendingReviews = (clone $baseQuery)->where('status', 'pending')->count();
        $averageRating = (clone $baseQuery)->where('status', 'approved')->avg('rating');

        return response()->json([
            'status' => 'success',
            'data' => [
                'total_reviews' => $totalReviews,
                'pending_reviews' => $pendingReviews,
                'average_rating' => $averageRating ? round((float) $averageRating, 1) : 0,
            ],
        ]);
    }
}
