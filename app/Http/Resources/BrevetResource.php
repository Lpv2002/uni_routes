<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\ImagenController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class BrevetResource extends JsonResource
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
            'nro' => $this->nro,
            'expiration_date' => $this->expiration_date,
            'broadcast_date' => $this->broadcast_date,
            'category' => $this->category,
            'photo' => ImagenController::show('brevet/', $this->photo),
        ];
    }
}
