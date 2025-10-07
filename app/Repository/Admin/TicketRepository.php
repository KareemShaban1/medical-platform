<?php

namespace App\Repository\Admin;

use App\Interfaces\Admin\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\Admin;
use Illuminate\Support\Facades\DB;

class TicketRepository implements TicketRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $tickets = Ticket::with(['user', 'latestReply'])->latest();

        return datatables()->of($tickets)
            ->addColumn('ticket_number', fn($item) => $item->ticket_number)
            ->addColumn('user_name', fn($item) => $item->user->name ?? 'N/A')
            ->addColumn('type', fn($item) => $item->type_badge)
            ->addColumn('status', fn($item) => $item->status_badge)
            ->addColumn('created_at', fn($item) => $item->created_at->format('Y-m-d H:i'))
            ->addColumn('last_reply', fn($item) => $this->getLastReplyInfo($item))
            ->addColumn('action', fn($item) => $this->ticketActions($item))
            ->rawColumns(['type', 'status', 'last_reply', 'action'])
            ->make(true);
    }

    public function show($id)
    {
        return Ticket::with(['user', 'replies.repliedBy'])->findOrFail($id);
    }

    public function updateStatus($id, $status)
    {
        return DB::transaction(function () use ($id, $status) {
            $ticket = Ticket::findOrFail($id);
            $ticket->update([
                'status' => $status,
                'closed_at' => in_array($status, ['closed', 'accepted', 'rejected']) ? now() : null
            ]);

            return $ticket;
        });
    }

    public function reply($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $ticket = Ticket::findOrFail($id);

            $reply = TicketReply::create([
                'ticket_id' => $ticket->id,
                'replied_by_type' => Admin::class,
                'replied_by_id' => auth('admin')->id(),
                'message' => $request['message'],
                'is_admin_reply' => true,
            ]);

            // Update ticket status to in_progress if it's pending
            if ($ticket->status === Ticket::STATUS_PENDING) {
                $ticket->update(['status' => Ticket::STATUS_IN_PROGRESS]);
            }

            return $reply;
        });
    }

    public function trash()
    {
        return [];
    }

    public function trashData()
    {
        $tickets = Ticket::onlyTrashed()->with(['user', 'latestReply'])->latest();

        return datatables()->of($tickets)
            ->addColumn('ticket_number', fn($item) => $item->ticket_number)
            ->addColumn('user_name', fn($item) => $item->user->name ?? 'N/A')
            ->addColumn('type', fn($item) => $item->type_badge)
            ->addColumn('status', fn($item) => '<span class="badge bg-secondary">Deleted</span>')
            ->addColumn('deleted_at', fn($item) => $item->deleted_at->format('Y-m-d H:i:s'))
            ->addColumn('action', fn($item) => $this->trashActions($item))
            ->rawColumns(['type', 'status', 'action'])
            ->make(true);
    }

    public function restore($id)
    {
        $ticket = Ticket::onlyTrashed()->findOrFail($id);
        $ticket->restore();
        return $ticket;
    }

    public function forceDelete($id)
    {
        return DB::transaction(function () use ($id) {
            $ticket = Ticket::onlyTrashed()->findOrFail($id);
            $ticket->replies()->delete();
            $ticket->clearMediaCollection('ticket_attachments');
            return $ticket->forceDelete();
        });
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function getLastReplyInfo($item): string
    {
        if ($item->latestReply) {
            $authorType = $item->latestReply->is_admin_reply ? 'Admin' : 'User';
            $time = $item->latestReply->created_at->diffForHumans();
            return '<small class="text-muted">Last reply by ' . $authorType . '<br>' . $time . '</small>';
        }
        return '<small class="text-muted">No replies yet</small>';
    }

    private function ticketActions($item): string
    {
        $showUrl = route('admin.tickets.show', $item->id);
        return <<<HTML
        <div class="d-flex gap-2">
            <a href="{$showUrl}" class="btn btn-sm btn-primary" title="View"><i class="fa fa-eye"></i></a>
            <button onclick="updateTicketStatus({$item->id}, '{$item->status}')" class="btn btn-sm btn-info" title="Update Status"><i class="fa fa-edit"></i></button>
            <button onclick="deleteTicket({$item->id})" class="btn btn-sm btn-danger" title="Delete"><i class="fa fa-trash"></i></button>
        </div>
        HTML;
    }

    private function trashActions($item): string
    {
        return <<<HTML
        <button class="btn btn-sm btn-success" onclick="restoreTicket({$item->id})">
            <i class="mdi mdi-restore"></i> Restore
        </button>
        <button class="btn btn-sm btn-danger" onclick="forceDeleteTicket({$item->id})">
            <i class="mdi mdi-delete-forever"></i> Delete
        </button>
        HTML;
    }
}
