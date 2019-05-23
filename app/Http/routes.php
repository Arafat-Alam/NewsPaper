<?php
Route::get('test',function(){
	return bcrypt(123450);
});	

Route::group(['middleware' => 'frontend'], function () {
	Route::get('/',
		[   'as' => '/',
			'uses' => 'IndexController@index'
		]);

	Route::get('newsDetails/{newsPostId}/{slug}',
		[   'as' => 'newsDetails',
			'uses' => 'IndexController@newsDetails'
		]);

	Route::get('category/{categoryRoute}',
		[   'as' => 'categoryWiseNews',
			'uses' => 'IndexController@categoryWiseNews'
		]);

	Route::get('sub-category/{categoryRoute}/{subCategoryRoute}',
		[   'as' => 'subCategoryWiseNews',
			'uses' => 'IndexController@subCategoryWiseNews'
		]);

	Route::get('frontendLangChange/{langType}',
		[   'as' => 'frontendLangChange',
			'uses' => 'IndexController@frontendLangChange'
		]);

	Route::post('searchNews',
		[   'as' => 'searchNews.post',
			'uses' => 'IndexController@searchNews'
		]);

	Route::get('most-recent',
		[   'as' => 'category/most-recent',
			'uses' => 'IndexController@mostRecent'
		]);
});


/*Route::get('password',function(){
	return Hash::make('mahfuz2011');
});*/

