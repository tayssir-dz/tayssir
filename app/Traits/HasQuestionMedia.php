<?php

namespace App\Traits;

trait HasQuestionMedia
{
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
