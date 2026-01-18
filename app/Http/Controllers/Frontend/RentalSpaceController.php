<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\RentalSpace;
use Illuminate\Http\Request;

class RentalSpaceController extends Controller
{
    public function index(Request $request)
    {
        $query = RentalSpace::approved()
            ->active()
            ->with(['availability', 'pricing', 'clinic', 'schedules']);

        // Apply filters
        $this->applyFilters($query, $request);

        $perPage = (int) $request->get('per_page', 16);
        $perPage = $perPage > 0 ? min($perPage, 100) : 16;

        $rentalSpaces = $query->paginate($perPage)->appends($request->query());

        // Return partial for AJAX requests
        if ($request->ajax()) {
            return view('frontend.pages.rental-spaces.partials.rental-spaces-grid', compact('rentalSpaces'))->render();
        }

        return view('frontend.pages.rental-spaces.index', compact('rentalSpaces'));
    }

    public function show($id)
    {
        $rentalSpace = RentalSpace::approved()
            ->active()
            ->with(['availability', 'pricing', 'pricings', 'clinic.clinicUsers', 'schedules'])
            ->findOrFail($id);

        $relatedSpaces = RentalSpace::approved()
            ->active()
            ->where('id', '!=', $rentalSpace->id)
            ->where('clinic_id', $rentalSpace->clinic_id)
            ->with(['pricing', 'schedules'])
            ->limit(6)
            ->get();

        return view('frontend.pages.rental-spaces.show', compact('rentalSpace', 'relatedSpaces'));
    }

    /**
     * Apply filters to query
     */
    private function applyFilters($query, Request $request)
    {
        // Search in name, description, and location
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Listing type filter (rent/sale)
        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        // Pricing type filter (hourly/daily/weekly/monthly)
        if ($request->filled('pricing_type')) {
            $pricingType = $request->pricing_type;
            $query->whereHas('pricing', function ($q) use ($pricingType) {
                $q->where('pricing_type', $pricingType);
            });
        }

        // Availability type filter
        if ($request->filled('availability')) {
            $availabilityType = $request->availability;
            $query->whereHas('availability', function ($q) use ($availabilityType) {
                $q->where('type', $availabilityType);
            });
        }

        // Available day filter (from schedules)
        if ($request->filled('available_day')) {
            $day = $request->available_day;
            $query->whereHas('schedules', function ($q) use ($day) {
                $q->where('day_of_week', $day)
                    ->where('is_available', true);
            });
        }

        // Price range filter
        if ($request->filled('price')) {
            $priceRange = $request->price;

            // Handle both sale price and rental pricing
            $query->where(function ($q) use ($priceRange) {
                switch ($priceRange) {
                    case '0-500':
                        $q->where(function ($sub) {
                            $sub->where('listing_type', 'sale')
                                ->whereBetween('sale_price', [0, 500]);
                        })->orWhereHas('pricing', function ($p) {
                            $p->whereBetween('price', [0, 500]);
                        });
                        break;
                    case '500-1000':
                        $q->where(function ($sub) {
                            $sub->where('listing_type', 'sale')
                                ->whereBetween('sale_price', [500, 1000]);
                        })->orWhereHas('pricing', function ($p) {
                            $p->whereBetween('price', [500, 1000]);
                        });
                        break;
                    case '1000-5000':
                        $q->where(function ($sub) {
                            $sub->where('listing_type', 'sale')
                                ->whereBetween('sale_price', [1000, 5000]);
                        })->orWhereHas('pricing', function ($p) {
                            $p->whereBetween('price', [1000, 5000]);
                        });
                        break;
                    case '5000+':
                        $q->where(function ($sub) {
                            $sub->where('listing_type', 'sale')
                                ->where('sale_price', '>=', 5000);
                        })->orWhereHas('pricing', function ($p) {
                            $p->where('price', '>=', 5000);
                        });
                        break;
                }
            });
        }

        // Clinic filter
        if ($request->filled('clinic')) {
            $query->where('clinic_id', $request->clinic);
        }

        // Capacity filter
        if ($request->filled('min_capacity')) {
            $query->where('capacity', '>=', (int) $request->min_capacity);
        }

        // Area filter
        if ($request->filled('min_area')) {
            $query->where('area_sqm', '>=', (float) $request->min_area);
        }

        // Sort
        $sort = $request->get('sort', 'newest');
        switch ($sort) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'name-desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'price_low':
                $query->orderByRaw('COALESCE(sale_price, (SELECT price FROM rental_pricings WHERE rental_pricings.rental_space_id = rental_spaces.id LIMIT 1)) ASC');
                break;
            case 'price_high':
                $query->orderByRaw('COALESCE(sale_price, (SELECT price FROM rental_pricings WHERE rental_pricings.rental_space_id = rental_spaces.id LIMIT 1)) DESC');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
        }
    }

    public function filter(Request $request)
    {
        try {
            $query = RentalSpace::approved()
                ->active()
                ->with(['availability', 'pricing', 'clinic', 'schedules']);

            $this->applyFilters($query, $request);

            $perPage = (int) ($request->get('per_page', 12));
            $perPage = $perPage > 0 ? min($perPage, 100) : 12;

            $currentPage = $request->get('page', 1);
            $rentalSpaces = $query->paginate($perPage, ['*'], 'page', $currentPage);

            $rentalSpaces->setPath(route('rental-spaces'));
            $rentalSpaces->appends($request->except('page'));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('frontend.pages.rental-spaces.partials.rental-spaces-grid', compact('rentalSpaces'))->render(),
                    'count' => $rentalSpaces->total(),
                ]);
            }

            return view('frontend.pages.rental-spaces.index', compact('rentalSpaces'));
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error filtering rental spaces: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Error filtering rental spaces.');
        }
    }
}
