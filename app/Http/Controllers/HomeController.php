<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CseService;
use App\Services\DseService;

class HomeController extends Controller
{
    protected CseService $cse;
    protected DseService $dse;

    public function __construct(CseService $cse, DseService $dse)
    {
        $this->cse = $cse;
        $this->dse = $dse;
    }

    public function index()
    {
        $cseStocks = $this->cse->getCseLatest(); // get from CSE service
        $dseStocks = $this->dse->getLatest();    // get from DSE service

        return view('index', compact('cseStocks', 'dseStocks'));
    }
}
