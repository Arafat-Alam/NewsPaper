<?php
namespace App\Services\BackEnd;

use DB;
use phpDocumentor\Reflection\Types\Null_;
use Session;
use Lang;
use Image;
use App\Http\Helper;

class NewsPostService
{
//=======@@ Start News Section  @@=======
    public static function getNewsPost()
    {
        $result = DB::table('news_post')
            ->orderBy('id', 'DESC')
            ->paginate(20);
        return $result;
    }

    public static function getNewsType()
    {
        $result = DB::table('news_type')
            ->where('status', 1)
            ->orderBy('id', 'ASC')
            ->get();
        return $result;
    }

    public static function getActiveNewsCategory()
    {
        $result = DB::table('news_category')
            ->where('status', 1)
            ->get();
        return $result;
    }

    public static function getActiveNewsSubCategory()
    {
        $result = DB::table('news_sub_category')
            ->where('status', 1)
            ->get();
        return $result;
    }

    public static function getSubCategoryBySelectedCategory($data = null)
    {
        return DB::table('news_sub_category')
            ->whereIn('fk_news_category_id', $data['category_id'])
            ->where('status', 1)
            ->get();
    }

    public static function saveNewsPost($data = null)
    {
        try {
            DB::beginTransaction();
            if (isset($data['news_title_lang1'])) {
                $newsPostId = DB::table('news_post')
                    ->insertGetId([
                        'news_type'         => $data['news_type'],
                        'title_lang1'       => $data['news_title_lang1'],
                        'title_lang2'       => $data['news_title_lang2'],
                        'sub_heading_lang1' => $data['sub_heading_lang1'],
                        'description_lang1' => $data['news_description_lang1'],
                        'description_lang2' => $data['news_description_lang2'],
                        'slug_lang1'        => $data['news_slug_lang1'],
                        'slug_lang2'        => $data['news_slug_lang2'],
                        'reporter_name'     => $data['reporter_name'],
                        'view_counter'      => 0,
                        'status'            => 1,
                        'created_at'        => date("Y-m-d H:i:s"),
                        'created_by'        => Session::get('admin.id'),
                    ]);
            } else {
                $newsPostId = DB::table('news_post')
                    ->insertGetId([
                        'news_type' => $data['news_type'],
                        'title_lang2' => $data['news_title_lang2'],
                        'sub_heading_lang1' => $data['sub_heading_lang1'],
                        'description_lang2' => $data['news_description_lang2'],
                        'slug_lang2' => $data['news_slug_lang2'],
                        'reporter_name' => $data['reporter_name'],
                        'view_counter' => 0,
                        'status' => 1,
                        'created_at' => date("Y-m-d h:i:s"),
                        'created_by' => Session::get('admin.id'),
                    ]);
            }

            if ($newsPostId) {
                if (isset($data['news_sub_category_name_id'])) {
                    if (count($data['news_sub_category_name_id']) > 0) {
                        $categoryIds = DB::table('news_sub_category')
                            ->whereIn('id', $data['news_sub_category_name_id'])
                            ->pluck('fk_news_category_id');
                        if (count($categoryIds)) {
                            foreach ($categoryIds as $categoryId) {
                                DB::table('category_wise_news')
                                    ->insert([
                                        'fk_news_category_id' => $categoryId,
                                        'fk_news_post_id' => $newsPostId,
                                        'created_at' => date("Y-m-d h:i:s"),
                                        'created_by' => Session::get('admin.id'),
                                    ]);
                            }
                        }
                        foreach ($data['news_sub_category_name_id'] as $newsSubCategoryId) {
                            DB::table('sub_category_wise_news')
                                ->insert([
                                    'fk_news_sub_category_id' => $newsSubCategoryId,
                                    'fk_news_post_id' => $newsPostId,
                                    'created_at' => date("Y-m-d h:i:s"),
                                    'created_by' => Session::get('admin.id'),
                                ]);
                        }
                    }
                }
                if (isset($data['news_image'])) {
                    $imageType = null;
                    for ($i = 0; $i < count($data['news_image']); $i++) {
                        ($i == 0) ? $imageType = 1 : $imageType = 2;
                        $folderPath = '/images/newsPost/';
                        // $fileName = Helper::imageUpload($newsPostId, $data['news_image'][$i], $folderPath, 330);
                        $fileName = Helper::imageUploadRaw($newsPostId, $data['news_image'][$i], $folderPath,330);

                        if (isset($data['image_title_lang1'])) {
                            DB::table('news_wise_image')
                                ->insert([
                                    'fk_news_post_id' => $newsPostId,
                                    'image_path' => $fileName,
                                    'image_type' => $imageType,
                                    'image_title_lang1' => $data['image_title_lang1'][$i],
                                    'image_title_lang2' => $data['image_title_lang2'][$i],
                                    'status' => 1,
                                    'created_at' => date("Y-m-d h:i:s"),
                                    'created_by' => Session::get('admin.id'),
                                ]);
                        } else {
                            DB::table('news_wise_image')
                                ->insert([
                                    'fk_news_post_id' => $newsPostId,
                                    'image_path' => $fileName,
                                    'image_type' => $imageType,
                                    //'image_title_lang1' => $data['image_title_lang1'][$i],
                                    'image_title_lang2' => $data['image_title_lang2'][$i],
                                    'status' => 1,
                                    'created_at' => date("Y-m-d h:i:s"),
                                    'created_by' => Session::get('admin.id'),
                                ]);
                        }
                    }
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function getNewsById($newsPostId = null)
    {
        $newsDetails = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
                'news_priority.id as news_priority_id',
            ])
            ->leftJoin('news_priority', 'np.id', '=', 'news_priority.fk_news_post_id')
            ->where('np.id', $newsPostId)
            ->first();

        $category = DB::table('category_wise_news as cwn')
            ->join('news_category as nc', 'cwn.fk_news_category_id', '=', 'nc.id')
            ->where('cwn.fk_news_post_id', $newsPostId)
            ->where('cwn.status', 1)
            ->get([
                'nc.*'
            ]);
        $subCategory = DB::table('sub_category_wise_news as scwn')
            ->join('news_sub_category as nsc', 'scwn.fk_news_sub_category_id', '=', 'nsc.id')
            ->where('scwn.fk_news_post_id', $newsPostId)
            ->where('scwn.status', 1)
            ->get([
                'nsc.*'
            ]);

        $newsImage = DB::table('news_wise_image')
            ->where('status', 1)
            ->where('fk_news_post_id', $newsPostId)
            ->get();
        $newsDetails->newsImage = $newsImage;
        $newsDetails->newsCategory = $category;
        $newsDetails->newsSubCategory = $subCategory;
        return $newsDetails;
    }

    public static function editNewsPostDetails($data = null)
    {
        print "<pre>";
        print_r($data);
        exit;
        try {
            DB::beginTransaction();
            DB::table('news_post')
                ->where('id', $data['news_post_id'])
                ->update([
                    'news_type' => $data['news_type'],
                    //'title_lang1'     => $data['news_title_lang1'],
                    'title_lang2' => $data['news_title_lang2'],
                    'sub_heading_lang1' => $data['sub_heading_lang1'],
                    //'description_lang1' => $data['news_description_lang1'],
                    'description_lang2' => $data['news_description_lang2'],
                    //'slug_lang1' => $data['news_slug_lang1'],
                    'slug_lang2' => $data['news_slug_lang2'],
                    'reporter_name' => $data['reporter_name'],
                    'view_counter' => 0,
                    'status' => 1,
                    'updated_at' => date("Y-m-d H:i:s"),
                    'updated_by' => Session::get('admin.id')
                ]);
            self::saveEditSubCategoryWiseNews($data);
            self::saveEditCategoryWiseNews($data);
            //self::saveEditNewsPriority($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            return $e;
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function saveEditSubCategoryWiseNews($data = null)
    {
        $deleteCategoryArr = array();
        $updateCategoryArr = array();
        $insertCategoryArr = array();
        $existsCategoryArr = array();
        $subCatWIseNews = DB::table('sub_category_wise_news')
            ->where('fk_news_post_id', $data['news_post_id'])
            ->get();
        for ($i = 0; $i < count($subCatWIseNews); $i++) {
            if (in_array($subCatWIseNews[$i]->fk_news_sub_category_id, $data['news_sub_category_name_id'])) {
                $updateCategoryArr[] = array(
                    'id' => $subCatWIseNews[$i]->id,
                    'fk_news_post_id' => $subCatWIseNews[$i]->fk_news_post_id,
                    'fk_news_sub_category_id' => $subCatWIseNews[$i]->fk_news_sub_category_id,
                );
            } else {
                $deleteCategoryArr[] = array(
                    'id' => $subCatWIseNews[$i]->id,
                    'fk_news_post_id' => $subCatWIseNews[$i]->fk_news_post_id,
                    'fk_news_sub_category_id' => $subCatWIseNews[$i]->fk_news_sub_category_id,
                );
            }
        }

        foreach ($updateCategoryArr as $key => $value) {
            $existsCategoryArr[] = $value['fk_news_sub_category_id'];
        }
        $insertCategoryArr = array_diff($data['news_sub_category_name_id'], $existsCategoryArr);

        foreach ($deleteCategoryArr as $key => $deleteColor) {
            DB::table('sub_category_wise_news')
                ->where('id', $deleteColor['id'])
                ->update([
                    'status' => 0,
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_by' => Session::get('admin.id'),
                ]);
        }
        foreach ($insertCategoryArr as $key => $insertCategory) {
            DB::table('sub_category_wise_news')
                ->insert([
                    'fk_news_post_id' => $data['news_post_id'],
                    'fk_news_sub_category_id' => $insertCategory,
                    'status' => 1,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_by' => Session::get('admin.id'),
                ]);
        }
    }

    public static function saveEditCategoryWiseNews($data = null)
    {
        $data['news_category_name_id'] = DB::table('news_sub_category')
            ->whereIn('id', $data['news_sub_category_name_id'])
            ->pluck('fk_news_category_id');

        $deleteCategoryArr = array();
        $updateCategoryArr = array();
        $insertCategoryArr = array();
        $existsCategoryArr = array();
        $newsCategory = DB::table('category_wise_news')
            ->where('fk_news_post_id', $data['news_post_id'])
            ->get();
        for ($i = 0; $i < count($newsCategory); $i++) {
            if (in_array($newsCategory[$i]->fk_news_category_id, $data['news_category_name_id'])) {
                $updateCategoryArr[] = array(
                    'id' => $newsCategory[$i]->id,
                    'fk_news_post_id' => $newsCategory[$i]->fk_news_post_id,
                    'fk_news_category_id' => $newsCategory[$i]->fk_news_category_id,
                );
            } else {
                $deleteCategoryArr[] = array(
                    'id' => $newsCategory[$i]->id,
                    'fk_news_post_id' => $newsCategory[$i]->fk_news_post_id,
                    'fk_news_category_id' => $newsCategory[$i]->fk_news_category_id,
                );
            }
        }

        foreach ($updateCategoryArr as $key => $value) {
            $existsCategoryArr[] = $value['fk_news_category_id'];
        }
        $insertCategoryArr = array_diff($data['news_category_name_id'], $existsCategoryArr);

        foreach ($deleteCategoryArr as $key => $deleteColor) {
            DB::table('category_wise_news')
                ->where('id', $deleteColor['id'])
                ->update([
                    'status' => 0,
                    'updated_at' => date('Y-m-d h:i:s'),
                    'updated_by' => Session::get('admin.id'),
                ]);
        }
        foreach ($insertCategoryArr as $key => $insertCategory) {
            DB::table('category_wise_news')
                ->insert([
                    'fk_news_post_id' => $data['news_post_id'],
                    'fk_news_category_id' => $insertCategory,
                    'status' => 1,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_by' => Session::get('admin.id'),
                ]);
        }
    }

    public static function saveEditNewsPriority($data)
    {
        $priorityStatus = DB::table('news_priority')
            ->where('fk_news_post_id', $data['news_post_id'])
            ->first();
        if (isset($data['is_priority_news']) && $data['is_priority_news']) {
            if (count($priorityStatus) <= 0) {
                DB::table('news_priority')
                    ->insert([
                        'fk_news_post_id' => $data['news_post_id'],
                        'priority_number' => 0,
                        'status' => 1,
                        'created_at' => date("Y-m-d h:i:s"),
                        'created_by' => Session::get('admin.id'),
                    ]);
            }
        } else {
            if (count($priorityStatus) > 0) {
                DB::table('news_priority')
                    ->where('id', $priorityStatus->id)
                    ->delete();
            }
        }
    }

    public static function saveEditNewsPostImage($data = null)
    {
        try {
            DB::beginTransaction();
            $folderPath = '/images/newsPost/';
            $newsPostId = $data['news_post_id'];
            $newsPostImage = self::getNewsPostImageById($newsPostId);
            for ($i = 0; $i < count($data['image_title_lang2']); $i++) {
                if ($i > 0) {
                    // $fileName = Helper::imageUpload($newsPostId, $data['news_image'][$i], $folderPath, 330);
                    $fileName = Helper::imageUploadRaw($newsPostId, $data['news_image'][$i], $folderPath,330);
                    DB::table('news_wise_image')
                        ->insert([
                            'fk_news_post_id' => $newsPostId,
                            'image_path' => $fileName,
                            'image_type' => 2,
                            'image_title_lang1' => $data['image_title_lang2'][$i],
                            'image_title_lang2' => $data['image_title_lang2'][$i],
                            'status' => 1,
                            'created_at' => date("Y-m-d h:i:s"),
                            'created_by' => Session::get('admin.id'),
                        ]);
                }
            }

            $imageUpdateFlag = null;
            if (isset($data['news_image'][0])) {
                // $fileName = Helper::imageUpload($newsPostId, $data['news_image'][0], $folderPath);
                $fileName = Helper::imageUploadRaw($newsPostId, $data['news_image'][0], $folderPath);
                if ($fileName == null) {
                    $fileName = $newsPostImage->image_path;
                } else if ($fileName == 'tooLarge') {
                    return 'tooLarge';
                } else {
                    $imageUpdateFlag = 1;
                }
            } else {
                $fileName = $newsPostImage->image_path;
            }

            $status = DB::table('news_wise_image')
                ->where('id', $newsPostImage->id)
                ->update([
                    'image_path' => $fileName,
                    'image_title_lang1' => $data['image_title_lang2'][0],
                    'image_title_lang1' => $data['image_title_lang2'][0],
                    'updated_at' => date("Y-m-d h:i:s"),
                    'updated_by' => Session::get('admin.id'),
                ]);
            if ($status) {
                if ($imageUpdateFlag == 1 && $newsPostImage->image_path && file_exists(public_path('images/newsPost/' . $newsPostImage->image_path))) {
                    unlink(public_path('images/newsPost/' . $newsPostImage->image_path));
                }
            }
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            dd($e);
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function getNewsPostImageById($newsPostId = null)
    {
        $result = DB::table('news_wise_image')
            ->where('fk_news_post_id', $newsPostId)
            ->where('image_type', 1)
            ->first();
        return $result;
    }

    public static function removeNewsPostImage($data = null)
    {
        $status = DB::table('news_wise_image')
            ->where('id', $data['image_id'])
            ->delete();
        if ($status) {
            return true;
            unlink(public_path('images/newsPost/' . $data['image_path']));
        }
    }

    public static function inactiveNews($newsId = null)
    {
        try {
            $status = DB::table('news_post')
                ->where('id', $newsId)
                ->update([
                    'status' => 0,
                ]);
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function activeNews($newsId = null)
    {
        try {
            $status = DB::table('news_post')
                ->where('id', $newsId)
                ->update([
                    'status' => 1,
                ]);
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

//=======@@ End News Section  @@=======

    public static function getAllPriorityWiseNews()
    {
        $result = DB::table('news_post')
            ->join('news_priority', 'news_post.id', '=', 'news_priority.fk_news_post_id')
            ->orderBy('news_priority.priority_number', 'ASC')
            ->where('news_priority.status', 1)
            ->get([
                'news_post.id as news_post_id',
                'news_post.title_lang2',
                'news_priority.id as news_priority_id',
                'news_priority.*'
            ]);
        return $result;
    }

    public static function getAllNewsForPriorityAssign()
    {
        $dupticateNewsArr = DB::table('news_post')
            ->join('news_priority', 'news_post.id', '=', 'news_priority.fk_news_post_id')
            ->pluck('news_post.id');
        $result = DB::table('news_post')
            ->whereIn('news_post.news_type', [2, 4, 5])
            ->whereNotIn('news_post.id', $dupticateNewsArr)
            ->where('news_post.status', 1)
            ->limit(50)
            ->orderBy('news_post.id', 'DESC')
            ->get([
                'news_post.id as news_post_id',
                'news_post.title_lang2',
            ]);
        return $result;
    }

    public static function inactiveNewsPriority($newsPriorityId = null)
    {
        try {
            $status = DB::table('news_priority')
                ->where('id', $newsPriorityId)
                ->update([
                    'status' => 0,
                ]);
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function activeNewsPriority($newsPriorityId = null)
    {
        try {
            $status = DB::table('news_priority')
                ->where('id', $newsPriorityId)
                ->update([
                    'status' => 1,
                ]);
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function saveEditNewsPriorityAssign($data = null)
    {
        try{
            if ($data['news_priority_id']) {
                DB::table('news_priority')
                    ->where('id',$data['news_priority_id'])
                    ->update([
                        'priority_number' => $data['news_priority_number']
                    ]);
            }else{
                DB::table('news_priority')
                    ->insert([
                        'fk_news_post_id' => $data['news_post_id'],
                        'priority_number' => $data['news_priority_number'],
                        'status' => 1,
                        'created_at' => date('Y-m-d'),
                        'created_by' => Session::get('admin.id'),
                    ]);
            }
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
        /*dd($data);
        $status = DB::table('news_priority')
            ->select(
                'news_post.news_type as news_type',
                'news_priority.*'
            )
            ->join('news_post', 'news_post.id', '=', 'news_priority.fk_news_post_id')
            ->where('priority_number', $data['news_priority_number'])
            ->whereIn('news_type', [2, 4, 5])
            ->first();
        if (count($status) > 0) {
            try {
                DB::table('news_priority')
                    ->where('id', $status->id)
                    ->update([
                        'fk_news_post_id' => $data['news_priority_id']
                    ]);
                return true;
            } catch (\Exception $e) {
                $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
                return $err_msg;
            }

        } else {
            try {
                DB::table('news_priority')
                    ->insert([
                        'fk_news_post_id' => $data['news_priority_id'],
                        'priority_number' => $data['news_priority_number'],
                        'status' => 1
                    ]);
                return true;
            } catch (\Exception $e) {
                $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
                return $err_msg;
            }
        }*/
    }

}
