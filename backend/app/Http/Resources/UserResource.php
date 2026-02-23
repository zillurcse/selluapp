<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'vendor_id' => $this->vendor_id,
            'roles' => $this->roles->map(fn($role) => [
                'id' => $role->id,
                'name' => $role->name,
            ]),
            'permissions' => $this->getAllPermissions()->pluck('name'),
            'vendor_profile' => $this->when($this->shop_profile, function () {
                $profile = $this->shop_profile;
                return [
                    'id' => $profile->id,
                    'store_name' => $profile->store_name,
                    'store_slug' => $profile->store_slug,
                    'logo' => $profile->logo,
                    'package' => $profile->package ? [
                        'id' => $profile->package->id,
                        'name' => $profile->package->name,
                        'pos_access' => (bool)$profile->package->pos_access,
                        'hrm_access' => (bool)$profile->package->hrm_access,
                        'product_limit' => $profile->package->product_limit,
                        'order_limit' => $profile->package->order_limit,
                        'employee_limit' => $profile->package->employee_limit,
                        'features' => $profile->package->features,
                    ] : null,
                ];
            }),
            // For staff users: include owner's profile with package for frontend feature checks
            'owner' => $this->when($this->vendor_id && $this->owner, function () {
                $owner = $this->owner;
                $profile = $owner->vendorProfile;
                return [
                    'id' => $owner->id,
                    'name' => $owner->name,
                    'vendor_profile' => $profile ? [
                        'id' => $profile->id,
                        'store_name' => $profile->store_name,
                        'store_slug' => $profile->store_slug,
                        'logo' => $profile->logo,
                        'package' => $profile->package ? [
                            'id' => $profile->package->id,
                            'name' => $profile->package->name,
                            'pos_access' => (bool)$profile->package->pos_access,
                            'hrm_access' => (bool)$profile->package->hrm_access,
                            'product_limit' => $profile->package->product_limit,
                            'order_limit' => $profile->package->order_limit,
                            'employee_limit' => $profile->package->employee_limit,
                            'features' => $profile->package->features,
                        ] : null,
                    ] : null,
                ];
            }),
        ];
    }
}
