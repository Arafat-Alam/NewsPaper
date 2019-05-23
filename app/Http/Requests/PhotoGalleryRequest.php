<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class PhotoGalleryRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'photo_title_lang1' => 'required',
            'photo_title_lang2' => 'required',
            'photo'             => 'required'
        ];
    }
}
