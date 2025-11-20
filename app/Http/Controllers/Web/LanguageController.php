<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class LanguageController extends Controller
{
    public function switch(Request $request)
    {
        $locale = $request->input('locale');

        if (!in_array($locale, ['en', 'kh'])) {
            return back()->withErrors(['locale' => 'Invalid language']);
        }

        Session::put('locale', $locale);
        App::setLocale($locale);

        return back();
    }


    /**
     * Fetch translations for the current locale.
     */
    public function translations(Request $request)
    {
        $locale = Session::get('locale', config('app.locale'));
        App::setLocale($locale);

        $path = resource_path("lang/{$locale}.json");

        if (!File::exists($path)) {
            return response()->json(["error" => "Language file not found"], 404);
        }

        return response()->json(json_decode(File::get($path), true));
    }
}
