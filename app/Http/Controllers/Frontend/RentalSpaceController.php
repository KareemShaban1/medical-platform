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
            ->with(['availability', 'pricing', 'clinic']);

        // Search in name, description, and location
        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%");
            });
        }

        // Availability type filter (if provided)
        if ($request->filled('availability')) {
            $availabilityType = $request->availability;
            $query->whereHas('availability', function ($q) use ($availabilityType) {
                $q->where('type', $availabilityType);
            });
        }

        // Price range filter (via pricing relation)
        if ($request->filled('price')) {
            $priceRange = $request->price;
            $query->whereHas('pricing', function ($q) use ($priceRange) {
                switch ($priceRange) {
                    case '0-500':
                        $q->whereBetween('price', [0, 500]);
                        break;
                    case '500-1000':
                        $q->whereBetween('price', [500, 1000]);
                        break;
                    case '1000+':
                        $q->where('price', '>=', 1000);
                        break;
                }
            });
        }

        // Clinic filter
        if ($request->filled('clinic')) {
            $query->where('clinic_id', $request->clinic);
        }

        // Date range filter
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Sort
        $sort = $request->get('sort', 'name');
        switch ($sort) {
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'name-desc':
                $query->orderBy('name', 'desc');
                break;
            case 'newest':
                $query->orderBy('created_at', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('name', 'asc');
        }

        $perPage = (int) $request->get('per_page', 20);
        $perPage = $perPage > 0 ? min($perPage, 100) : 20;

        $rentalSpaces = $query->paginate($perPage)->appends($request->query());

        return view('frontend.pages.rental-spaces.index', compact('rentalSpaces'));
    }

    public function show($id)
    {
        $rentalSpace = RentalSpace::approved()
            ->active()
            ->with(['availability', 'pricing', 'clinic.clinicUsers'])
            ->findOrFail($id);

        $relatedSpaces = RentalSpace::approved()
            ->active()
            ->where('id', '!=', $rentalSpace->id)
            ->where('clinic_id', $rentalSpace->clinic_id)
            ->limit(6)
            ->get();

        return view('frontend.pages.rental-spaces.show', compact('rentalSpace', 'relatedSpaces'));
    }

    public function filter(Request $request)
    {
        try {
            $query = RentalSpace::approved()
                ->active()
                ->with(['availability', 'pricing', 'clinic']);

            if ($request->filled('search')) {
                $search = trim($request->search);
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('location', 'like', "%{$search}%");
                });
            }

            if ($request->filled('availability')) {
                $availabilityType = $request->availability;
                $query->whereHas('availability', function ($q) use ($availabilityType) {
                    $q->where('type', $availabilityType);
                });
            }

            if ($request->filled('price')) {
                $priceRange = $request->price;
                $query->whereHas('pricing', function ($q) use ($priceRange) {
                    switch ($priceRange) {
                        case '0-500':
                            $q->whereBetween('price', [0, 500]);
                            break;
                        case '500-1000':
                            $q->whereBetween('price', [500, 1000]);
                            break;
                        case '1000+':
                            $q->where('price', '>=', 1000);
                            break;
                    }
                });
            }

            $sort = $request->get('sort', 'name');
            switch ($sort) {
                case 'name-desc':
                    $query->orderBy('name', 'desc');
                    break;
                case 'newest':
                    $query->orderBy('created_at', 'desc');
                    break;
                case 'oldest':
                    $query->orderBy('created_at', 'asc');
                    break;
                default:
                    $query->orderBy('name', 'asc');
            }

            $perPage = (int) ($request->get('per_page', 12));
            $perPage = $perPage > 0 ? min($perPage, 100) : 12;

            // Get current page from request
            $currentPage = $request->get('page', 1);

            // Paginate with current page
            $rentalSpaces = $query->paginate($perPage, ['*'], 'page', $currentPage);

            // Set paginator path to rental-spaces index route (for proper URL generation)
            $rentalSpaces->setPath(route('rental-spaces'));

            // Append filter parameters to pagination URLs
            $rentalSpaces->appends($request->except('page'));

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'html' => view('frontend.pages.rental-spaces.partials.rental-spaces-grid', compact('rentalSpaces'))->render(),
                    'pagination' => view('frontend.pages.rental-spaces.partials.pagination', compact('rentalSpaces'))->render(),
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
