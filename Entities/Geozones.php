<?php

namespace Modules\Ilocations\Entities;

use Illuminate\Database\Eloquent\Model;

class Geozones extends Model
{

  protected $table = 'ilocations__geozones';

  protected $fillable = [
    'name',
    'description'
  ];

  public function countries()
  {
    return $this->morphedByMany(Country::Class, 'geozonable', 'ilocations__geozonables', 'geozone_id', 'geozonable_id');
  }

  public function cities()
  {
    return $this->morphedByMany(City::Class, 'geozonable', 'ilocations__geozonables', 'geozone_id');
  }

  public function provinces()
  {
    return $this->morphedByMany(Province::Class, 'geozonable', 'ilocations__geozonables', 'geozone_id');
  }

  public function polygons()
  {
    return $this->morphedByMany(Polygon::Class, 'geozonable', 'ilocations__geozonables', 'geozone_id');
  }

  public function neighborhoods()
  {
    return $this->morphedByMany(Neighborhood::Class, 'geozonable', 'ilocations__geozonables', 'geozone_id');
  }

}
