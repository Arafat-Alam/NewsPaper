<?php

namespace App\Http\Controllers\BackEnd;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Requests\PhotoGalleryRequest;
use App\Services\BackEnd\PhotoGalleryService;

class PhotoGalleryController extends Controller
{
    public function photoGallery()
    {
        $photoGallery = PhotoGalleryService::getPhotoGallery();
        return view('backend.photoGallery.photoGallery', compact('photoGallery'));
    }

    public function photoGallerySave(PhotoGalleryRequest $request)
    {
        $savePhotoGallery = PhotoGalleryService::savePhotoGallery($request->all());
        if ($savePhotoGallery === true) {
            return redirect()->route('adds')->with('success', 'Save Category Successfull.');
        } elseif ($savePhotoGallery == 'tooLarge') {
            return redirect()->route('adds')->with('error', 'Image Too Large. Your Image Size Must Less Than 1000KB');
        } else {
            return redirect()->route('adds')->with('error', $savePhotoGallery);
        }
    }

    public function photoGalleryEditModal($photoGalleryId)
    {
        $PhotoGallery = PhotoGalleryService::getPhotoGalleryById($photoGalleryId);
        return view('backend.photoGallery.photoGalleryEditModal', compact('PhotoGallery'));
    }

    public function saveEditPhotoGallery(Request $request)
    {
         $saveEditPhotoGallery = PhotoGalleryService::saveEditPhotoGallery($request->all());
        if ($saveEditPhotoGallery === true) {
            return redirect()->route('adds')->with('success', 'Save Category Successfull.');
        } elseif ($saveEditPhotoGallery == 'tooLarge') {
            return redirect()->route('adds')->with('error', 'Image Too Large. Your Image Size Must Less Than 1000KB');
        } else {
            return redirect()->route('adds')->with('error', $saveEditPhotoGallery);
        }
    }

    public function inactivePhotoGallery($id = null)
    {
        $inactivePhotoGallery = PhotoGalleryService::inactivePhotoGallery($id);
        if ($inactivePhotoGallery === true) {
            return response()->json(['success' => true, 'status' => "Category Inactive Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $inactivePhotoGallery]);
        }
    }

    public function activePhotoGallery($id = null)
    {
        $activePhotoGallery = PhotoGalleryService::activePhotoGallery($id);
        if ($activePhotoGallery === true) {
            return response()->json(['success' => true, 'status' => "Category Active Successfull."]);
        } else {
            return response()->json(['error' => true, 'status' => $activePhotoGallery]);
        }
    }
}
