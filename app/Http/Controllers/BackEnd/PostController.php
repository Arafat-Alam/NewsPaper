<?php

namespace App\Http\Controllers\BackEnd;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Helper;
use App\Services\BackEnd\NewsPostService;
use App\Http\Requests\NewsPostRequest;
use DB;
use Datatables;


class PostController extends Controller
{
    public function newsPost()
    {
        //$getAllPost = null;
        $getAllPost = NewsPostService::getNewsPost();
        $getNewsType = NewsPostService::getNewsType();
        $getSubCategories = NewsPostService::getActiveNewsSubCategory();
        return view('backend.newsPost.newsPost', compact('getAllPost', 'getNewsType', 'getSubCategories'));
    }

    public function getNewsPostDatatable()
    {
        $newsPosts = DB::table('news_post as np')->orderBy('np.id','DESC')
            ->select(['np.*']);

        return Datatables::of($newsPosts)
            ->addColumn('news_type', function ($news) {
                $btn = '';
                if ($news->news_type == 1) {
                    $btn = '<span class="label label-xs label-primary"> Normal</span>';
                }elseif ($news->news_type == 2) {
                    $btn = '<span class="label label-xs label-primary"> Selected</span>';
                }elseif ($news->news_type == 3) {
                    $btn = '<span class="label label-xs label-primary"> Breaking</span>';
                }elseif ($news->news_type == 4) {
                    $btn = '<span class="label label-xs label-primary"> Top</span>';
                }elseif ($news->news_type == 5) {
                    $btn = '<span class="label label-xs label-primary"> Top Partial</span>';
                }
                return $btn;
            })
            ->addColumn('status', function ($news) {
                return ($news->status == 1) ? '<span class="label label-xs label-success"> active</span>' : '<span class="label label-xs label-danger"> Inactive</span>';
            })
            ->addColumn('action', function ($news) {
                $btn = ($news->status == 1) ? '<a href="inactiveNews' . '/' . $news->id . '" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Inactive</a>': '<a href="activeNews' . '/' . $news->id . '" class="btn btn-xs btn-success"><i class="fa fa-trash"></i> active</a>';
                return '<a href="editNewsPost' . '/' . $news->id . '" class="btn btn-xs btn-primary"><i class="fa fa-edit"></i>Edit</a> '.$btn;
            })
            ->make(true);
    }

    public function getSubCategoryBySelectedCategory(Request $request)
    {
        return NewsPostService::getSubCategoryBySelectedCategory($request->all());
    }

    public function saveNewsPost(NewsPostRequest $request)
    {
        $saveStatus = NewsPostService::saveNewsPost($request->all());
        if ($saveStatus === true) {
            return redirect()->back()->with('flash_success', 'News Save Successfull.');
        } else {
            return redirect()->back()->with('flash_error', $saveStatus);
        }
    }

    public function editNewsPost($newsPostId = null)
    {
        $getNewsType = NewsPostService::getNewsType();
        $getSubCategories = NewsPostService::getActiveNewsSubCategory();
        $news = NewsPostService::getNewsById($newsPostId);
        return view('backend.newsPost.editNewsPost',
            compact('news', 'getAllPost', 'getNewsType', 'getSubCategories'));
    }

    public function saveEditNewsPostDetails(Request $request)
    {
        $saveStatus = NewsPostService::editNewsPostDetails($request->all());
        if ($saveStatus === true) {
            return redirect()->back()->with('flash_success', 'News Update Successfull.');
        } else {
            return redirect()->back()->with('flash_error', $saveStatus);
        }
    }

    public function saveEditNewsPostImage(Request $request)
    {
        $saveStatus = NewsPostService::saveEditNewsPostImage($request->all());
        if ($saveStatus === true) {
            return redirect()->back()->with('flash_success', 'News Save Successfull.');
        } else {
            return redirect()->back()->with('flash_error', $saveStatus);
        }
    }

    public function removeNewsPostImage(Request $request)
    {
        $saveStatus = NewsPostService::removeNewsPostImage($request->all());
        if ($saveStatus == true) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => true]);
        }
    }

    public function inactiveNews($newsId = null)
    {
        $status = NewsPostService::inactiveNews($newsId);
        if ($status === true) {
            return redirect('newsPost')->with('success' ,"News Inactive Successfull.");
        } else {
            return redirect('newsPost')->with('error' ,$status);
        }
    }

    public function activeNews($newsId = null)
    {
        $status = NewsPostService::activeNews($newsId);
        if ($status === true) {
            return redirect('newsPost')->with('success' ,"News Active Successfull.");
        } else {
            return redirect('newsPost')->with('error' ,$status);
        }
    }

    public function newsPriorityView()
    {
        $priorityNews = NewsPostService::getAllPriorityWiseNews();
        $allNews = NewsPostService::getAllNewsForPriorityAssign();
        return view('backend.newsPost.newsPriorityView', compact('priorityNews','allNews'));
    }

    public function inactiveNewsPriority($newsPriorityId = null)
    {
        $status = NewsPostService::inactiveNewsPriority($newsPriorityId);
        if ($status === true) {
            return redirect('newsPriorityView')->with('success' ,"News Inactive Successfull.");
        } else {
            return redirect('newsPriorityView')->with('error' ,$status);
        }
    }

    public function activeNewsPriority($newsPriorityId = null)
    {
        $status = NewsPostService::activeNewsPriority($newsPriorityId);
        if ($status === true) {
            return redirect('newsPriorityView')->with('success' ,"News Inactive Successfull.");
        } else {
            return redirect('newsPriorityView')->with('error' ,$status);
        }
    }

    public function saveEditNewsPriority(Request $request)
    {   
        $saveStatus = NewsPostService::saveEditNewsPriorityAssign($request->all());
        if ($saveStatus === true) {
            return response()->json(['success' => true, 'status' => "News Priority Set Successfull."]);
        } elseif ($saveStatus === false) {
            return response()->json(['error' => true, 'status' => "This Position Another Already Active."]);
        } else {
            return response()->json(['error' => true, 'status' => $saveStatus]);
        }
    }
}
