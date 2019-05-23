<?php
namespace App\Services\BackEnd;
use DB;
use Session;
use Lang;
use Image;
use App\Http\Helper;

class UserService{


//=======@@ Start Brand Section  @@=======

	public static function getUser(){
		$result = DB::table('users')
				  ->orderBy('id','DESC')
				  ->get();
		return $result;
	}

	public static function saveUser($data = null){
		try{
		DB::beginTransaction();
			$userId = DB::table('users')
						->insertGetId([
								'full_name' => $data['full_name'],
								'email'     => $data['email'],
								'mobile_no' => $data['mobile_no'],
								'status'     => 1
							]);
			if ($userId) {
				// $fileName = Helper::imageUpload($userId,$data['image'],'/images/user/');
				$fileName = Helper::imageUploadRaw($userId, $data['image'], '/images/user/');
		        $status =DB::table('users')
		                ->where('id', $userId)
		                ->update(['image' => $fileName]); 
			}
		DB::commit();
			return true;
		}catch(\Exception $e){
             $err_msg = \Lang::get("mysqlError.".$e->errorInfo[1]);
             return $err_msg;
        }
	}

	public static function saveEditUser($data = null){

		try{
			$status = DB::table('users')
				  ->where('id',$data['user_id'])
				  ->update([
						'full_name'  => $data['full_name'],
						'email'      => $data['email'],
						'mobile_no'  => $data['mobile_no'],
						'updated_at' => date("Y-m-d h-i-s")
					]);	
			if ($status) {
				return true;
			}else{
				return false;
			}
		}catch(\Exception $e){
             $err_msg = \Lang::get("mysqlError.".$e->errorInfo[1]);
             return $err_msg;
        }
	}

	public static function inactiveUser($id = null){
		try{
			$status = DB::table('users')
				  ->where('id',$id)
				  ->update([
						'status' => 0,
					]);	
			return true;
		}catch(\Exception $e){
             $err_msg = \Lang::get("mysqlError.".$e->errorInfo[1]);
             return $err_msg;
        }
	}

	public static function activeUser($id = null){
		try{
			$status = DB::table('users')
				  ->where('id',$id)
				  ->update([
						'status' => 1,
					]);	
			return true;
		}catch(\Exception $e){
             $err_msg = \Lang::get("mysqlError.".$e->errorInfo[1]);
             return $err_msg;
        }
	}

//=======@@ End Brand Section  @@=======

//=========Start admins ============

	public static function getAdmin(){
		$result = DB::table('admins')
				  ->orderBy('id','DESC')
				  ->get();
		return $result;
	}

	public static function saveAdmin($data = null){
		try{
		DB::beginTransaction();
		 $data['date_time'] = date("Y-m-d H:i:s");
			$userId = DB::table('admins')
						->insertGetId([
								'full_name'        => $data['name'],
								'email'            => $data['email'],
								'password'         => bcrypt($data['password']),
								'remember_token'   => $data['_token'],
								'admin_type'       => $data['admin_type'],
								'last_login_lang'  => 1,
								'status'           => 1

							]);
		DB::commit();
			return true;
		}catch(\Exception $e){
             $err_msg = \Lang::get("mysqlError.".$e->errorInfo[1]);
             return $err_msg;
        }
	}

	public static function saveEditAdmin($data = null){
		try{
			$status = DB::table('admins')
				  ->where('id',$data['admin_id'])
				  ->update([
				        'full_name'             => $data['name'],
						'email'            => $data['email'],
						'admin_type'       => $data['admin_type'],
						'last_login_lang'  => $data['language'],
						'updated_at'       => date("Y-m-d h-i-s")
					]);	
			if ($status) {
				return true;
			}else{
				return false;
			}
		}catch(\Exception $e){
             $err_msg = \Lang::get("mysqlError.".$e->errorInfo[1]);
             return $err_msg;
        }
	}

	public static function inactiveAdmin($id = null){
		try{
			$status = DB::table('admins')
				  ->where('id',$id)
				  ->update([
						'status' => 0,
					]);	
			return true;
		}catch(\Exception $e){
             $err_msg = \Lang::get("mysqlError.".$e->errorInfo[1]);
             return $err_msg;
        }
	}

	public static function activeAdmin($id = null){
		try{
			$status = DB::table('admins')
				  ->where('id',$id)
				  ->update([
						'status' => 1,
					]);	
			return true;
		}catch(\Exception $e){
             $err_msg = \Lang::get("mysqlError.".$e->errorInfo[1]);
             return $err_msg;
        }
	}

}