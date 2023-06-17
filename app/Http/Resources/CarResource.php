<?php

namespace App\Http\Resources;

use App\Http\Controllers\Api\ImagenController;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CarResource extends JsonResource
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
            'placa' => $this->placa,
            'model' => $this->model,
            'year' => $this->year,
            'capacity' => $this->capacity,
            'photo' => ImagenController::show('car/', $this->photo),
        ];
    }
}
