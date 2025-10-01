<?php

namespace App\Http\Controllers\Frontend\Patient;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $patient = auth('patient')->user();
        return view('frontend.patient.dashboard', compact('patient'));
    }
}
