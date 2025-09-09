<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class VariationTypeOption extends Model implements HasMedia
{
    use InteractsWithMedia;

    public $timestamps = false; // 👈 Disable auto timestamps

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(100)
            ->queued(); // ✅ mark this conversion as queued;

        $this->addMediaConversion('small')
            ->width(480)
            ->queued();

        $this->addMediaConversion('large')
            ->width(1200)
            ->queued();
    }
}
