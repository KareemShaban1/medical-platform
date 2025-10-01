<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Models\Job;
use App\Models\Supplier;
use App\Models\RentalSpace;
use App\Models\Product;
use App\Models\Clinic;

class HomeController extends Controller
{
    public function index()
    {
        $jobs = Job::approved()->active()->get();
        $suppliers = Supplier::approved()->active()->get();
        $rentalSpaces = RentalSpace::approved()->active()->get();
        $courses = Course::active()->get();
		$products = Product::approved()->active()->get();
        $clinics = Clinic::approved()->active()->get();
		return view('frontend.pages.home.index', compact('jobs', 'suppliers', 'rentalSpaces', 'courses', 'products','clinics'));
    }
}

