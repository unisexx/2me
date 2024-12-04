<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function getPage($id)
    {
        $rs = Page::find($id);
        return response()->json($rs);
    }
}
