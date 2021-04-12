<?php

namespace Modules\Ilocations\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Ilocations\Entities\Geozones;

class GeozoneTableSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    Model::unguard();
      $path = base_path('/Modules/Ilocations/Assets/js/geozone.json');
      $geozones = json_decode(file_get_contents($path), true);

      foreach ($geozones as $key => &$geozone) {
          $zones = $geozone['zones'];
          unset($geozone['zones']);
          $geozoneCreated = Geozones::create($geozone);
          $geozoneCreated->zonesToGeoZone()->createMany($zones);
      }


  }
}