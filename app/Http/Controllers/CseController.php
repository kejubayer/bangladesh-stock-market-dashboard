<?php

namespace App\Http\Controllers;

use App\Services\CseService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class CseController extends Controller
{
    protected CseService $cse;

    public function __construct(CseService $cse)
    {
        $this->cse = $cse;
    }

    /**
     * Show CSE page (Blade view)
     */
    public function index()
    {
        return view('cse.index');
    }

    
    public function fetch()
    {
        $stocks = $this->cse->getCseLatest();
        return response()->json($stocks);
    }
}
