<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

class NewsPostRequest extends Request
{

    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            //'news_category_name_id'  => 'required | array',
            //'news_type'              => 'required | numeric',
            //'news_title_lang1'       => 'required',
            'news_title_lang2'       => 'required',
            //'news_description_lang1' => 'required',
            //'news_description_lang2' => 'required',
            //'news_slug_lang1'        => 'required',
            'news_slug_lang2'        => 'required',
            'news_image'             => 'required | array',
            //'image_title_lang1'      => 'required | array',
            'image_title_lang2'      => 'required | array',
            //'is_priority_news'       => 'required | numeric',
        ];
    }
}
