<?php

namespace Modules\Ilocations\Repositories;

use Modules\Core\Repositories\BaseRepository;

interface GeozonesRepository extends BaseRepository
{
  public function getAll();
  
}