<?php

namespace App\Http\Controllers\BackEnd;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Requests\CategoryRequest;
use App\Http\Requests\SubCategoryRequest;
use App\Http\Requests\SpecificationRequest;
use App\Http\Requests\CityRequest;
use App\Http\Requests\ColorRequest;
use App\Http\Requests\BrandRequest;
use App\Http\Requests\SizeRequest;
use App\Http\Requests\TagRequest;
use App\Http\Requests\DivisionRequest;
use App\Http\Requests\incomeExpenseHeadRequest;
use App\Services\BackEnd\BasicSetupService;

class BasicSetupController extends Controller
{

//=======@@ Start Category Section  @@=======
    public function category()
    {
        $categories  = BasicSetupService::getCategory();
        $newsPosition       = BasicSetupService::getNewsPosition();
        return view('backend.basicSetup.category', compact('newsPosition', 'categories'));
    }

    public function saveCategory(CategoryRequest $request)
    {
        $saveCategory = BasicSetupService::saveCategory($request->all());
        if ($saveCategory === true) {
            return redirect()->route('category')->with('success', 'Save Category Successfull.');
        } elseif ($saveCategory == 'tooLarge') {
            return redirect()->route('category')->with('error', 'Image Too Large. Your Image Size Must Less Than 1000KB');
        } else {
            return redirect()->route('category')->with('error', $saveCategory);
        }
    }

    public function categoryEditModal($categoryId = null)
    {
        $category = BasicSetupService::getCategoryByid($categoryId);
        return view('backend.basicSetup.categoryEditModal', compact('category'));
    }

    public function saveEditCategory(Request $request)
    {
        $saveEditCategory = BasicSetupService::saveEditCategory($request->all());
        if ($saveEditCategory === true) {
            return response()->json(['success' => true, 'status' => "Update Category Successfull."]);
        } elseif ($saveEditCategory == false) {
            return response()->json(['error' => true, 'status' => "No Change Occour."]);
        } else {
            return response()->json(['error' => true, 'status' => $saveEditCategory]);
        }
    }


    public function inactiveCategory($id = null)
    {
        $saveEditCategory = BasicSetupService::inactiveCategory($id);
        if ($saveEditCategory === true) {
            return response()->json(['success' => true, 'status' => "Category Inactive Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $saveEditCategory]);
        }
    }

    public function activeCategory($id = null)
    {
        $activeCategory = BasicSetupService::activeCategory($id);
        if ($activeCategory === true) {
            return response()->json(['success' => true, 'status' => "Category Active Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $activeCategory]);
        }
    }

//=======@@ End Category Section  @@=======

//=======@@ Start Sub Category Section  @@=====
    public function subCategory()
    {
        $categories     = BasicSetupService::getActiveCategory();
        $subCategories  = BasicSetupService::getSubCategory();
        return view('backend.basicSetup.subCategory', compact('categories', 'subCategories'));
    }

    public function saveSubCategory(SubCategoryRequest $request)
    {
        $subCategory = BasicSetupService::saveSubCategory($request->all());
        if ($subCategory === true) {
            return redirect()->route('subCategory')->with('success', 'Save Sub-Category Successfull.');
        } elseif ($subCategory == 'tooLarge') {
            return redirect()->route('subCategory')->with('error', 'Image Too Large. Your Image Size Must Less Than 1000KB');
        } else {
            return redirect()->route('subCategory')->with('error', $subCategory);
        }
    }

    public function saveEditSubCategory(Request $request)
    {
        $editSubCategory = BasicSetupService::saveEditSubCategory($request->all());
        if ($editSubCategory === true) {
            return response()->json(['success' => true, 'status' => "Sub-Category Update Successfull."]);
        } elseif ($editSubCategory == false) {
            return response()->json(['error' => true, 'status' => "No Change Occour."]);
        } else {
            return response()->json(['error' => true, 'status' => $editSubCategory]);
        }
    }

    public function inactiveSubCategory($id = null)
    {
        $inactiveSubCategory = BasicSetupService::inactiveSubCategory($id);
        if ($inactiveSubCategory === true) {
            return response()->json(['success' => true, 'status' => "Sub-Category Inactive Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $inactiveSubCategory]);
        }
    }

    public function activeSubCategory($id = null)
    {
        $activeSubCategory = BasicSetupService::activeSubCategory($id);
        if ($activeSubCategory === true) {
            return response()->json(['success' => true, 'status' => "Sub-Category Active Successfull.", window . location('category')]);
        } else {
            return response()->json(['error' => true, 'status' => $activeSubCategory]);
        }
    }
//=======@@ End Sub Category Section  @@=====

//=======@@ Start Sub Cetagory Details Section  @@======

    public function subCategoryDetails()
    {
        $subCategories = BasicSetupService::getActiveSubCategory();
        $subCategoriesDetail = BasicSetupService::getSubCategoryDetails();
        return view('backend.basicSetup.subCategoryDetails', compact('subCategories', 'subCategoriesDetail'));
    }

    public function subCategoryDetailsSave(SubCategoryDetailsRequest $request)
    {

        $subCategory = BasicSetupService::subCategoryDetailsSave($request->all());


        if ($subCategory === true) {
            return redirect()->route('subCategoryDetails')->with('success', 'Save Sub-Category Details Successfull.');
        } else {
            return redirect()->route('subCategoryDetails')->with('error', $subCategory);
        }
    }

    public function saveEditSubCategoryDetails(Request $request)
    {
        $editDetails = BasicSetupService::saveEditSubCategoryDetails($request->all());
        if ($editDetails === true) {
            return response()->json(['success' => true, 'status' => "Update Sub Category Details Successfull."]);
        } elseif ($editDetails == false) {
            return response()->json(['error' => true, 'status' => "No Change Occour."]);
        } else {
            return response()->json(['error' => true, 'status' => $editDetails]);
        }
    }

    public function inactiveSubCategoryDetails($id = null)
    {
        $inactiveSubCategoryDetails = BasicSetupService::inactiveSubCategoryDetails($id);
        if ($inactiveSubCategoryDetails === true) {
            return response()->json(['success' => true, 'status' => "Sub Category Details Inactive Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $inactiveSubCategoryDetails]);
        }
    }

    public function activeSubCategoryDetails($id = null)
    {
        $activeSubCategoryDetails = BasicSetupService::activeSubCategoryDetails($id);
        if ($activeSubCategoryDetails === true) {
            return response()->json(['success' => true, 'status' => "Sub Category Details Active Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $activeSubCategoryDetails]);
        }
    }


//=======@@ End Sub Category Details Section  @@=======

//=======@@ Start Division Section  @@======

    public function division()
    {
        $division = BasicSetupService::getDivision();

        return view('backend.basicSetup.division', compact('division'));
    }

    public function saveDivision(DivisionRequest $request)
    {
        $saveDivision = BasicSetupService::saveDivision($request->all());

        if ($saveDivision === true) {
            return redirect()->route('division')->with('success', 'Division Save Successfull.');
        } else {
            return redirect()->route('division')->with('error', $saveDivision);
        }
    }

    public function saveEditDivision(Request $request)
    {
        $editDivision = BasicSetupService::saveEditDivision($request->all());
        if ($editDivision === true) {
            return response()->json(['success' => true, 'status' => "Update Successfull."]);
        } elseif ($editDivision == false) {
            return response()->json(['error' => true, 'status' => "No Change Occour."]);
        } else {
            return response()->json(['error' => true, 'status' => $editDivision]);
        }
    }

    public function inactiveDivision($id = null)
    {
        $inactiveDivision = BasicSetupService::inactiveDivision($id);
        if ($inactiveDivision === true) {
            return response()->json(['success' => true, 'status' => "Inactive Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $inactiveDivision]);
        }
    }

    public function activeDivision($id = null)
    {
        $activeDivision = BasicSetupService::activeDivision($id);
        if ($activeDivision === true) {
            return response()->json(['success' => true, 'status' => "Active Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $activeDivision]);
        }
    }
//=======@@ ENd Division Section @@=======

}
