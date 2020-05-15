<?php

namespace Modules\Ilocations\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
  use Translatable;
  
  protected $table = 'ilocations__cities';

  public $translatedAttributes = [
    'name'
  ];
  protected $fillable = [
    'code',
    'province_id',
    'country_id'
  ];
  
  public function country()
  {
    return $this->belongsTo(Country::class);
  }
  
  public function province()
  {
    return $this->belongsTo(Province::class);
  }

  public function geozones()
  {
    return $this->morphToMany(Geozones::class, 'geozonable');
  }
}
