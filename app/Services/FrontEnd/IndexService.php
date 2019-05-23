<?php
namespace App\Services\FrontEnd;

use DB;
use Illuminate\Http\Request;
use Mockery\CountValidator\Exception;
use Session;
use Lang;
use Helper;

class IndexService
{
    //1 = normal news, 2 = selected news, 3 = breaking news, 4 = exclusive_news
    public static function getMenu()
    {
        $result = [];
        $categories = DB::table('news_category')
            ->where('status', 1)
            ->where('menu_show', 1)
            ->orderBy('view_order', 'ASC')
            ->get();
        foreach ($categories as $key => $category) {
            if ($category->sub_cat_consider == 0) {
                $result[json_encode($category)] = DB::table('news_sub_category')
                    ->where('fk_news_category_id', $category->id)
                    ->where('status', 1)
                    ->orderBy('view_order', 'ASC')
                    ->get();
            }elseif($category->sub_cat_consider == 1){
                $result[json_encode($category)] = null;
            }
        }
        return $result;
    }

    public static function newsDetails($newsPostId = null)
    {
        $news = DB::table('news_post as np')
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->join('news_category as nc', 'cwn.fk_news_category_id', '=', 'nc.id')
            ->where('np.status', 1)
            ->where('np.id', $newsPostId)
            ->first([
                'np.id as news_post_id',
                'np.*',
                //'nc.id as news_category_id',
                //'nc.*',
            ]);
        $news->news_image = $image = DB::table('news_wise_image')
            ->where('fk_news_post_id', $news->news_post_id)
            ->get();
        return $news;
    }

    public static function newsViewCount($newsPostId = null)
    {
        DB::table('news_post')
            ->where('id', $newsPostId)
            ->update([
                'view_counter' => DB::raw("view_counter + 1"),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    public static function getPhotoGallery()
    {
        $result = DB::table('photo_gallery')
            ->where('status', 1)
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get();
        return $result;
    }

    //1 = normal news, 2 = selected news, 3 = breaking news, 4 = Top News, 5 = Top Partial

    public static function getPriorityWiseTopNews($limit)
    {
        // return $limit;
        $result = array();
        $image = array();
        $latestNews = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
                'npr.*'
            ])
            ->leftJoin('news_priority as npr', 'np.id', '=', 'npr.fk_news_post_id')
            ->where('np.status', 1)
            ->where('npr.status', 1)
            ->whereIn('npr.priority_number', [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14])
            ->orderBy('npr.priority_number', 'ASC')
            ->orderBy('npr.id', 'DESC')
            ->limit($limit)
            ->get();
        foreach ($latestNews as $key => $news) {
            $result[] = $news;
            $image = DB::table('news_wise_image')
                ->where('fk_news_post_id', $news->news_post_id)
                ->where('image_type', 1)
                ->first();
            if (count($image) > 0) {
                $latestNews[$key]->news_image = $image;
            }
        }
        return $result;
    }

    public static function getHeadingAndBreakingNews()
    {
        $breakingNews = DB::table('news_post')
            ->where('news_type',3)
            ->get();
        if (count($breakingNews) > 0) {
            if ($breakingNews[0]->news_type == 3) {
                $breakingNews[0]->breaking_status = 1;
            }            
        } else {
            $breakingNews = DB::table('news_post')
            ->where('news_type',6)
            ->get();
            if (count($breakingNews) > 0) {
                if ($breakingNews[0]->news_type == 6) {
                    $breakingNews[0]->breaking_status = 2;
                }            
            }
        }
        return $breakingNews;
    }

    public static function categoryWiseLatestNews($newsCategoryId = null, $limit = null)
    {
        $result = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->where('cwn.fk_news_category_id', $newsCategoryId)
            ->where('np.status', 1)
            ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get();
        return $result;
    }

    public static function newsWiseLatestNews($newsPostId, $limit)
    {
        $news = DB::table('news_post as np')
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->where('np.id', $newsPostId)
            ->first();
        $query = DB::table('news_post as np')
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->where('np.status', 1)
            ->orderBy('np.id', 'DESC')
            ->limit($limit);
        if (!empty($news->fk_sub_district_id)) {
            $query->where('np.fk_sub_district_id', $news->fk_sub_district_id);
        } elseif (empty($news->fk_sub_district_id)) {
            $query->where('cwn.fk_news_category_id', $news->fk_news_category_id);
        }
        return $query->get();
    }

    public static function districtWiseLatestNews($districtId, $limit)
    {
        $result = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->join('sub_districts as sub_districts', 'np.fk_sub_district_id', '=', 'sub_districts.id')
            ->where('sub_districts.fk_district_id', $districtId)
            ->where('np.status', 1)
            ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get();
        return $result;
    }

    public static function subDistrictWiseLatestNews($subDistrictId, $limit)
    {
        $result = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->where('np.fk_sub_district_id', $subDistrictId)
            ->where('np.status', 1)
            ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get();
        return $result;
    }

    public static function getMostReadNews($limit = null)
    {
        $date = date('Y-m-d');
        $from_date = date('Y-m-d')." 12:00:01"; 
        $tomorrow = date('Y-m-d',strtotime(date('Y-m-d') . "+1 days")).(" 12:00:00");
        
        return DB::table('news_post as np')
            ->join('news_wise_image as nwi','np.id','=','nwi.fk_news_post_id')
            ->where('np.status', 1)
            ->where('nwi.image_type', 1)
            //->whereRaw("LEFT(np.created_at,10) = '$date'")
            ->orderBy('np.view_counter', 'DESC')
            ->whereBetween('np.updated_at',[$from_date,$tomorrow])
            // ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get([
                'np.id as news_post_id',
                'np.*',
                'nwi.*'
            ]);
    }

    public static function newsWiseMostReadNews($newsPostId, $limit)
    {
        $news = DB::table('news_post as np')
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->where('np.id', $newsPostId)
            ->first();
        $query = DB::table('news_post as np')
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->where('np.status', 1)
            ->orderBy('np.view_counter', 'DESC')
            ->limit($limit);
        if (!empty($news->fk_sub_district_id)) {
            $query->where('np.fk_sub_district_id', $news->fk_sub_district_id);
        } elseif (empty($news->fk_sub_district_id)) {
            $query->where('cwn.fk_news_category_id', $news->fk_news_category_id);
        }
        return $query->get();
    }

    public static function getcategoryWiseMostReadNews($categoryId = null, $limit = null)
    {
        $mostReadNews = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->where('cwn.fk_news_category_id', $categoryId)
            ->where('np.status', 1)
            ->orderBy('np.view_counter', 'DESC')
            ->limit($limit)
            ->get();
        return $mostReadNews;
    }

    public static function getDistrictWiseMostReadNews($districtId = null, $limit = null)
    {
        $mostReadNews = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->join('sub_districts as sd', 'np.fk_sub_district_id', '=', 'sd.id')
            ->where('sd.fk_district_id', $districtId)
            ->where('np.status', 1)
            ->orderBy('np.view_counter', 'DESC')
            ->limit($limit)
            ->get();
        return $mostReadNews;
    }

    public static function getSubDistrictWiseMostReadNews($subDistrictId = null, $limit = null)
    {
        $mostReadNews = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->where('np.fk_sub_district_id', $subDistrictId)
            ->where('np.status', 1)
            ->orderBy('np.view_counter', 'DESC')
            ->limit($limit)
            ->get();
        return $mostReadNews;
    }

    public static function getcategoryWiseSelectedNews($categoryId = null, $limit = null)
    {
        $mostReadNews = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->where('cwn.fk_news_category_id', $categoryId)
            ->where('np.status', 1)
            // ->where('np.news_type', 2)
            ->whereIn('np.news_type', [2,4,5])
            ->limit($limit)
            ->get();
        return $mostReadNews;
    }

    public static function getDistrictWiseSelectedNews($districtId = null, $limit = null)
    {
        $mostReadNews = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->join('sub_districts as sd', 'np.fk_sub_district_id', '=', 'sd.id')
            ->where('sd.fk_district_id', $districtId)
            ->where('np.status', 1)
            // ->where('np.news_type', 2)
            ->whereIn('np.news_type', [2,4,5])
            ->limit($limit)
            ->get();
        return $mostReadNews;
    }

    public static function getSubDistrictWiseSelectedNews($subDistrictId = null, $limit = null)
    {
        return DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
            ])
            ->where('np.fk_sub_district_id', $subDistrictId)
            ->where('np.status', 1)
            // ->where('np.news_type', 2)
            ->whereIn('np.news_type', [2,4,5])
            ->limit($limit)
            ->get();
    }

    public static function latestNews($limit = null)
    {
        return DB::table('news_post as np')
            ->join('news_wise_image as nwi', 'np.id', '=', 'nwi.fk_news_post_id')
            ->select([
                'np.id as news_post_id',
                'np.*',
                'nwi.image_path',
                'nwi.image_type'
            ])
            ->where('nwi.image_type', 1)
            ->where('np.status', 1)
            ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get();
    }

    public static function categoryWiseNews($categoryId = null, $limit)
    {
        $result = array();
        $image = array();
        $newsCategory = DB::table('news_category')
            ->where('id', $categoryId)
            ->first();
        $categoryWiseNews = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
                'nc.id as news_category_id',
                'nc.*',
            ])
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->join('news_category as nc', 'cwn.fk_news_category_id', '=', 'nc.id')
            ->where('np.status', 1)
            ->where('nc.id', $newsCategory->id)//here 1 first Layout
            ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get();
        $i = 0;
        foreach ($categoryWiseNews as $news) {
            $image = DB::table('news_wise_image')
                ->where('fk_news_post_id', $news->news_post_id)
                ->where('image_type', 1)
                ->first();
            if (count($image) > 0) {
                $categoryWiseNews[$i]->news_image = $image;
            }
            $i++;
        }
        if (count($categoryWiseNews) > 0) {
            $categoryWiseNews[0]->news_category_name = (Session::get('frontend_lang') == 1) ? $newsCategory->category_name_lang1 : $newsCategory->category_name_lang2;
        }
        return $categoryWiseNews;
    }

    public static function subCategoryWiseNews($subCategoryId = null, $limit)
    {
        $result = array();
        $image = array();
        $newsSubCategory = DB::table('news_sub_category')
            ->where('id', $subCategoryId)
            ->where('status',1)
            ->first();      
        $subCategoryWiseNews = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
                'nsc.id as news_sub_category_id',
                'nsc.*',
            ])
            ->join('sub_category_wise_news as scwn', 'np.id', '=', 'scwn.fk_news_post_id')
            ->join('news_sub_category as nsc', 'scwn.fk_news_sub_category_id', '=', 'nsc.id')
            ->where('np.status', 1)
            ->where('nsc.id', $newsSubCategory->id)//here 1 first Layout
            ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get();
        foreach ($subCategoryWiseNews as $key => $news) {
            $image = DB::table('news_wise_image')
                ->where('fk_news_post_id', $news->news_post_id)
                ->where('image_type', 1)
                ->first();
            if (count($image) > 0) {
                $subCategoryWiseNews[$key]->news_image = $image;
            }
        }
        if (count($subCategoryWiseNews) > 0) {
            $subCategoryWiseNews[0]->news_category_name = (Session::get('frontend_lang') == 1) ? $newsSubCategory->sub_category_name_lang1 : $newsSubCategory->sub_category_name_lang2;
        }
        return $subCategoryWiseNews;
    }

    public static function allCategoryNews($categoryId = null, $limit)
    {
        $result = array();
        $image = array();
        $newsCategory = DB::table('news_category')
            ->where('id', '!=', $categoryId)
            ->take(8)
            ->get();

        foreach ($newsCategory as $category) {
            $allCategoryNews = DB::table('news_post as np')
                ->select([
                    'np.id as news_post_id',
                    'np.*',
                    'nc.id as news_category_id',
                    'nc.*',
                ])
                ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
                ->join('news_category as nc', 'cwn.fk_news_category_id', '=', 'nc.id')
                ->where('np.status', 1)
                ->where('nc.id', $category->id)
                ->orderBy('np.id', 'DESC')
                ->groupBy('cwn.fk_news_post_id')
                ->limit($limit)
                ->get();
            $i = 0;
            foreach ($allCategoryNews as $news) {
                $image = DB::table('news_wise_image')
                    ->where('fk_news_post_id', $news->news_post_id)
                    ->where('image_type', 1)
                    ->first();
                if (count($image) > 0) {
                    $allCategoryNews[$i]->news_image = $image;
                }
                $i++;
            }
            (Session::get('frontend_lang') == 1) ? $categoryName = $category->category_name_lang1 : $categoryName = $category->category_name_lang2;
            $result[$categoryName] = $allCategoryNews;
        }
        return $result;
    }


    public static function getRelatedNews($newsPostId = null)
    {
        $category = DB::table('news_post as np')
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->join('news_category as nc', 'cwn.fk_news_category_id', '=', 'nc.id')
            ->where('np.id', $newsPostId)
            ->first([
                'nc.id as news_category_id', 'nc.*'
            ]);
        if (isset($category->news_category_id)) {
            $result = array();
            $image = array();
            $newses = DB::table('news_post as np')
                ->select([
                    'np.id as news_post_id',
                    'np.*',
                    'nc.id as news_category_id',
                    'nc.*',
                ])
                ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
                ->join('news_category as nc', 'cwn.fk_news_category_id', '=', 'nc.id')
                ->join('news_position', 'nc.fk_news_position_id', '=', 'news_position.id')
                ->where('np.status', 1)
                ->where('np.id', '<>', $newsPostId)
                ->where('cwn.fk_news_category_id', $category->news_category_id)
                ->orderBy('np.id', 'DESC')
                ->limit(10)
                ->get();
            $i = 0;
            foreach ($newses as $news) {
                $image = DB::table('news_wise_image')
                    ->where('fk_news_post_id', $news->news_post_id)
                    ->where('image_type', 1)
                    ->first();
                if (count($image) > 0) {
                    $newses[$i]->news_image = $image;
                }
                $i++;
            }
            if (count($newses) > 0) {
                $newses[0]->news_category_name = (Session::get('frontend_lang') == 1) ? $category->category_name_lang1 : $category->category_name_lang2;
            }
            return $newses;
        }
    }

    public static function getCategoryByCategoryName($categoryRoute = null)
    {
        return DB::table('news_category')
            ->where('category_route', $categoryRoute)
            ->first();
    }

    public static function getSubCategoryByCategoryName($subCategoryRoute = null)
    {
        return DB::table('news_sub_category')
            ->where('sub_category_route', $subCategoryRoute)
            ->where('status',1)
            ->first();
    }

    public static function getCategoryByNewsPostId($newsPostId = null)
    {
        return DB::table('category_wise_news')
            ->where('fk_news_post_id', $newsPostId)
            ->first();
    }

    public static function getThumbnailSubCategory()
    {
        return DB::table('news_sub_category as nsc')
            ->join('news_category as nc','nsc.fk_news_category_id','=','nc.id')
            ->whereIn('sub_category_route', ['news-paper-web-link','educational-important-website','higher-study-in-another-country'])
            ->orderBy('nsc.id','ASC')
            ->get();
    }

    public static function getColumnNewses($route,$limit)
    {
        return DB::table('news_post as np')
            ->join('sub_category_wise_news as scwn','np.id','=','scwn.fk_news_post_id')
            ->join('news_sub_category as nsc','scwn.fk_news_sub_category_id','=','nsc.id')
            ->join('news_wise_image as nwi','np.id','=','nwi.fk_news_post_id')
            ->where('np.status', 1)
            // ->where('np.news_type', 2)
            ->whereIn('np.news_type', [2,4,5])
            ->where('nsc.sub_category_route',$route)
            ->where('nwi.image_type', 1)
            ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get([
                'np.id as news_post_id',
                'np.*',
                'nwi.*'
            ]);
    }

    public static function getCampusNews($route,$limit)
    {
        $result = [];
        $subCategory = DB::table('news_category as nc')
            ->join('news_sub_category as nsc','nc.id','=','nsc.fk_news_category_id')
            ->where('nc.category_route',$route)
            ->where('nsc.status',1)
            ->limit(12)
            ->get(['nsc.*','nc.category_route']);

            //print_r($subCategory);
            //return 0;
        foreach ($subCategory as $value){
            $result[json_encode($value)] =  DB::table('news_post as np')
                ->join('sub_category_wise_news as scwn','np.id','=','scwn.fk_news_post_id')
                ->join('news_wise_image as nwi','np.id','=','nwi.fk_news_post_id')
                ->where('scwn.fk_news_sub_category_id',$value->id)
                ->where('np.status', 1)
                ->whereIn('np.news_type', [2,4,5])
                ->where('nwi.image_type', 1)
                ->orderBy('np.id', 'DESC')
                ->limit($limit)
                ->get([
                    'np.id as news_post_id',
                    'np.*',
                    'nwi.*'
                ]);
        }
        return $result;
    }

    public static function getCategoryWiseNews($categoryRoute,$limit)
    {
        return DB::table('news_post as np')
            ->join('category_wise_news as cwn','np.id','=','cwn.fk_news_post_id')
            ->join('news_category as nc','cwn.fk_news_category_id','=','nc.id')
            ->Join('news_wise_image as nwi','np.id','=','nwi.fk_news_post_id')
            ->where('np.status', 1)
            // ->where('np.news_type', 2)
            ->whereIn('np.news_type', [2,4,5])
            ->where('nc.category_route',$categoryRoute)
            ->where('nwi.image_type', 1)
            ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get([
                'np.id as news_post_id',
                'np.*',
                'nwi.*'
            ]);
    }

    public static function getSubCategoryWiseNews($subCategoryRoute,$limit)
    {
        return DB::table('news_post as np')
            ->join('sub_category_wise_news as scwn','np.id','=','scwn.fk_news_post_id')
            ->join('news_sub_category as nsc','scwn.fk_news_sub_category_id','=','nsc.id')
            ->Join('news_wise_image as nwi','np.id','=','nwi.fk_news_post_id')
            ->where('np.status', 1)
            // ->where('np.news_type', 2)
            ->whereIn('np.news_type', [2,4,5])
            ->where('nsc.sub_category_route',$subCategoryRoute)
            ->where('nwi.image_type', 1)
            ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get([
                'np.id as news_post_id',
                'np.*',
                'nwi.*'
            ]);
    }


    public static function mostRecent($limit)
    {
        $result = array();
        $image = array();
        $result = DB::table('news_post as np')
            ->select([
                'np.id as news_post_id',
                'np.*',
                'nc.id as news_category_id',
                'nc.*',
            ])
            ->join('category_wise_news as cwn', 'np.id', '=', 'cwn.fk_news_post_id')
            ->join('news_category as nc', 'cwn.fk_news_category_id', '=', 'nc.id')
            ->where('np.status', 1)
            ->orderBy('np.id', 'DESC')
            ->limit($limit)
            ->get();
        foreach ($result as $key => $news) {
            $image = DB::table('news_wise_image')
                ->where('fk_news_post_id', $news->news_post_id)
                ->where('image_type', 1)
                ->first();
            if (count($image) > 0) {
                $result[$key]->news_image = $image;
            }
        }
        return $result;
    }

    public static function searchNews($data)
    {
        $content = $data['search_content'];
        return DB::table('news_post')
            ->where('title_lang1', 'like', "%$content%")
            ->orWhere('title_lang2', 'like', "%$content%")
            ->orWhere('description_lang1', 'like', "%$content%")
            ->orWhere('description_lang2', 'like', "%$content%")
            ->orWhere('slug_lang1', 'like', "%$content%")
            ->orWhere('slug_lang2', 'like', "%$content%")
            ->first();
    }

    public static function getSection4News($limit){
        $result = [];
         $subCategory = DB::table('news_category as nc')
            ->join('news_sub_category as nsc','nc.id','=','nsc.fk_news_category_id')
            ->where('nsc.status',1)
            ->whereIn('nsc.sub_category_route',
                    ['M.P.O-Non-M.P.O','Governmentization']
                )
            ->get(['nsc.*','nc.category_route']);
        foreach ($subCategory as $value){
            $result[json_encode($value)] =  DB::table('news_post as np')
                ->join('sub_category_wise_news as scwn','np.id','=','scwn.fk_news_post_id')
                ->Join('news_wise_image as nwi','np.id','=','nwi.fk_news_post_id')
                ->where('scwn.fk_news_sub_category_id',$value->id)
                ->where('np.status', 1)
                // ->where('np.news_type', 2)
                ->whereIn('np.news_type', [2,4,5])
                ->where('nwi.image_type', 1)
                ->orderBy('np.id', 'DESC')
                ->limit($limit)
                ->get([
                    'np.id as news_post_id',
                    'np.*',
                    'nwi.*'
                ]);
        }
        return $result;
    }
    public static function getSection4News2($limit){
        $result = [];
         $category = DB::table('news_category as nc')
            ->where('nc.status',1)
            ->whereIn('nc.category_route',
                    ['Recruitment-Test','BCS']
                )
            ->get(['nc.*']);
        foreach ($category as $value){
            $result[json_encode($value)] =  DB::table('news_post as np')
                ->join('category_wise_news as cwn','np.id','=','cwn.fk_news_post_id')
                ->Join('news_wise_image as nwi','np.id','=','nwi.fk_news_post_id')
                ->where('cwn.fk_news_category_id',$value->id)
                ->where('np.status', 1)
                // ->where('np.news_type', 2)
                ->whereIn('np.news_type', [2,4,5])
                ->where('nwi.image_type', 1)
                ->orderBy('np.id', 'DESC')
                ->limit($limit)
                ->get([
                    'np.id as news_post_id',
                    'np.*',
                    'nwi.*'
                ]);
        }
        return $result;
    }
    public static function getSection4News3($limit){
        // return 'hi';
        $result = [];
        $sub_category = DB::table('news_sub_category as nsc')
        ->join('news_category','nsc.fk_news_category_id','=','news_category.id')
            ->where('nsc.status',1)
            ->whereIn('nsc.sub_category_route',
                    ['Public-University','Private-University','National-University','Polytechnic-Institute']
                )
            ->get(['nsc.*',
                'news_category.category_route']);
            // dd($sub_category);
        foreach ($sub_category as $value){
            $result[json_encode($value)] =  DB::table('news_post as np')
                ->join('sub_category_wise_news as scwn','np.id','=','scwn.fk_news_post_id')
                ->Join('news_wise_image as nwi','np.id','=','nwi.fk_news_post_id')
                ->where('scwn.fk_news_sub_category_id',$value->id)
                ->where('np.status', 1)
                // ->where('np.news_type', 2)
                ->whereIn('np.news_type', [2,4,5])
                ->where('nwi.image_type', 1)
                ->orderBy('np.id', 'DESC')
                ->limit($limit)
                ->get([
                    'np.id as news_post_id',
                    'np.*',
                    'nwi.*'
                ]);
        }
        return $result;
    }
    public static function getSection3News($limit){
        $result = [];
         $subCategory = DB::table('news_category as nc')
            ->join('news_sub_category as nsc','nc.id','=','nsc.fk_news_category_id')
            ->where('nsc.status',1)
            ->whereIn('nsc.sub_category_route',
                    ['College','School','Madrasha']
                )
            ->get(['nsc.*','nc.category_route']);
        foreach ($subCategory as $value){
            $result[json_encode($value)] =  DB::table('news_post as np')
                ->join('sub_category_wise_news as scwn','np.id','=','scwn.fk_news_post_id')
                ->Join('news_wise_image as nwi','np.id','=','nwi.fk_news_post_id')
                ->where('scwn.fk_news_sub_category_id',$value->id)
                ->where('np.status', 1)
                // ->where('np.news_type', 2)
                ->whereIn('np.news_type', [2,4,5])
                ->where('nwi.image_type', 1)
                ->orderBy('np.id', 'DESC')
                ->limit($limit)
                ->get([
                    'np.id as news_post_id',
                    'np.*',
                    'nwi.*'
                ]);
        }
        return $result;
    }
}
