<?php

namespace Modules\Ilocations\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Polygon extends Model
{
  use Translatable;

  protected $table = 'ilocations__polygons';

  public $translatedAttributes = [
    'name',
    'description'
  ];

  protected $fillable = [
    'points',
    'options',
  ];

  protected $casts = [
    'options' => 'array',
    'points' => 'array',
  ];

  public function geozones()
  {
    return $this->morphToMany(Geozones::class, 'geozonable');
  }
}
