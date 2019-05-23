<?php

namespace App\Http\Controllers\FrontEnd;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Services\FrontEnd\FrontEndService;
use App\Services\FrontEnd\IndexService;
use Cookie;
use Session;
use DB;
use App\Http\Helper as Helper;

use EasyBanglaDate\Types\BnDateTime;
use EasyBanglaDate\Types\DateTime as EnDateTime;

class IndexController extends Controller
{
    public function getMenues()
    {
        if ( ! Session::has('frontend_lang')){
            Session::set('frontend_lang',2);
        }
        $menues         = IndexService::getMenu();
        Session::set('menues',$menues);
    }
    
    public function index()
    {
        // return 'hi';
        $priorityWiseNews    = IndexService::getPriorityWiseTopNews(14);
        // print_r($priorityWiseNews);
        // exit;
        $headingBreckingNews = IndexService::getHeadingAndBreakingNews();
        $mostReadNewses      = IndexService::getMostReadNews(10);

        $thumnailSubCats     = IndexService::getThumbnailSubCategory();
        $jobNewses           = IndexService::getCategoryWiseNews('Job-News',8);
        $readerOpinions      = IndexService::getSubCategoryWiseNews('Readers-Opinion',5);
        $interviewNewses     = IndexService::getSubCategoryWiseNews('Interview',5);
        $campusStarNewses    = IndexService::getSubCategoryWiseNews('Campus-Star',5);
        $womensStudy         = IndexService::getSubCategoryWiseNews('Women-Chapter',7);
        $artAndLiteratures   = IndexService::getSubCategoryWiseNews('Art-And-Literature',5);
        $admissionInformations = IndexService::getSubCategoryWiseNews('Admission-Info',5);
        $unquestionablyNewses  = IndexService::getSubCategoryWiseNews('General-Knowledge',5);
        $columnNewses        = IndexService::getColumnNewses('Colume',4);
        $campusNewses        = IndexService::getCampusNews('campus-news',4);
        $sportsNewses        = IndexService::getCategoryWiseNews('Sports-News',7);
        $entertainmentNewses = IndexService::getCategoryWiseNews('Entertainment',8);
        $moreNewses          = IndexService::getCategoryWiseNews('More-News',8);
        $section4Newses      = IndexService::getSection4News(4);
        $section4Newses2     = IndexService::getSection4News2(4);
        $section4Newses3      = IndexService::getSection4News3(4);
        // dd($section4Newses3);
        $section3Newses      = IndexService::getSection3News(4);
        
        return view('frontend.index', compact(
            'headingBreckingNews','photoGallery','priorityWiseNews','mostReadNewses',
            'latestNews','thumnailSubCats','jobNewses','readerOpinions','interviewNewses',
            'campusStarNewses','columnNewses','campusNewses','artAndLiteratures',
            'admissionInformations','unquestionablyNewses','sportsNewses','entertainmentNewses',
            'moreNewses','section4Newses','section4Newses2','womensStudy','section4Newses3','section3Newses'
        ));
    }

    public function newsDetails($newsPostId = null)
    {
        $newsPostId = Helper::decodeId($newsPostId);
        $this->getMenues();
        IndexService::newsViewCount($newsPostId);
        $headingBreckingNews  = IndexService::getHeadingAndBreakingNews();
        $singleNews          = IndexService::newsDetails($newsPostId);
        $getRelatedNews       = IndexService::getRelatedNews($newsPostId);
        $newsWiseLatestNews   = IndexService::newsWiseLatestNews($newsPostId,10);
        $newsWiseMostReadNews = IndexService::newsWiseMostReadNews($newsPostId,10);
        return view('frontend.newsDetails', compact(
            'singleNews','getRelatedNews','newsWiseLatestNews', 
            'newsWiseMostReadNews', 'headingBreckingNews'
        ));
    }

    public function categoryWiseNews($categoryRoute = null)
    {
        $this->getMenues();
        $headingBreckingNews = IndexService::getHeadingAndBreakingNews();
        $category            = IndexService::getCategoryByCategoryName($categoryRoute);
        $categoryNewses      = IndexService::categoryWiseNews($category->id,23);
        return view('frontend.categoryWiseNews', compact(
            'headingBreckingNews','categoryNewses','catWiselatestNews','mostReadNewses',
            'selectedNewses','allCategoryNews','photoGallery','category'
        ));
    }

    public function subCategoryWiseNews($categoryRoute = null,$subCategoryRoute = null)
    {
        $this->getMenues();
        $headingBreckingNews = IndexService::getHeadingAndBreakingNews();
        $subCategory         = IndexService::getSubCategoryByCategoryName($subCategoryRoute);
        $categoryNewses      = IndexService::subCategoryWiseNews($subCategory->id,23);
        return view('frontend.categoryWiseNews', compact(
            'headingBreckingNews','categoryNewses','catWiselatestNews','mostReadNewses',
            'selectedNewses','allCategoryNews','photoGallery','category'
        ));
    }

    public function mostRecent()
    {
        $this->getMenues();
        $headingBreckingNews = IndexService::getHeadingAndBreakingNews();
        $categoryNewses      = IndexService::mostRecent(23);
        return view('frontend.mostRecentNews', compact('headingBreckingNews','categoryNewses'));
    }

    public function frontendLangChange($langType = null)
    {
        Session::set('frontend_lang',$langType);
        return response()->json(['success'=>true]);
    }

    public function searchNews(Request $request)
    {
        if (empty($request->search_content)){
            return redirect()->back()->with('flash_errror','Dont Match Anything..!');
        }
        $news = IndexService::searchNews($request->all());
        if (count($news) < 1){
            return redirect()->back();
        }
        return $this->newsDetails($news->id);
    }

}
