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
use App\Models\Governorate;
use App\Models\City;
use App\Models\Area;

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

    public function getGovernorates()
    {
        $governorates = Governorate::orderBy('name')->get();
        return response()->json($governorates);
    }

    public function getCities(Request $request)
    {
        $request->validate([
            'governorate_id' => 'required|exists:governorates,id'
        ]);

        $cities = City::where('governorate_id', $request->governorate_id)
            ->orderBy('name')
            ->get();

        return response()->json($cities);
    }

    public function getAreas(Request $request)
    {
        $request->validate([
            'city_id' => 'required|exists:cities,id'
        ]);

        $areas = Area::where('city_id', $request->city_id)
            ->orderBy('name')
            ->get();

        return response()->json($areas);
    }
}