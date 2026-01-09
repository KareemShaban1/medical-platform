<?php

namespace App\Repository\User;

use App\Interfaces\User\TicketRepositoryInterface;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\TicketType;
use Illuminate\Support\Facades\DB;

class TicketRepository implements TicketRepositoryInterface
{
    public function index()
    {
        return [];
    }

    public function data()
    {
        $tickets = Ticket::with(['ticketType', 'latestReply'])->mine()->latest();

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
            $user = $this->getCurrentUser();

            if (!$user) {
                throw new \Exception('No authenticated user found');
            }

            $data = $request;

            $ticket = Ticket::create([
                'ticketable_type' => get_class($user),
                'ticketable_id' => $user->id,
                'ticket_type_id' => $data['type'],
                'details' => $data['details'],
                'status' => Ticket::STATUS_PENDING,
            ]);

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
        return Ticket::with(['ticketType', 'replies.repliedBy'])->mine()->findOrFail($id);
    }

    public function reply($id, $request)
    {
        return DB::transaction(function () use ($id, $request) {
            $ticket = Ticket::mine()->findOrFail($id);

            // Check if ticket is open for replies
            if ($ticket->isClosed()) {
                throw new \Exception('Cannot reply to a closed ticket');
            }

            $user = $this->getCurrentUser();

            $reply = TicketReply::create([
                'ticket_id' => $ticket->id,
                'replied_by_type' => get_class($user),
                'replied_by_id' => $user->id,
                'message' => $request['message'],
                'is_admin_reply' => false,
            ]);

            return $reply;
        });
    }

    /**
     * Get available ticket types for the current user.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getAvailableTypes()
    {
        $user = $this->getCurrentUser();

        if (!$user) {
            return collect();
        }

        $userType = $user->getTicketUserType();

        return TicketType::active()->forUserType($userType)->get();
    }

    /** ---------------------- PRIVATE HELPERS ---------------------- */

    /**
     * Get the currently authenticated user from any guard.
     *
     * @return mixed|null
     */
    private function getCurrentUser()
    {
        $guards = ['patient', 'clinic', 'supplier', 'affiliate'];

        foreach ($guards as $guard) {
            if (auth($guard)->check()) {
                return auth($guard)->user();
            }
        }

        return null;
    }

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
