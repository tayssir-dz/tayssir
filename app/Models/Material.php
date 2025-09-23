<?php

namespace App\Models;

use App\Enums\ContentDirection;
use App\Models\Pivot\DivisionMaterial;
use App\Models\Pivot\MaterialUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Material extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'color',
        'secondary_color',
        'description',
        'division_id',
        'direction',
        'active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'direction' => ContentDirection::class,
    ];

    /**
     * Register the media collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('image')
            ->singleFile();

        $this->addMediaCollection('image_grid')
            ->singleFile();
    }

    /**
     * Get the image attribute.
     */
    public function getImageAttribute()
    {
        return $this->getFirstMediaUrl('image') ? $this->getFirstMediaUrl('image') : null;
    }

    /**
     * Get the image_grid attribute.
     */
    public function getImageGridAttribute()
    {
        return $this->getFirstMediaUrl('image_grid') ? $this->getFirstMediaUrl('image_grid') : null;
    }

    /**
     * Get the divisions for the material.
     */
    public function divisions()
    {
        return $this->belongsToMany(Division::class)
            ->using(DivisionMaterial::class)
            ->withPivot('sort');
    }

    public function units()
    {
        return $this->belongsToMany(Unit::class)
            ->using(MaterialUnit::class)
            ->withPivot('sort')
            ->orderBy('material_unit.sort');
    }

    /**
     * Get the summaries for the material.
     */
    public function summaries()
    {
        return $this->hasMany(Summary::class);
    }

    /**
     * Get the bacs for the material.
     */
    public function bacs()
    {
        return $this->hasMany(Bac::class);
    }

    /**
     * Get the flashcard groups for the material.
     */
    public function flashcardGroups()
    {
        return $this->hasMany(FlashcardGroup::class);
    }

    /**
     * Get the rtl attribute for backward compatibility.
     */
    public function getRtlAttribute()
    {
        return $this->direction === ContentDirection::RTL;
    }

    /**
     * Scope a query to only include active materials.
     */
    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    /**
     * Deep clone the material along with its units, chapters, questions and related media.
     *
     * Cloning rules:
     * - Material name becomes: "{original name} (cloned)".
     * - Media collections (image, image_grid) are copied (new physical files) for the cloned material.
     * - Each related Unit is cloned (attributes + its single "image" media) and attached with same pivot sort.
     * - Each Unit's Chapters are cloned (attributes + all media in "chapter_photos" collection) with original pivot sort preserved.
     * - Each Chapter's Questions are cloned (attributes + media collections: image, explanation_asset, hint_image) with original pivot sort preserved.
     *
     * All operations execute inside a single DB transaction to avoid partial clones.
     */
    public function deepClone(): self
    {
        return DB::transaction(function () {
            $stripCounts = function (Model $model) {
                foreach ($model->getAttributes() as $key => $value) {
                    if (str_ends_with($key, '_count')) {
                        unset($model->$key);
                    }
                }
            };
            // Clone material core attributes
            $cloned = $this->replicate();
            $cloned->name = $this->name . ' (cloned)';
            $stripCounts($cloned);
            $cloned->push(); // save

            // Copy material media (single file collections)
            foreach (['image', 'image_grid'] as $collection) {
                if ($media = $this->getFirstMedia($collection)) {
                    $cloned
                        ->addMedia($media->getPath())
                        ->usingName($media->name)
                        ->usingFileName($media->file_name)
                        ->toMediaCollection($collection);
                }
            }

            // Eager load nested relations to minimize queries
            $originalUnits = $this->units()
                ->with(['chapters.questions'])
                ->get();

            foreach ($originalUnits as $unit) {
                $newUnit = $unit->replicate();
                // Detach any potential lingering foreign keys that might wrongly point to original material (if schema still has material_id)
                if ($newUnit->isFillable('material_id')) {
                    $newUnit->material_id = null; // relationship handled by pivot
                }
                $stripCounts($newUnit);
                $newUnit->push();

                // Copy unit media
                if ($uMedia = $unit->getFirstMedia('image')) {
                    $newUnit
                        ->addMedia($uMedia->getPath())
                        ->usingName($uMedia->name)
                        ->usingFileName($uMedia->file_name)
                        ->toMediaCollection('image');
                }

                // Attach unit to cloned material preserving pivot sort
                $cloned->units()->attach($newUnit->id, [
                    'sort' => $unit->pivot->sort ?? 0,
                ]);

                // Clone chapters of unit
                $chapters = $unit->chapters()->with('questions')->get();
                foreach ($chapters as $chapter) {
                    $newChapter = $chapter->replicate();
                    $stripCounts($newChapter);
                    $newChapter->push();

                    // Copy all chapter photos (could be multiple)
                    foreach ($chapter->getMedia('chapter_photos') as $cMedia) {
                        $newChapter
                            ->addMedia($cMedia->getPath())
                            ->usingName($cMedia->name)
                            ->usingFileName($cMedia->file_name)
                            ->toMediaCollection('chapter_photos');
                    }

                    // Attach chapter to new unit preserving sort
                    $newUnit->chapters()->attach($newChapter->id, [
                        'sort' => $chapter->pivot->sort ?? 0,
                    ]);

                    // Clone questions of chapter
                    $questions = $chapter->questions()->get();
                    foreach ($questions as $question) {
                        $newQuestion = $question->replicate();
                        $stripCounts($newQuestion);
                        $newQuestion->push();

                        foreach (['image', 'explanation_asset', 'hint_image'] as $qCollection) {
                            if ($qMedia = $question->getFirstMedia($qCollection)) {
                                $newQuestion
                                    ->addMedia($qMedia->getPath())
                                    ->usingName($qMedia->name)
                                    ->usingFileName($qMedia->file_name)
                                    ->toMediaCollection($qCollection);
                            }
                        }

                        $newChapter->questions()->attach($newQuestion->id, [
                            'sort' => $question->pivot->sort ?? 0,
                        ]);
                    }
                }
            }

            return $cloned->fresh(['units.chapters.questions']);
        });
    }
}
