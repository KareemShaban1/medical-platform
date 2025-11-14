<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\CourseEnrollment;
use Illuminate\Http\Request;

class CourseEnrollmentController extends Controller
{
    public function index()
    {
        return view('backend.dashboards.admin.pages.course-enrollments.index');
    }

    public function data(Request $request)
    {
        $enrollments = CourseEnrollment::with(['course', 'clinicUser.clinic']);

        if ($request->filled('status') && $request->status !== 'all') {
            $enrollments->where('status', $request->status);
        }

        return datatables()->of($enrollments)
            ->addColumn('course', fn ($item) => e($item->course?->title ?? ('#'.$item->course_id)))
            ->addColumn('clinic', function ($item) {
                $clinic = $item->clinicUser?->clinic?->name ?? '-';
                $user = $item->clinicUser?->name ?? '';
                $email = $item->clinicUser?->email ?? '';
                return e($clinic) . ' / ' . e($user) . ' (' . e($email) . ')';
            })
            ->addColumn('contact', function ($item) {
                $email = e($item->clinicUser?->email ?? __('Not provided'));
                $phone = e($item->clinicUser?->phone ?? __('Not provided'));
                return <<<HTML
                    <div class="d-flex flex-column gap-1">
                        <span class="badge bg-light text-dark"><i class="fa fa-envelope me-1"></i>{$email}</span>
                        <span class="badge bg-light text-dark"><i class="fa fa-phone me-1"></i>{$phone}</span>
                    </div>
                HTML;
            })
            ->editColumn('status', function ($item) {
                $status = e(ucfirst($item->status));
                $id = $item->id;
                return <<<HTML
                    <select class="form-select form-select-sm" onchange="updateEnrollmentStatus({$id}, this.value)">
                        <option value="pending" {$this->selected($item->status, 'pending')}>Pending</option>
                        <option value="approved" {$this->selected($item->status, 'approved')}>Approved</option>
                        <option value="rejected" {$this->selected($item->status, 'rejected')}>Rejected</option>
                    </select>
                HTML;
            })
            ->addColumn('action', function ($item) {
                $id = $item->id;
                return <<<HTML
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteEnrollment({$id})"><i class="fa fa-trash"></i></button>
                HTML;
            })
            ->editColumn('created_at', fn ($item) => $item->created_at?->format('M d, Y H:i') ?? '-')
            ->rawColumns(['status', 'action', 'contact'])
            ->make(true);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $enrollment = CourseEnrollment::findOrFail($id);
        $enrollment->status = $request->status;
        $enrollment->save();

        return response()->json(['status' => 'success', 'message' => __('Enrollment status updated')]);
    }

    public function destroy($id)
    {
        $enrollment = CourseEnrollment::findOrFail($id);
        $enrollment->delete();
        return response()->json(['status' => 'success', 'message' => __('Enrollment deleted')]);
    }

    private function selected($value, $expected)
    {
        return $value === $expected ? 'selected' : '';
    }
}
