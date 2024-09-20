<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;

    protected $fillable = [
        'province_id', 'city_id', 'name'
    ];

    public function province() {
        return $this->belongsTo(Province::class, 'province_id');
        // custome foreign Key
        // return $this->hasMany(Model::class, 'foreign_key');
    }
}
