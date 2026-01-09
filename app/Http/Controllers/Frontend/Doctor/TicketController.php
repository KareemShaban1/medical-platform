<?php

namespace App\Http\Controllers\Frontend\Doctor;

use App\Http\Controllers\Controller;
use App\Models\Ticket;
use App\Models\TicketReply;
use App\Models\TicketType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TicketController extends Controller
{
    private function getCurrentUser()
    {
        return auth('clinic')->user();
    }

    public function index()
    {
        $ticketTypes = TicketType::active()->forUserType('clinic_user')->get();
        return view('frontend.doctor.tickets.index', compact('ticketTypes'));
    }

    public function data()
    {
        $user = $this->getCurrentUser();

        $tickets = Ticket::with(['ticketType', 'latestReply'])
            ->where('ticketable_type', get_class($user))
            ->where('ticketable_id', $user->id)
            ->latest();

        return datatables()->of($tickets)
            ->addColumn('ticket_number', fn($item) => $item->ticket_number)
            ->addColumn('type', fn($item) => $item->type_badge)
            ->addColumn('status', fn($item) => $item->status_badge)
            ->addColumn('created_at', fn($item) => $item->created_at->format('Y-m-d H:i'))
            ->addColumn('last_reply', fn($item) => $this->getLastReplyInfo($item))
            ->addColumn('action', fn($item) => '<a href="' . route('doctor.tickets.show', $item->id) . '" class="btn btn-sm btn-primary"><i class="fas fa-eye"></i></a>')
            ->rawColumns(['type', 'status', 'last_reply', 'action'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|exists:ticket_types,id',
            'details' => 'required|string|min:10|max:5000',
            'attachments' => 'nullable|array|max:5',
            'attachments.*' => 'file|mimes:jpeg,png,jpg,gif,pdf,doc,docx|max:2048',
        ]);

        $user = $this->getCurrentUser();

        DB::transaction(function () use ($request, $user) {
            $ticket = Ticket::create([
                'ticketable_type' => get_class($user),
                'ticketable_id' => $user->id,
                'ticket_type_id' => $request->type,
                'details' => $request->details,
                'status' => Ticket::STATUS_PENDING,
            ]);

            if ($request->hasFile('attachments')) {
                foreach ($request->file('attachments') as $attachment) {
                    $ticket->addMedia($attachment)->toMediaCollection('ticket_attachments');
                }
            }
        });

        return request()->ajax()
            ? response()->json(['status' => 'success', 'message' => __('Ticket submitted successfully')])
            : redirect()->back()->with('success', __('Ticket submitted successfully'));
    }

    public function show($id)
    {
        $user = $this->getCurrentUser();
        $ticket = Ticket::with(['ticketType', 'replies.repliedBy'])
            ->where('ticketable_type', get_class($user))
            ->where('ticketable_id', $user->id)
            ->findOrFail($id);

        return view('frontend.doctor.tickets.show', compact('ticket'));
    }

    public function reply(Request $request, $id)
    {
        $request->validate(['message' => 'required|string|min:5|max:5000']);

        $user = $this->getCurrentUser();
        $ticket = Ticket::where('ticketable_type', get_class($user))
            ->where('ticketable_id', $user->id)
            ->findOrFail($id);

        if ($ticket->isClosed()) {
            return response()->json(['status' => 'error', 'message' => __('Cannot reply to a closed ticket')]);
        }

        TicketReply::create([
            'ticket_id' => $ticket->id,
            'replied_by_type' => get_class($user),
            'replied_by_id' => $user->id,
            'message' => $request->message,
            'is_admin_reply' => false,
        ]);

        return request()->ajax()
            ? response()->json(['status' => 'success', 'message' => __('Reply sent successfully')])
            : redirect()->back()->with('success', __('Reply sent successfully'));
    }

    private function getLastReplyInfo($item): string
    {
        if ($item->latestReply) {
            $authorType = $item->latestReply->is_admin_reply ? 'Admin' : 'You';
            return '<small class="text-muted">' . __('Last reply by') . ' ' . $authorType . '<br>' . $item->latestReply->created_at->diffForHumans() . '</small>';
        }
        return '<small class="text-muted">' . __('No replies yet') . '</small>';
    }
}
