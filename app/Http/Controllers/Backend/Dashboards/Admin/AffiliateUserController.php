<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\AffiliateCode;
use App\Models\AffiliatePayoutProfile;
use App\Models\AffiliatePayoutRequest;
use App\Models\AffiliateTransaction;
use App\Models\AffiliateUser;
use App\Models\ClinicUser;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AffiliateUserController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!hasPermission('view affiliate users'), 403, __('You are not authorized to view affiliate users'));

        $codesQuery = AffiliateCode::query()
            ->whereIn('affiliateable_type', [
                AffiliateUser::class,
                ClinicUser::class,
            ])
            ->with([
                'affiliateable' => function ($morphTo) {
                    $morphTo->morphWith([
                        AffiliateUser::class => [],
                        ClinicUser::class => [],
                    ]);
                    if (method_exists($morphTo, 'morphWithTrashed')) {
                        $morphTo->morphWithTrashed([
                            ClinicUser::class => [],
                        ]);
                    }
                }
            ])
            ->latest();

        if ($request->filled('type') && $request->type !== 'all') {
            $map = [
                'clinic' => ClinicUser::class,
                'affiliate' => AffiliateUser::class,
            ];
            if (isset($map[$request->type])) {
                $codesQuery->where('affiliateable_type', $map[$request->type]);
            }
        }

        $codes = $codesQuery->paginate(perPage: 20)->appends($request->query());

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

        return view('backend.dashboards.admin.pages.affiliates.users.index', compact(
            'codes',
            'clinicUsersById',
            'affiliateUsersById',
            'clinicUsersByCodePrefix'
        ));
    }

    public function data()
    {
        abort_if(!hasPermission('view affiliate users'), 403, __('You are not authorized to view affiliate users'));

        $users = AffiliateUser::query();

        return datatables()->of($users)
            ->addColumn('action', function ($user) {
                $actions = '<div class="d-flex gap-2">';

                if ($user->affiliateCode) {
                    $showUrl = route('admin.affiliates.users.show', $user->affiliateCode->id);
                    $actions .= '<a href="' . $showUrl . '" class="btn btn-sm btn-info" title="' . __('View') . '"><i class="fa fa-eye"></i></a>';
                }

                if (hasPermission('delete affiliate user')) {
                    $actions .= '<button onclick="deleteUser(' . $user->id . ')" class="btn btn-sm btn-danger" title="' . __('Delete') . '"><i class="fa fa-trash"></i></button>';
                }

                $actions .= '</div>';
                return $actions;
            })
            ->addColumn('status_badge', function ($user) {
                $statusClass = $user->status === 'active' ? 'bg-success' : 'bg-danger';
                return '<span class="badge ' . $statusClass . '">' . ucfirst($user->status ?? 'inactive') . '</span>';
            })
            ->rawColumns(['action', 'status_badge'])
            ->make(true);
    }

    public function show(AffiliateCode $affiliateCode)
    {
        abort_if(!hasPermission('view affiliate users'), 403, __('You are not authorized to view affiliate users'));

        $affiliateCode->load('affiliateable');
        $owner = $affiliateCode->affiliateable;
        if (!$owner && $affiliateCode->affiliateable_type === ClinicUser::class) {
            $owner = ClinicUser::withTrashed()->find($affiliateCode->affiliateable_id);
        }
        if (!$owner && $affiliateCode->affiliateable_type === AffiliateUser::class) {
            $owner = AffiliateUser::find($affiliateCode->affiliateable_id);
        }
        if (!$owner && $affiliateCode->affiliateable_type === ClinicUser::class) {
            $prefix = strtoupper(Str::before($affiliateCode->code, '-'));
            if ($prefix !== '') {
                $owner = ClinicUser::withTrashed()
                    ->get()
                    ->first(function ($user) use ($prefix) {
                        $base = strtoupper(Str::slug($user->name ?? '', ''));
                        return $base !== '' && str_starts_with($base, $prefix);
                    });
            }
        }

        $transactions = AffiliateTransaction::where('affiliate_code_id', $affiliateCode->id)
            ->latest()
            ->paginate(20, ['*'], 'transactions_page');

        $payoutRequests = AffiliatePayoutRequest::where('affiliate_code_id', $affiliateCode->id)
            ->latest()
            ->paginate(20, ['*'], 'payouts_page');

        $payoutProfile = AffiliatePayoutProfile::where('affiliate_code_id', $affiliateCode->id)->first();

        return view('backend.dashboards.admin.pages.affiliates.users.show', compact(
            'affiliateCode',
            'owner',
            'transactions',
            'payoutRequests',
            'payoutProfile'
        ));
    }

    public function trash()
    {
        abort_if(!hasPermission('view trash affiliate users'), 403, __('You are not authorized to view trash'));

        return view('backend.dashboards.admin.pages.affiliates.users.trash');
    }

    public function trashData()
    {
        abort_if(!hasPermission('view trash affiliate users'), 403, __('You are not authorized to view trash'));

        $users = AffiliateUser::onlyTrashed();

        return datatables()->of($users)
            ->addColumn('deleted_at_formatted', function ($user) {
                return $user->deleted_at->format('Y-m-d H:i:s');
            })
            ->addColumn('action', function ($user) {
                $actions = '<div class="d-flex gap-2">';

                if (hasPermission('restore affiliate user')) {
                    $actions .= '<button onclick="restoreUser(' . $user->id . ')" class="btn btn-sm btn-info" title="' . __('Restore') . '"><i class="fa fa-undo"></i></button>';
                }

                if (hasPermission('force delete affiliate user')) {
                    $actions .= '<button onclick="forceDeleteUser(' . $user->id . ')" class="btn btn-sm btn-danger" title="' . __('Permanently Delete') . '"><i class="fa fa-trash"></i></button>';
                }

                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function destroy($id)
    {
        abort_if(!hasPermission('delete affiliate user'), 403, __('You are not authorized to delete'));

        $user = AffiliateUser::findOrFail($id);
        $user->delete();

        return response()->json([
            'status' => 'success',
            'message' => __('Affiliate user deleted successfully')
        ]);
    }

    public function restore($id)
    {
        abort_if(!hasPermission('restore affiliate user'), 403, __('You are not authorized to restore'));

        $user = AffiliateUser::onlyTrashed()->findOrFail($id);
        $user->restore();

        return response()->json([
            'status' => 'success',
            'message' => __('Affiliate user restored successfully')
        ]);
    }

    public function forceDelete($id)
    {
        abort_if(!hasPermission('force delete affiliate user'), 403, __('You are not authorized to permanently delete'));

        $user = AffiliateUser::onlyTrashed()->findOrFail($id);
        $user->forceDelete();

        return response()->json([
            'status' => 'success',
            'message' => __('Affiliate user permanently deleted')
        ]);
    }
}

