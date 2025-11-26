<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LanguageController extends Controller
{
    public function changeLanguage(Request $request)
    {
        try {
            $locale = $request->input('locale');

            // Kiểm tra nếu locale hợp lệ
            if (!in_array($locale, ['vi', 'en'])) {
                throw new \Exception('Invalid locale provided');
            }

            // Lưu locale vào session
            session(['APP_LOCALE' => $locale]);

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
