<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariationType extends Model
{
    public function options()
    {
        return $this->hasMany(VariationTypeOption::class);
    }
}
