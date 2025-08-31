<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\App;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class LanguageController extends Controller
{
    public function index()
    {
        $languages = get_all_languages();
        return view('admin.languages.index', ['languages' => $languages]);
    }

	// Language Functions
	public function postAdd(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'language' => 'required|string|max:255',
        ],[
            'language.required' => 'Currency Name is Required',
            'language.string' => 'Currency Name should be a String',
            'language.max' => 'Currency Name max characters should be 255',
        ]);

        if ($validator->fails())
        {
            return response()->json([
                 'status' => 400,
                 'messages' => $validator->getMessageBag()
            ]);
        }

        $language_name = $request->language;
        saveDefaultJSONFile($language_name);

        return response()->json([
            'status' => 200,
            'messages' => 'Language added Successfully'
        ]);
	}

    public function edit($language)
    {
        return view('admin.languages.index', ['language' => $language]);
    }

	public function update(Request $request)
    {
		$current_editing_language = $request->currentEditingLanguage;
		$updatedValue = $request->updatedValue;
		$key = $request->key;

		saveJSONFile($current_editing_language, $key, $updatedValue);
        return response()->json([
            'status' => 'success',
            'message' => 'Phrase Updated Successfully'
        ]);
	}

    public function changeLanguage($locale)
    {
        App::setLocale($locale);
        Session::put('locale', $locale);
        return redirect()->back();
    }

    public function default()
    {
        $languages = get_all_languages();
        return view('admin.languages.index', ['languages' => $languages]);
    }

    public function postDefault(Request $request)
    {
        $locale = $request->lang;
        $type = 'DEFAULT_LANGUAGE';
        overWriteEnvFile($type, $locale);
        App::setLocale($locale);
        Session::put('locale', $locale);
        return redirect()->back()->with('success', 'Successfully set Default Language');
    }

	// Language Functions
	public function delete(Request $request){

        $lang = $request->id;

        if (file_exists(resource_path('lang/'.$lang.'.json'))) {

            unlink(resource_path('lang/'.$lang.'.json'));

            return response()->json(['status' => 'Language Deleted Successfully']);
        }
	}
}
