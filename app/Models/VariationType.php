<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariationType extends Model
{

    public $timestamps = false; // 👈 Disable auto timestamp
    public function options()
    {
        return $this->hasMany(VariationTypeOption::class);
    }
}
