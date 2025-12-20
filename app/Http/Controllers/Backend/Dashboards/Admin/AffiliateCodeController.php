<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCode;
use App\Models\AffiliateUser;
use App\Models\ClinicUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AffiliateCodeController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!hasPermission('view affiliates'), 403, __('You are not authorized to view affiliates'));

        $query = AffiliateCode::with(['affiliateable' => function ($morphTo) {
            $morphTo->morphWith([
                \App\Models\ClinicUser::class => [],
                \App\Models\AffiliateUser::class => [],
            ]);
            if (method_exists($morphTo, 'morphWithTrashed')) {
                $morphTo->morphWithTrashed([
                    \App\Models\ClinicUser::class => [],
                ]);
            }
        }]);

        if ($request->filled('type') && $request->type !== 'all') {
            $type = $request->type;
            $map = [
                'clinic' => 'App\\Models\\ClinicUser',
                'affiliate' => 'App\\Models\\AffiliateUser',
            ];
            if (isset($map[$type])) {
                $query->where('affiliateable_type', $map[$type]);
            }
        }

        $codes = $query->latest()->paginate(20);

        $clinicIds = $codes->getCollection()
            ->where('affiliateable_type', ClinicUser::class)
            ->pluck('affiliateable_id')
            ->unique()
            ->values();
        $affiliateIds = $codes->getCollection()
            ->where('affiliateable_type', AffiliateUser::class)
            ->pluck('affiliateable_id')
            ->unique()
            ->values();

        $clinicUsersById = ClinicUser::withTrashed()
            ->whereIn('id', $clinicIds)
            ->get()
            ->keyBy('id');
        $affiliateUsersById = AffiliateUser::whereIn('id', $affiliateIds)
            ->get()
            ->keyBy('id');
        $clinicUsersByCodePrefix = ClinicUser::withTrashed()
            ->get()
            ->mapWithKeys(function ($user) {
                $prefix = strtoupper(Str::slug($user->name ?? '', ''));
                if ($prefix === '') {
                    return [];
                }
                $prefix = substr($prefix, 0, 8);
                return [$prefix => $user];
            });

        return view('backend.dashboards.admin.pages.affiliates.index', compact(
            'codes',
            'clinicUsersById',
            'affiliateUsersById',
            'clinicUsersByCodePrefix'
        ));
    }

    public function update(Request $request, $id)
    {
        abort_if(!hasPermission('update affiliates'), 403, __('You are not authorized to update affiliates'));

        $validated = $request->validate([
            'discount_percent' => 'nullable|numeric|min:0|max:100',
            'commission_percent' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $code = AffiliateCode::findOrFail($id);
        $code->update([
            'discount_percent' => $validated['discount_percent'] ?? null,
            'commission_percent' => $validated['commission_percent'] ?? null,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->back()->with('success', __('Affiliate code updated.'));
    }
}
