<?php
namespace App\Services\BackEnd;
use DB;
use Session;
use Lang;
use Image;
use App\Http\Helper;

class PhotoGalleryService{


//=======@@ Start Brand Section  @@=======

	public static function getPhotoGallery(){
		$result = DB::table('photo_gallery')
				  ->orderBy('status','DESC')
				  ->orderBy('id','DESC')
				  ->get();
		return $result;
	}

	public static function savePhotoGallery($data = null){
		try{
			$photoId = DB::table('photo_gallery')
						->insertGetId([
								'image_title_lang1' => $data['photo_title_lang1'],
								'image_title_lang2' => $data['photo_title_lang2'],
								'adds_url' 			=> $data['adds_url'],
								'adds_position' 	=> $data['adds_position'],
								'status'			=> 1,
								]);

			if($photoId) {
				// $fileName = Helper::imageUpload($photoId, $data['photo'],'/images/PhotoGallery/',325,570);
				$fileName = Helper::imageUploadRaw($photoId, $data['photo'], '/images/PhotoGallery/',325,570);
		        $status =DB::table('photo_gallery')
		                ->where('id', $photoId)
		                ->update(['image_path' => $fileName]); 
			}
			return true;
		}catch(\Exception $e){
             $err_msg = \Lang::get("mysqlError.".$e->errorInfo[1]);
             return $err_msg;
        }
	}

	public static function getPhotoGalleryById($photoGalleryid = null){

		$result = DB::table('photo_gallery')
				  ->where('id',$photoGalleryid)
				  ->first();
		return $result;
	}

	public static function saveEditPhotoGallery($data = null){
		
		try{
			$status = DB::table('photo_gallery')
				  ->where('id',$data['id'])
				  ->update([
						'image_title_lang1'  => $data['photo_title_lang1'],
						'image_title_lang2'  => $data['photo_title_lang2'],
						'adds_url' 			 => $data['adds_url'],
						'adds_position' 	 => $data['adds_position']
					]);	

				  if(isset($status)) {
				// $fileName = Helper::imageUpload($data['id'], $data['photo'],'/images/PhotoGallery/',325,570);
				$fileName = Helper::imageUploadRaw($data['id'], $data['photo'], '/images/PhotoGallery/',325,570);
		        $status =DB::table('photo_gallery')
		                ->where('id', $data['id'])
		                ->update(['image_path' => $fileName]); 
			}
			return true;
		}catch(\Exception $e){
			//return $e;
             $err_msg = \Lang::get("mysqlError.".$e->errorInfo[1]);
             return $err_msg;
        }
	}

	public static function inactivePhotoGallery($id = null){
		try{
			$status = DB::table('photo_gallery')
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

	public static function activePhotoGallery($id = null){
		try{
			$status = DB::table('photo_gallery')
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

}