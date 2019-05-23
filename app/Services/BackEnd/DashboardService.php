<?php
namespace App\Services\BackEnd;
use DB;
use Session;
use Lang;
use Image;
use App\Http\Helper;

class DashboardService{


//=======@@ Start Brand Section  @@=======

	public static function getdata(){
		$allInformation = array();
		$totalNewsViewerToday = 0;
		$getTotalNewsViewer = DB::table('news_post')
				  	->whereBetween('created_at',['2016-08-11 00:00:01','2016-08-11 23:59:59'])
				  	->where('status',1)
				  	->get(['view_counter']);
		foreach($getTotalNewsViewer as $newsViewer){
			$totalNewsViewerToday = $totalNewsViewerToday + $newsViewer->view_counter;
		}
		$totalPostToday = DB::table('news_post')
				  	->whereBetween('created_at',['2016-08-11 00:00:01','2016-08-11 23:59:59'])
				  	->where('status',1)
				  	->count();

	    $totalPost = DB::table('news_post')
	    			->where('status',1)
	    			->count();

		$totalCategory = DB::table('news_category')
					->count();

		$allInformation['totalNewsViewerToday'] 	= $totalNewsViewerToday;
		$allInformation['totalPostToday'] 			= $totalPostToday;
		$allInformation['totalPost'] 				= $totalPost;
		$allInformation['totalCategory'] 			= $totalCategory;
		return $allInformation;
	}

	

}