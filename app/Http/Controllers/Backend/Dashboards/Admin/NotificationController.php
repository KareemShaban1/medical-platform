<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = auth('admin')->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        return view('backend.dashboards.admin.pages.notifications.index', compact('notifications'));
    }

    public function getLatest()
    {
        $notifications = auth('admin')->user()
            ->unreadNotifications()
            ->latest()
            ->take(10)
            ->get();

        $unreadCount = auth('admin')->user()->unreadNotifications()->count();

        return response()->json([
            'notifications' => $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'action_url' => $notification->data['action_url'] ?? '#',
                    'type' => $notification->data['type'] ?? 'info',
                    'created_at' => $notification->created_at->diffForHumans(),
                    'read_at' => $notification->read_at,
                ];
            }),
            'unread_count' => $unreadCount
        ]);
    }

    public function markAsRead($id)
    {
        $notification = auth('admin')->user()
            ->notifications()
            ->where('id', $id)
            ->first();

        if ($notification) {
            $notification->markAsRead();

            // Return the action URL to redirect to
            return response()->json([
                'status' => 'success',
                'action_url' => $notification->data['action_url'] ?? route('admin.dashboard')
            ]);
        }

        return response()->json(['status' => 'error'], 404);
    }

    public function markAllAsRead()
    {
        auth('admin')->user()->unreadNotifications->markAsRead();

        return response()->json(['status' => 'success']);
    }

    public function data()
    {
        $notifications = auth('admin')->user()
            ->notifications()
            ->latest();

        return datatables()->of($notifications)
            ->addColumn('title', function ($notification) {
                $isRead = $notification->read_at ? '' : 'fw-bold';
                $title = $notification->data['title'] ?? 'Notification';
                return "<span class=\"{$isRead}\">{$title}</span>";
            })
            ->addColumn('message', function ($notification) {
                $isRead = $notification->read_at ? 'text-muted' : '';
                $message = $notification->data['message'] ?? '';
                return "<span class=\"{$isRead}\">" . Str::limit($message, 100) . "</span>";
            })
            ->addColumn('type', function ($notification) {
                $type = $notification->data['type'] ?? 'info';
                $badgeClass = [
                    'profile_submitted' => 'bg-warning',
                    'profile_approved' => 'bg-success',
                    'profile_rejected' => 'bg-danger',
                    'info' => 'bg-info',
                ];
                $class = $badgeClass[$type] ?? 'bg-secondary';
                return "<span class=\"badge {$class}\">" . ucfirst(str_replace('_', ' ', $type)) . "</span>";
            })
            ->addColumn('status', function ($notification) {
                return $notification->read_at
                    ? '<span class="badge bg-success">Read</span>'
                    : '<span class="badge bg-warning">Unread</span>';
            })
            ->addColumn('created_at', function ($notification) {
                return $notification->created_at->format('Y-m-d H:i:s');
            })
            ->addColumn('action', function ($notification) {
                $actions = '<div class="d-flex gap-2">';

                if (!$notification->read_at) {
                    $actions .= '<button onclick="markAsRead(\'' . $notification->id . '\')" class="btn btn-sm btn-info" title="Mark as Read">
                        <i class="fa fa-check"></i>
                    </button>';
                }

                if (isset($notification->data['action_url'])) {
                    $actions .= '<a href="' . $notification->data['action_url'] . '" class="btn btn-sm btn-primary" title="View">
                        <i class="fa fa-eye"></i>
                    </a>';
                }

                $actions .= '</div>';
                return $actions;
            })
            ->rawColumns(['title', 'message', 'type', 'status', 'action'])
            ->make(true);
    }
}
