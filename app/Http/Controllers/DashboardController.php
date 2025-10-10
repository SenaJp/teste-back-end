<?php

namespace App\Http\Controllers;

use App\Services\DashboardMetrics;

class DashboardController extends Controller
{
    public function __construct(private DashboardMetrics $metrics)
    {
    }

    public function index()
    {
        $data = $this->metrics->get();
        return view('dashboard', $data);
    }
}
