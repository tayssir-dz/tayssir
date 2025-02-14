<?php

namespace App\Traits;



trait HasQuestionMedia
{
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();

        $this->addMediaCollection('explanation_asset')
            ->singleFile();

        $this->addMediaCollection('hint_image')
            ->singleFile();
    }

    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('image');
    }

    public function getExplanationAssetAttribute()
    {
        return $this->getFirstMediaUrl('explanation_asset');
    }

    public function getHintImageAttribute()
    {
        return $this->getFirstMediaUrl('hint_image');
    }
}
