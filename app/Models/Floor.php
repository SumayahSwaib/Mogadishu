<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Floor extends Model
{
    use HasFactory;

    //creating , avod duplicate names boot
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $existingFloor = Floor::where('name', $model->name)->first();
            if ($existingFloor) {
                return false; // Prevent duplicate names
            }
            return $model;
        });

        //on updating, udpate floor name of all rooms
        static::updating(function ($model) {
            $existingFloor = Floor::where('name', $model->name)->first();
            if ($existingFloor && $existingFloor->id != $model->id) {
                // return false; // Prevent duplicate names
            }
            $oldName = $model->getOriginal('name'); 

            // Update floor name for all rooms
            Room::where('floor', $oldName)->update(['floor' => $model->name]);
        });
    }
}
