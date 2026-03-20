<?php

namespace App\Http\Resources\Storefront;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $data = parent::toArray($request);
        $data['price'] = $this->sale_price;
        $data['rating'] = $this->reviews_avg_rating ? number_format($this->reviews_avg_rating, 1) : null;
        $data['reviews_count'] = $this->reviews_count ?? 0;

        $data['image'] = $this->formatImageUrl($this->image);
        $data['thumbnail'] = $this->formatImageUrl($this->thumbnail);

        if ($this->gallery) {
            $data['gallery'] = array_map(function ($path) {
                return $this->formatImageUrl($path);
            }, $this->gallery);
        }

        if (isset($data['vendor']['vendor_profile'])) {
            $profile = $this->vendor->vendorProfile;
            if ($profile) {
                $data['vendor']['vendor_profile']['logo_url'] = $this->formatImageUrl($profile->logo);
                $data['vendor']['vendor_profile']['banner_url'] = $this->formatImageUrl($profile->banner);
            }
        }

        if (isset($data['variants'])) {
            foreach ($data['variants'] as $key => $variant) {
                $data['variants'][$key]['image_url'] = $this->formatImageUrl($variant['image'] ?? null);
            }
        }

        return $data;
    }

    /**
     * Format the image URL to be fully qualified.
     *
     * @param string|null $path
     * @return string
     */
    private function formatImageUrl($path)
    {
        if (!$path) {
            return 'https://pub-bce20a7f7a44490793d27b05e814fbc4.r2.dev/uploads/5/U62TvMwCw3yiu0BOaR0cTg5PkcBEdER9diqubHHi.png';
        }

        if (\Illuminate\Support\Str::startsWith($path, ['http://', 'https://'])) {
            return $path;
        }

        return \Illuminate\Support\Facades\Storage::disk('public')->url($path);
    }
}
