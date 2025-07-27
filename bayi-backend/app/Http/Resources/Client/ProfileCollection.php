<?php

namespace App\Http\Resources\Client;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ProfileCollection extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var User $this */

        $data = [
            "fullname" => $this->fullname ?? "",
            "email" => $this->email ?? "",
        ];

        return $data;
    }
}
