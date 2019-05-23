<?php
namespace App\Services\BackEnd;

use DB;
use Session;
use Lang;
use Image;
use App\Http\Helper;
use App\Http\Controllers\BackEnd\Controller;

class BasicSetupService
{
//=======@@ Start Category Section  @@=======

    public static function getCategory()
    {
        $result = DB::table('news_category as nc')
            ->leftJoin('news_position as np','nc.fk_news_position_id','=','np.id')
            ->orderBy('id', 'DESC')
            ->get([
                'nc.*','np.position_title'
            ]);
        return $result;
    }

    public static function getNewsPosition()
    {
        return DB::table('news_position')
            ->orderBy('id', 'ASC')
            ->get();
    }

    public static function getCategoryByid($categoryId)
    {
        return DB::table('news_category')
            ->where('id', $categoryId)
            ->first();
    }

    public static function saveCategory($data = null)
    {
        try {
            DB::beginTransaction();
            $categoryId = DB::table('news_category')
                ->insert([
                    'menu_show'           => $data['menu_show'],
                    'category_name_lang1' => $data['category_name_lng1'],
                    'category_name_lang2' => $data['category_name_lng2'],
                    'category_route'      => $data['category_route'],
                    'fk_news_position_id' => $data['news_position_id'],
                    'created_at'          => date('Y-m-d h:i:s'),
                    'created_by'          => Session::get('admin.id'),
                    'status'              => 1,
                    'created_at'          => date('Y-m-d h:i:S'),
                    'created_by'          => Session::get('admin.id'),
                ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function saveEditCategory($data = null)
    {
        try {
            $status = DB::table('news_category')
                ->where('id', $data['category_id'])
                ->update([
                    'menu_show'           => $data['menu_show'],
                    'category_name_lang1' => $data['category_name_lang1'],
                    'category_name_lang2' => $data['category_name_lang2'],
                    'category_route'      => $data['category_route'],
                    'fk_news_position_id' => $data['news_position_id'],
                    'updated_by'          => Session::get('admin.id'),
                    'updated_at'          => date("Y-m-d h-i-s")
                ]);
            if ($status) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            //return $e;
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function inactiveCategory($id = null)
    {
        try {
            $status = DB::table('news_category')
                ->where('id', $id)
                ->update([
                    'status' => 0,
                ]);
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function activeCategory($id = null)
    {
        try {
            $status = DB::table('news_category')
                ->where('id', $id)
                ->update([
                    'status' => 1,
                ]);
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

//=======@@ End Category Section  @@=======

//=======@@ Start Sub Category Section  @@=======
    public static function getActiveCategory()
    {
        $result = DB::table('news_category')
            ->where('status', 1)
            ->get();
        return $result;
    }

    public static function saveSubCategory($data = null)
    {
        try {
            $subCategoryId = DB::table('news_sub_category')
                ->insert([
                    'fk_news_category_id'       => $data['category_name_id'],
                    'sub_category_name_lang1'   => $data['sub_category_name_lng1'],
                    'sub_category_name_lang2'   => $data['sub_category_name_lng2'],
                    'sub_category_route'        => $data['sub_category_route'],
                    'status' => 1,
                    'created_at' => date('Y-m-d h:i:s'),
                    'created_by' => Session::get('admin.id'),
                ]);
            DB::commit();
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    /*public static function getSubCategoryByid($subCategoryId = null)
    {
        $result = DB::table('news_sub_category')
            ->select([
                'news_sub_category.id',
                'news_sub_category.sub_category_name_lang1',
                'news_sub_category.sub_category_name_lang2',
                'news_sub_category.status',
                'news_category.category_name_lang1',
                'news_category.id as category_id',
            ])
            ->join('news_category', 'news_sub_category.fk_news_category_id', '=', 'news_category.id')
            ->where('news_sub_category.id', $subCategoryId)
            ->first();
        return $result;
    }*/

    public static function getSubCategory()
    {
        $result = DB::table('news_sub_category')
            ->join('news_category', 'news_sub_category.fk_news_category_id', '=', 'news_category.id')
            ->orderBy('news_sub_category.id', 'DESC')
            ->get([
                'news_sub_category.id',
                'news_sub_category.sub_category_name_lang1',
                'news_sub_category.sub_category_name_lang2',
                'news_sub_category.sub_category_route',
                'news_sub_category.status',
                'news_category.category_name_lang1',
                'news_category.id as category_id',
            ]);
        return $result;
    }

    public static function saveEditSubCategory($data = null)
    {
        try {
            //return $data;
            $status = DB::table('news_sub_category')
                ->where('id', $data['sub_category_id'])
                ->update([
                    'fk_news_category_id'       => $data['category_name_id'],
                    'sub_category_name_lang1'   => $data['sub_category_name_lang1'],
                    'sub_category_name_lang2'   => $data['sub_category_name_lang2'],
                    'sub_category_route'        => $data['sub_category_route'],
                    'updated_at'                => date('Y-m-d h:i:s'),
                    'updated_by'                => Session::get('admin.id'),
                ]);
            if ($status) {
                return true;
            } else {
                return false;
            }
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function inactiveSubCategory($id = null)
    {
        try {
            $status = DB::table('news_sub_category')
                ->where('id', $id)
                ->update([
                    'status' => 0,
                ]);
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

    public static function activeSubCategory($id = null)
    {
        try {
            $status = DB::table('news_sub_category')
                ->where('id', $id)
                ->update([
                    'status' => 1,
                ]);
            return true;
        } catch (\Exception $e) {
            $err_msg = \Lang::get("mysqlError." . $e->errorInfo[1]);
            return $err_msg;
        }
    }

//=======@@ End Sub Category Section  @@=======

}