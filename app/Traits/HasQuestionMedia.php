<?php

namespace App\Traits;

trait HasQuestionMedia
{
    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('image') ? $this->getFirstMediaUrl('image') : null;
    }

    public function getExplanationAssetAttribute()
    {
        return $this->getFirstMediaUrl('explanation_asset') ? $this->getFirstMediaUrl('explanation_asset') : null;
    }

    public function getHintImageAttribute()
    {
        return $this->getFirstMediaUrl('hint_image') ? $this->getFirstMediaUrl('hint_image') : null;
    }
}
