<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductViewController extends Controller
{
    public function recordView(Request $request)
    {
        $validatedData = $request->validate([
            'type'       => 'required|string|in:sticker,theme,emoji',
            'id'         => 'required|integer',
            'ip_address' => 'nullable|ip', // ตรวจสอบว่าค่า ip_address เป็น IP address ที่ถูกต้อง
        ]);

        recordProductView($validatedData['type'], $validatedData['id'], $validatedData['ip_address'] ?? null);

        return response()->json(['success' => true, 'message' => 'View recorded successfully']);
    }

}
