<?php

namespace App\Repository\User;

use App\Interfaces\User\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class TicketRepository implements TicketRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $tickets = Ticket::with(['latestReply'])->mine()->latest();

        return datatables()->of($tickets)
            ->addColumn('ticket_number', fn($item) => $item->ticket_number)
            ->addColumn('type', fn($item) => $item->type_badge)
            ->addColumn('status', fn($item) => $item->status_badge)
            ->addColumn('created_at', fn($item) => $item->created_at->format('Y-m-d H:i'))
            ->addColumn('last_reply', fn($item) => $this->getLastReplyInfo($item))
            ->addColumn('action', fn($item) => $this->ticketActions($item))
            ->rawColumns(['type', 'status', 'last_reply', 'action'])
            ->make(true);
    }

    public function store($request)
    {
        return DB::transaction(function () use ($request) {
            $data = $request;
            $data['user_id'] = auth('patient')->id();

            $ticket = Ticket::create($data);

            if (!empty($data['attachments'])) {
                foreach ($data['attachments'] as $attachment) {
                    $ticket->addMedia($attachment)->toMediaCollection('ticket_attachments');
                }
            }

            return $ticket;
        });
    }

    public function show($id)
    {
        return Ticket::with(['replies.repliedBy'])->mine()->findOrFail($id);
    }

    public function reply($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $ticket = Ticket::mine()->findOrFail($id);

            // Check if ticket is open for replies
            if ($ticket->isClosed()) {
                throw new \Exception('Cannot reply to a closed ticket');
            }

            $reply = TicketReply::create([
                'ticket_id' => $ticket->id,
                'replied_by_type' => User::class,
                'replied_by_id' => auth('patient')->id(),
                'message' => $request['message'],
                'is_admin_reply' => false,
            ]);

            return $reply;
        });
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    private function getLastReplyInfo($item): string
    {
        if ($item->latestReply) {
            $authorType = $item->latestReply->is_admin_reply ? 'Admin' : 'You';
            $time = $item->latestReply->created_at->diffForHumans();
            return '<small class="text-muted">Last reply by ' . $authorType . '<br>' . $time . '</small>';
        }
        return '<small class="text-muted">No replies yet</small>';
    }

    private function ticketActions($item): string
    {
        $showUrl = route('user.tickets.show', $item->id);

        return <<<HTML
        <div class="d-flex gap-2">
            <a href="{$showUrl}" class="btn btn-sm btn-primary" title="View"><i class="fa fa-eye"></i></a>
        </div>
        HTML;
    }
}
