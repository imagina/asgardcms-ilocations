<?php

namespace Modules\Ilocations\Entities;

use Dimsav\Translatable\Translatable;
use Illuminate\Database\Eloquent\Model;

class Neighborhood extends Model
{
    use Translatable;

    protected $table = 'ilocations__neighborhoods';
    public $translatedAttributes = ["name"];
    protected $fillable = ["city_id"];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

  public function geozones()
  {
    return $this->morphToMany(Geozones::class, 'geozonable');
  }
}
