<?php

namespace App\Http\Controllers;

use App\Services\DseService;
use Illuminate\Http\Request;

class DseController extends Controller
{
    protected $dse;

    public function __construct(DseService $dse)
    {
        $this->dse = $dse;
    }

    public function index()
    {
        return view('dse.index');
    }

    public function fetch()
    {
        $stocks = $this->dse->getLatest();
        return response()->json($stocks);
    }
}
