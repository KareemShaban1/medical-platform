<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class ContactMessageController extends Controller
{
    /**
     * Display a listing of contact messages
     */
    public function index()
    {
        // apply permissions
        abort_if(!hasPermission('view contact messages'), 403, __('You are not authorized to view contact messages'));
        $stats = [
            'total' => ContactMessage::count(),
            'new' => ContactMessage::where('status', 'new')->count(),
            'read' => ContactMessage::where('status', 'read')->count(),
            'replied' => ContactMessage::where('status', 'replied')->count(),
            'archived' => ContactMessage::where('status', 'archived')->count(),
        ];

        return view('backend.dashboards.admin.pages.contact-messages.index', compact('stats'));
    }

    /**
     * Get data for DataTables
     */
    public function data(Request $request)
    {
        // apply permissions
        abort_if(!hasPermission('view contact messages'), 403, __('You are not authorized to view contact messages'));
        $query = ContactMessage::query()->orderBy('created_at', 'desc');

        // Apply status filter
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        // Apply date range filter
        if ($request->has('date_from') && $request->date_from != '') {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->has('date_to') && $request->date_to != '') {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        return DataTables::of($query)
            ->addColumn('full_name', function ($message) {
                return $message->full_name;
            })
            ->addColumn('status_badge', function ($message) {
                $badges = [
                    'new' => '<span class="badge bg-primary">New</span>',
                    'read' => '<span class="badge bg-info">Read</span>',
                    'replied' => '<span class="badge bg-success">Replied</span>',
                    'archived' => '<span class="badge bg-secondary">Archived</span>',
                ];
                return $badges[$message->status] ?? '<span class="badge bg-secondary">Unknown</span>';
            })
            ->addColumn('short_message', function ($message) {
                return Str::limit($message->message, 50);
            })
            ->addColumn('action', function ($message) {
                $actions = '<div class="btn-group">';

                // View button
                if (hasPermission('view contact messages')) {
                    $viewUrl = route('admin.contact-messages.show', $message->id);
                    $actions .= '<a href="' . $viewUrl . '" class="btn btn-sm btn-info" title="View">
                        <i class="fa fa-eye"></i>
                    </a>';
                }

                // Delete button
                if (hasPermission('delete contact message')) {
                    $deleteUrl = route('admin.contact-messages.destroy', $message->id);
                    $actions .= '<button type="button" class="btn btn-sm btn-danger delete-btn" data-id="' . $message->id . '" data-url="' . $deleteUrl . '" title="Delete">
                        <i class="fa fa-trash"></i>
                    </button>';
                }

                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['status_badge', 'action'])
            ->make(true);
    }

    /**
     * Display the specified contact message
     */
    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view contact messages'), 403, __('You are not authorized to view contact message'));
        $message = ContactMessage::findOrFail($id);

        // Mark as read if it's new
        if ($message->status === 'new') {
            $message->markAsRead();
        }

        return view('backend.dashboards.admin.pages.contact-messages.show', compact('message'));
    }

    /**
     * Update the message status
     */
    public function updateStatus(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update contact message'), 403, __('You are not authorized to update contact message'));
        $request->validate([
            'status' => 'required|in:new,read,replied,archived',
        ]);

        $message = ContactMessage::findOrFail($id);
        $message->update([
            'status' => $request->status,
            'read_at' => $request->status !== 'new' ? ($message->read_at ?? now()) : null,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Status updated successfully'),
        ]);
    }

    /**
     * Add admin notes
     */
    public function addNotes(Request $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update contact message'), 403, __('You are not authorized to update contact message'));
        $request->validate([
            'admin_notes' => 'required|string|max:1000',
        ]);

        $message = ContactMessage::findOrFail($id);
        $message->update([
            'admin_notes' => $request->admin_notes,
        ]);

        return response()->json([
            'success' => true,
            'message' => __('Notes saved successfully'),
        ]);
    }

    /**
     * Remove the specified contact message
     */
    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete contact message'), 403, __('You are not authorized to delete contact message'));
        try {
            $message = ContactMessage::findOrFail($id);
            $message->delete();

            return response()->json([
                'success' => true,
                'message' => __('Message deleted successfully'),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('Error deleting message'),
            ], 500);
        }
    }
}
