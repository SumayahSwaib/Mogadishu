<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Room extends Model
{
    use HasFactory;

    protected $appends = ['name_text'];

    public function getNameTextAttribute()
    {
        return $this->house->name . ", " . $this->name;
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($m) {
            return Room::my_update($m); //she removed this, so when adding a new room, automatic things like landload_id where not being set!
        });
        self::updating(function ($m) {
            return Room::my_update($m);
        });
        self::deleting(function ($m) {
            if ($m->id == 1) {
                throw new Exception("You cannot delete this room.", 1);
            }
        });
    }


    /**
     * Updates the given model by setting its landload_id, region_id, and area_id
     * based on the corresponding values from the associated House model.
     *
     * @param mixed $m The model to be updated.
     * @throws Exception If the House associated with the model is not found.
     * @return mixed The updated model.
     */
    public static function my_update($m)
    {
        /* 
        $house = House::find($m->house_id);
        if ($house == null) {
            throw new Exception("House not found.", 1);
        }

        $m->landload_id = $house->landload_id;
        $m->region_id = $house->region_id;
        $m->area_id = $house->area_id;

        return $m; */
    }

    public static function get_rooms()
    {
        $houses = [];
        foreach (Room::where([])->orderBy('name', 'asc')->get() as $key => $h) {
            $houses[$h->id] = "#" . $h->id . " - " . $h->name;
        }
        return $houses;
    }

    //find a list of available and ready rooms
    public static function get_ready_rooms()
    {
        $houses = [];
        foreach (
            Room::where(['status' => 'Vacant'])
                ->orderBy('name', 'asc')->get() as $key => $room
        ) {
            $houses[$room->id] = "#" . $room->id . " - " . $room->name . ", " . $room->house->name . " - UGX " . number_format($room->price);
        }
        return $houses;
    }
    public static function get_all_rooms()
    {
        $houses = [];
        foreach (
            Room::where([])
                ->orderBy('name', 'asc')->get() as $key => $room
        ) {
            $houses[$room->id] = "#" . $room->id . " - " . $room->name . ", " . $room->house->name . " - UGX " . number_format($room->price);
        }
        return $houses;
    }
    public static function get_vacant_rooms()
    {
        $houses = [];
        foreach (
            Room::where([
                'status' => 'Vacant'
            ])
                ->orderBy('name', 'asc')->get() as $key => $room
        ) {
            $houses[$room->id] = "#" . $room->id . " - " . $room->name . ", " . $room->house->name . " - UGX " . number_format($room->price);
        }
        return $houses;
    }

    //status attribute
    public function getStatusAttribute($value)
    {
        if (!in_array($value, ['Vacant', 'Occupied',])) {
            $sql = 'UPDATE rooms SET status = ? WHERE id = ?';
            DB::update($sql, ['Vacant', $this->id]);
            $value = 'Vacant'; // reset to Vacant if status is not recognized 
        }
        return $value;
    }

    public function house()
    {
        $x = House::find($this->house_id);
        if ($x == null) {
            $this->house_id = 1;
            $this->save();
        }
        return $this->belongsTo(House::class);
    }
    public function rentings()
    {
        return $this->hasMany(Renting::class, 'room_id');
    }


    public function getFurnishingsAttribute($d)
    {
        if ($d != null)
            return json_decode($d, true);
    }


    public function setFurnishingsAttribute($d)
    {
        if (is_array($d)) {
            $this->attributes['furnishings'] = json_encode($d);
        }
    }

    public function getUtilitiesAttribute($d)
    {
        if ($d != null)
            return json_decode($d, true);
    }


    public function setUtilitiesAttribute($d)
    {
        if (is_array($d)) {
            $this->attributes['utilities'] = json_encode($d);
        }
    }



    public function getInternetAccessAttribute($d)
    {
        if ($d != null)
            return json_decode($d, true);
    }


    public function setInternetAccessAttribute($d)
    {
        if (is_array($d)) {
            $this->attributes['internet_access'] = json_encode($d);
        }
    }


    public function getSecurityAttribute($d)
    {
        if ($d != null)
            return json_decode($d, true);
    }


    public function setSecurityAttribute($d)
    {
        if (is_array($d)) {
            $this->attributes['security'] = json_encode($d);
        }
    }


    public function getAmenitiesAttribute($d)
    {
        if ($d != null)
            return json_decode($d, true);
    }


    public function setAmenitiesAttribute($d)
    {
        if (is_array($d)) {
            $this->attributes['amenities'] = json_encode($d);
        }
    }

    public function getImagesAttribute($d)
    {
        if ($d != null)
            return json_decode($d, true);
    }


    public function getImageAttribute($value)
    {
        if ($value == null || $value == '' || strlen($value) < 3) {
            return url('logo.jpg');
        }
        return $value;
    }


    public function setImagesAttribute($d)
    {
        if (is_array($d)) {
            $this->attributes['images'] = json_encode($d);
        }
    }
}
