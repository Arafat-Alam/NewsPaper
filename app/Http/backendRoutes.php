<?php

	Route::get('login',
	    [   'as' => 'login',
	        'uses' => 'LoginController@login'
	    ]);

	Route::post('login',
	    [   'as' => 'login.post',
	        'uses' => 'LoginController@checkLogin'
	    ]);

	Route::get('logout',
	    [   'as' => 'logout',
	        'uses' => 'LoginController@logout'
	    ]);

//===== Star category Section=====

Route::group(['middleware' => 'adminAuth'], function () {
	
	Route::get('dashboard',
	    [   'as' => 'dashboard',
	        'uses' => 'DashboardController@dashboard'
	    ]);

	Route::get('langChange/{langType}',
	    [   'as' => 'langChange',
	        'uses' => 'DashboardController@langChange'
	    ]);

	Route::get('category',
	    [   'as' => 'category',
	        'uses' => 'BasicSetupController@category'
	    ]);

	Route::post('category',
	    [   'as' => 'category.post',
	        'uses' => 'BasicSetupController@saveCategory'
	    ]);

	Route::get('categoryEditModal/{categoryId}/action',
	    [   'as' => 'categoryEditModal',
	        'uses' => 'BasicSetupController@categoryEditModal'
	    ]);

	Route::get('saveEditCategory',
	    [   'as' => 'saveEditCategory',
	        'uses' => 'BasicSetupController@saveEditCategory'
	    ]);

	Route::get('inactiveCategory/{id}',
	    [   'as' => 'inactiveCategory',
	        'uses' => 'BasicSetupController@inactiveCategory'
	    ]);

	Route::get('activeCategory/{id}',
	    [   'as' => 'activeCategory',
	        'uses' => 'BasicSetupController@activeCategory'
	    ]);
//===== End category Section=====

//===== Start Sub category Section=====
	Route::get('subCategory',
	    [   'as' => 'subCategory',
	        'uses' => 'BasicSetupController@subCategory'
	    ]);

	Route::post('subCategory',
	    [   'as' => 'subCategory.post',
	        'uses' => 'BasicSetupController@saveSubCategory'
	    ]);

	Route::get('saveEditSubCategory',
	    [   'as' => 'saveEditSubCategory',
	        'uses' => 'BasicSetupController@saveEditSubCategory'
	    ]);

	Route::get('inactiveSubCategory/{id}',
	    [   'as' => 'inactiveSubCategory',
	        'uses' => 'BasicSetupController@inactiveSubCategory'
	    ]);

	Route::get('activeSubCategory/{id}',
	    [   'as' => 'activeSubCategory',
	        'uses' => 'BasicSetupController@activeSubCategory'
	    ]);

//===== End Sub category Section=====

//===== Start Post Section=====

	Route::get('newsPost',
	    [   'as' => 'newsPost',
	        'uses' => 'PostController@newsPost'
	    ]);

	Route::get('getNewsPostDatatable',
	    [   'as' => 'getNewsPostDatatable',
	        'uses' => 'PostController@getNewsPostDatatable'
	    ]);


	Route::get('getSubCategoryBySelectedCategory',
		[   'as' => 'getSubCategoryBySelectedCategory',
			'uses' => 'PostController@getSubCategoryBySelectedCategory'
		]);


	Route::get('inactiveNews/{newsId}',
		[   'as' => 'inactiveNews',
			'uses' => 'PostController@inactiveNews'
		]);

	Route::get('activeNews/{newsId}',
		[   'as' => 'activeNews',
			'uses' => 'PostController@activeNews'
		]);

	Route::post('newsPost',
		[   'as' => 'newsPost.post',
			'uses' => 'PostController@saveNewsPost'
		]);

	Route::get('editNewsPost/{newsPostId}',
		[   'as' => 'editNewsPost',
			'uses' => 'PostController@editNewsPost'
		]);

	Route::post('editNewsPostDetails',
		[   'as' => 'editNewsPostDetails',
			'uses' => 'PostController@saveEditNewsPostDetails'
		]);

	Route::post('editNewsPostImage',
		[   'as' => 'editNewsPostImage',
			'uses' => 'PostController@saveEditNewsPostImage'
		]);

	Route::get('removeNewsPostImage',
		[   'as' => 'removeNewsPostImage',
			'uses' => 'PostController@removeNewsPostImage'
		]);

	Route::get('newsPriorityView',
		[   'as' => 'newsPriorityView',
			'uses' => 'PostController@newsPriorityView'
		]);

	Route::get('inactiveNewsPriority/{newsPriorityId}',
		[   'as' => 'inactiveNewsPriority',
			'uses' => 'PostController@inactiveNewsPriority'
		]);

	Route::get('activeNewsPriority/{newsPriorityId}',
		[   'as' => 'activeNewsPriority',
			'uses' => 'PostController@activeNewsPriority'
		]);

	Route::get('saveEditNewsPriority',
		[   'as' => 'saveEditNewsPriority',
			'uses' => 'PostController@saveEditNewsPriority'
		]);

//===== End Post Section=====


//===== Start Photo Gallery Section=====

	Route::get('adds',
	    [   'as' => 'adds',
			'uses' => 'PhotoGalleryController@photoGallery'
	    ]);


	Route::post('adds',
	    [   'as' => 'adds.post',
	        'uses' => 'PhotoGalleryController@photoGallerySave'
	    
	    ]);

	Route::get('photoGalleryEditModal/{photoGalleryId}/action',
	    [   'as' => 'photoGalleryEditModal',
	        'uses' => 'PhotoGalleryController@photoGalleryEditModal'
	    ]);

	Route::post('saveEditAdd',
	    [   'as' => 'saveEditAdd.post',
	        'uses' => 'PhotoGalleryController@saveEditPhotoGallery'
	    ]);

	Route::get('inactivePhotoGallery/{id}',
	    [   'as' => 'inactivePhotoGallery',
	        'uses' => 'PhotoGalleryController@inactivePhotoGallery'
	    ]);

	Route::get('activePhotoGallery/{id}',
	    [   'as' => 'activePhotoGallery',
	        'uses' => 'PhotoGalleryController@activePhotoGallery'
	    ]);
	
//===== End Photo Gallery Section=====


//====== start User Section ========
	Route::get('user',
	    [   'as' => 'user',
	        'uses' => 'UserController@user'
	    ]);

	Route::post('user',
	    [   'as' => 'user.post',
	        'uses' => 'UserController@saveUser'
	    ]);

	Route::get('saveEditUser',
	    [   'as' => 'saveEditUser',
	        'uses' => 'UserController@saveEditUser'
	    ]);

	Route::get('inactiveUser/{id}',
	    [   'as' => 'inactiveUser',
	        'uses' => 'UserController@inactiveUser'
	    ]);

	Route::get('activeUser/{id}',
	    [   'as' => 'activeUser',
	        'uses' => 'UserController@activeUser'
	    ]);

//============== Admin Section =============
	
	Route::get('admin',
		    [   'as' => 'admin',
		        'uses' => 'UserController@admin'
		    ]);

	Route::post('admin',
		    [   'as' => 'admin.post',
		        'uses' => 'UserController@saveAdmin'
		    ]);

	Route::get('saveEditAdmin',
	    [   'as' => 'saveEditAdmin',
	        'uses' => 'UserController@saveEditAdmin'
	    ]);

	Route::get('activeAdmin/{adminId}',
	    [   'as' => 'activeAdmin',
	        'uses' => 'UserController@activeAdmin'
	    ]);

	Route::get('inactiveAdmin/{adminId}',
	    [   'as' => 'inactiveAdmin',
	        'uses' => 'UserController@inactiveAdmin'
	    ]);

//======= End Admin Section ======================

//============== Start Dashboard Section ===============

	Route::get('profile',
	    [   'as' => 'profile',
	        'uses' => 'DashboardController@profile'
	    ]);
	
	Route::post('profile',
	    [   'as' => 'profile.post',
	        'uses' => 'DashboardController@saveProfile'
	    ]);

	Route::get('systemSetting',
		    [   'as' => 'systemSetting',
		        'uses' => 'DashboardController@systemSetting'
		    ]);
	Route::post('systemSetting',
		    [   'as' => 'systemSetting.post',
		        'uses' => 'DashboardController@saveSystemSetting'
		    ]);

	Route::get('checkAdminPassword',
		    [   'as' => 'checkAdminPassword',
		        'uses' => 'DashboardController@checkAdminPassword'
		    ]);

	Route::post('saveAdminPassword',
		    [   'as' => 'saveAdminPassword.post',
		        'uses' => 'DashboardController@saveAdminPassword'
		    ]);

//=============== End Dashboard Section ===================


});

/*Route::get('test',function(){
	$data = DB::table('news_sub_category')
		->whereIn('id',[1,2,3,4,5])
		->pluck('fk_news_category_id');
	return $data[0];
});*/

