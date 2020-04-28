<?php

namespace Modules\Ilocations\Http\Requests;

use Modules\Core\Internationalisation\BaseFormRequest;

class CreatePolygonRequest extends BaseFormRequest
{
    public function rules()
    {
        return [
            'name' => 'required',
            'description' => 'required',
            'points' => 'required',
            'options' => 'required',
        ];
    }

    public function translationRules()
    {
        return [];
    }

    public function authorize()
    {
        return true;
    }

    public function messages()
    {
        return [];
    }

    public function translationMessages()
    {
        return [];
    }
}
