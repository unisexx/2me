<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductViewController extends Controller
{
    public function recordView(Request $request)
    {
        // ตรวจสอบข้อมูลที่รับเข้ามา
        $validatedData = $request->validate([
            'type' => 'required|string|in:sticker,theme,emoji', // ประเภทต้องเป็น sticker, theme, หรือ emoji
            'id' => 'required|integer',                         // ID ต้องเป็นตัวเลข
        ]);

        // เรียกใช้ฟังก์ชัน recordProductView
        recordProductView($validatedData['type'], $validatedData['id']);

        return response()->json([
            'success' => true,
            'message' => 'View recorded successfully.',
        ]);
    }
}
