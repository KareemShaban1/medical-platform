<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
	public function index(Request $request)
	{
		// Get initial suppliers with pagination
		$suppliers = Supplier::approved()
			->where('status', true)
			->with(['approvement'])
			->paginate(12);

		// Get filter options
		$categories = [
			'equipment' => 'Medical Equipment',
			'supplies' => 'Medical Supplies',
			'pharmaceuticals' => 'Pharmaceuticals',
			'diagnostic' => 'Diagnostic Tools',
			'surgical' => 'Surgical Instruments',
			'disposables' => 'Disposable Items',
			'laboratory' => 'Laboratory Equipment',
			'rehabilitation' => 'Rehabilitation Equipment'
		];

		$locations = [
			'local' => 'Local Suppliers',
			'national' => 'National',
			'international' => 'International',
			'regional' => 'Regional'
		];

		return view('frontend.pages.suppliers.index', compact(
			'suppliers',
			'categories',
			'locations'
		));
	}

	public function filter(Request $request)
	{
		$query = Supplier::approved()
			->where('status', true)
			->with(['approvement']);

		// Search filter
		if ($request->filled('search')) {
			$searchTerm = $request->search;
			$query->where(function ($q) use ($searchTerm) {
				$q->where('name', 'like', "%{$searchTerm}%")
					->orWhere('address', 'like', "%{$searchTerm}%");
			});
		}

		// Category filter
		if ($request->filled('category')) {
			$query->where('category', $request->category);
		}

		// Location filter
		if ($request->filled('location')) {
			$query->where('location', $request->location);
		}

		// Rating filter
		if ($request->filled('rating')) {
			$minRating = (float) $request->rating;
			$query->where('rating', '>=', $minRating);
		}

		// Certification filters
		// if ($request->filled('certified')) {
		// 	$query->where('is_certified', true);
		// }

		// if ($request->filled('iso')) {
		// 	$query->where('iso_certified', true);
		// }

		// if ($request->filled('fda')) {
		// 	$query->where('fda_approved', true);
		// }

		// Experience filter
		if ($request->filled('experience')) {
			$minExperience = (int) $request->experience;
			$query->where('years_experience', '>=', $minExperience);
		}

		// Sort options
		switch ($request->get('sort', 'name')) {
			case 'name':
				$query->orderBy('name', 'asc');
				break;
			case 'rating':
				$query->orderBy('rating', 'desc');
				break;
			case 'experience':
				$query->orderBy('years_experience', 'desc');
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

		$suppliers = $query->paginate(12);

		if ($request->ajax()) {
			return response()->json([
				'success' => true,
				'html' => view('frontend.pages.suppliers.partials.suppliers-grid', ['suppliers' => $suppliers])->render(),
				'pagination' => view('frontend.pages.suppliers.partials.pagination', ['suppliers' => $suppliers])->render(),
				'count' => $suppliers->total(),
				'applied_filters' => $this->getAppliedFilters($request)
			]);
		}

		return view('frontend.pages.suppliers.index', compact('suppliers'));
	}

	private function getAppliedFilters(Request $request)
	{
		$filters = [];

		if ($request->filled('search')) {
			$filters[] = [
				'label' => 'Search',
				'value' => $request->search,
				'type' => 'search'
			];
		}

		if ($request->filled('category')) {
			$categories = [
				'equipment' => 'Medical Equipment',
				'supplies' => 'Medical Supplies',
				'pharmaceuticals' => 'Pharmaceuticals',
				'diagnostic' => 'Diagnostic Tools',
				'surgical' => 'Surgical Instruments',
				'disposables' => 'Disposable Items',
				'laboratory' => 'Laboratory Equipment',
				'rehabilitation' => 'Rehabilitation Equipment'
			];
			$filters[] = [
				'label' => 'Category',
				'value' => $categories[$request->category] ?? $request->category,
				'type' => 'category'
			];
		}

		if ($request->filled('location')) {
			$locations = [
				'local' => 'Local Suppliers',
				'national' => 'National',
				'international' => 'International',
				'regional' => 'Regional'
			];
			$filters[] = [
				'label' => 'Location',
				'value' => $locations[$request->location] ?? $request->location,
				'type' => 'location'
			];
		}

		if ($request->filled('rating')) {
			$filters[] = [
				'label' => 'Minimum Rating',
				'value' => $request->rating . '+ Stars',
				'type' => 'rating'
			];
		}

		if ($request->filled('experience')) {
			$filters[] = [
				'label' => 'Minimum Experience',
				'value' => $request->experience . '+ Years',
				'type' => 'experience'
			];
		}

		if ($request->filled('certified')) {
			$filters[] = [
				'label' => 'Certification',
				'value' => 'Certified Suppliers',
				'type' => 'certified'
			];
		}

		if ($request->filled('iso')) {
			$filters[] = [
				'label' => 'Certification',
				'value' => 'ISO Certified',
				'type' => 'iso'
			];
		}

		if ($request->filled('fda')) {
			$filters[] = [
				'label' => 'Certification',
				'value' => 'FDA Approved',
				'type' => 'fda'
			];
		}

		return $filters;
	}

	/**
	 * Show supplier details
	 */
	public function show($id)
	{
		$supplier = Supplier::approved()
			->where('status', true)
			->with(['approvement'])
			->findOrFail($id);

		// Get related suppliers with same category
		$relatedSuppliers = Supplier::approved()
			->where('status', true)
			->where('id', '!=', $id)
			// ->where('category', $supplier->category)
			->limit(4)
			->get();

		// Get suppliers with similar specialties
		$similarSuppliers = Supplier::approved()
			->where('status', true)
			->where('id', '!=', $id)
			// ->where('location', $supplier->location)
			->limit(4)
			->get();

		return view('frontend.pages.suppliers.show', compact('supplier', 'relatedSuppliers', 'similarSuppliers'));
	}
}
