<?php

namespace App\Http\Controllers\BackEnd;

use Illuminate\Http\Request;

use App\Http\Requests;
use Session;
use Auth;
use DB;
use App\Http\Helper;
use Hash;
use App\Services\BackEnd\DashboardService;

class DashboardController extends Controller
{
    public function dashBoard()
    {
        $getDashboardInformation = DashboardService::getdata();
        return view('backend.index')->with('getDashboardInformation',$getDashboardInformation);
    }

    public function langChange($langType)
    {
        Session::set('last_login_lang', $langType);
        $status = DB::table('admins')
            ->where('id', Session::get('admin.id'))
            ->update([
                'last_login_lang' => $langType
            ]);
        if ($status) {
            return response()->json(['success' => true]);
        }
    }

    public function systemSetting()
    {
        $serrializeData = file_get_contents('config/softwareConfig.txt');
        $jsonEncodeData = unserialize($serrializeData);
        $contact = json_decode($jsonEncodeData);
        return view('backend.Setting.setting', compact('contact'));
    }

    public function saveSystemSetting(Request $request)
    {
        $serrializeData = file_get_contents('config/softwareConfig.txt');
        $jsonEncodeData = unserialize($serrializeData);
        $regData = json_decode($jsonEncodeData);
        $imageName = Helper::imageUpload(1, $request->company_logo, "/images/company/");

        $regData->company_names = $request->company_names;
        $regData->address = $request->address;
        $regData->mobile_no = $request->mobile_no;
        $regData->slogan = $request->slogan;
        $regData->currency = $request->currency;
        $regData->language = $request->language;
        $regData->logo = $imageName;

        $jsonEncodeData = json_encode($regData);
        $serializeData = serialize($jsonEncodeData);
        $fileName = "config/softwareConfig.txt";
        $fileHandeler = fopen($fileName, 'w');
        fwrite($fileHandeler, $serializeData);
        fclose($fileHandeler);

        $status = DB::table('system_config')->first();
        if (isset($status->id)) {
            DB::table('system_config')
                ->where('id', $status->id)
                ->update([
                    'company_name' => $request->company_names,
                    'address' => $request->address,
                    'mobile_no' => $request->mobile_no,
                    'logo' => $imageName,
                    'currency' => $request->currency,
                    'slogun' => $request->slogan,
                    'default_lang' => $request->language,
                ]);
        }
        return redirect()->route('systemSetting')->with('success', 'System Stting Update Succesfull');
    }

    public function profile()
    {
        $getData = DB::table('admins')
            ->where('id', Session::get('admin.id'))
            ->first();
        return view('backend.profile.profile', compact('getData'));
    }

    public function checkAdminPassword(Request $request)
    {
        $data = DB::table('admins')
            ->where('id', Session::get('admin.id'))
            ->first();
        if (Hash::check($request->prev_password, $data->password)) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['error' => true]);
        }
    }

    public function saveAdminPassword(Request $request)
    {
        $status = DB::table('admins')
            ->where('id', Session::get('admin.id'))
            ->update([
                'password' => Hash::make($request->new_password)
            ]);
        if ($status) {
            return redirect()->route('profile')->with('success', 'Password Update Succesfull.');
        } else {
            return redirect()->route('profile')->with('error', 'Password Update Fail...!');
        }
    }

}
