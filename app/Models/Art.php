<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMediaConversions;
use Spatie\MediaLibrary\HasMedia\Interfaces\HasMedia;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Art extends Model implements HasMediaConversions
{
    use HasMediaTrait;
    use SoftDeletes;

    protected $table = 'arts';

    protected $guarded = ['id'];

    public function registerMediaConversions()
    {
        $this->addMediaConversion('thumb')
             ->setManipulations(['w' => 100, 'h' => 100]);
		$this->addMediaConversion('admin')
             ->setManipulations(['w' => 200, 'h' => 200]);
    }
}
