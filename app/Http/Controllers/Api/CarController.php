<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CarResource;
use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CarController extends Controller
{
    const path_imagen = 'car';

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cars = Car::all();

        return new CarResource($cars);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated_data = $request->validate([
            'placa' => ['required', 'string'],
            'model' => ['nullable', 'string'],
            'year' => ['nullable', 'numeric'],
            'capacity' => ['nullable', 'integer'],
            'photo' => ['image'],
        ]);

        $path = ImagenController::store(self::path_imagen, $validated_data['photo']);

        $validated_data['photo'] = $path;

        $car = Car::create($validated_data);

        $driver = auth()->user->driver();
        $driver->car_id = $car->id;
        $driver->save();

        return response([
            'car' => new CarResource($car),
            'message' => 'Datos correctos'
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $car = Car::findOrFail($id);

        return response([
            'car' =>  new CarResource($car),
            'message' => 'Solicitud exitosa'
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated_data = $request->validate([
            'placa' => ['string'],
            'model' => ['nullable', 'string'],
            'year' => ['nullable', 'numeric'],
            'capacity' => ['nullable', 'integer'],
            'photo' => ['image'],
        ]);

        $car = Car::findOrFail($id);

        if ($request->hasFile('photo')) {
            if ($car->photo) {
                ImagenController::destroy(self::path_imagen . '/' . $car->photo);
            }

            $car->photo = ImagenController::store(self::path_imagen, $request->photo);
        }

        $car->update($validated_data);

        return response([
            'car' => new CarResource($car),
            'message' => 'Datos actualizados'
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $car = Car::findOrFail($id);

        if ($car->photo) {
            ImagenController::destroy(self::path_imagen . '/' . $car->photo);
        }

        $car->delete();

        return response([
            'message' => 'Coche eliminado correctamente',
        ], 204);
    }
}
