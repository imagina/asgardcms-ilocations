<?php

namespace Modules\Ilocations\Transformers;

use Illuminate\Http\Resources\Json\Resource;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

class NeighborhoodTransformer extends Resource
{
    public function toArray($request)
    {
      $data = [
        'id' => $this->when($this->id, $this->id),
        'name'=> $this->when($this->translate('en')->name,$this->translate('en')->name),
        'city' => new CityTransformer($this->whenLoaded('city')),
        'updatedAt' => $this->when($this->updated_at, $this->updated_at),
        'createdAt' => $this->when($this->created_at, $this->created_at),
      ];

      $filter = json_decode($request->filter);

      // Return data with available translations
      if (isset($filter->allTranslations) && $filter->allTranslations) {
        // Get langs avaliables
        $languages = \LaravelLocalization::getSupportedLocales();

        foreach ($languages as $lang => $value) {
          $data[$lang]['name'] = $this->hasTranslation($lang) ? $this->translate("$lang")['name'] : '';
        }
      }

      return $data;
    }
}