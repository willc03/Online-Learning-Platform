<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SectionItem extends Model
{
    use HasFactory;
    use HasUuids;

    /*
     * Certain attributes can be casted to certain types to save programming time
     */
    protected $casts = [
        'item_value' => 'array',
    ];

    /*
     * Create a many-to-one relationship between the item and its owner
     */
    public function section()
    {
        return $this->hasOne(Section::class, 'id', 'section_id');
    }
}
