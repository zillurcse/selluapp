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

        $data['image'] = $this->image ? $this->image:'https://pub-bce20a7f7a44490793d27b05e814fbc4.r2.dev/uploads/5/U62TvMwCw3yiu0BOaR0cTg5PkcBEdER9diqubHHi.png';
        $data['thumbnail'] = $this->thumbnail ? $this->thumbnail:'https://pub-bce20a7f7a44490793d27b05e814fbc4.r2.dev/uploads/5/U62TvMwCw3yiu0BOaR0cTg5PkcBEdER9diqubHHi.png';

        if ($this->gallery) {
            $data['gallery_urls'] = array_map(function ($path) {
                return $path;
            }, $this->gallery);
        }

        if (isset($data['vendor']['vendor_profile'])) {
            $profile = $this->vendor->vendorProfile;
            $data['vendor']['vendor_profile']['logo_url'] = $profile->logo ?? $profile->logo;
            $data['vendor']['vendor_profile']['banner_url'] = $profile->banner ?? $profile->banner;
        }

        if (isset($data['variants'])) {
            foreach ($data['variants'] as $key => $variant) {
                $data['variants'][$key]['image_url'] = $variant['image'] ? $variant['image'] : 'https://pub-bce20a7f7a44490793d27b05e814fbc4.r2.dev/uploads/5/U62TvMwCw3yiu0BOaR0cTg5PkcBEdER9diqubHHi.png';
            }
        }

        return $data;
    }
}
