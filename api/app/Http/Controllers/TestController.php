<?php

namespace App\Http\Controllers;

use App\Helpers\HttpExpansion;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{
    public function test(Request $request)
    {
        $data = DB::table('orders')->get();

        return response()->json(['message' => 'Hello World', 'data' => $data]);
    }
}
