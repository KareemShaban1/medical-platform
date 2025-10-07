<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\Ticket\TicketStoreRequest;
use App\Http\Requests\User\Ticket\TicketReplyRequest;
use App\Interfaces\User\TicketRepositoryInterface;

class TicketController extends Controller
{
    protected $ticketRepo;

    public function __construct(TicketRepositoryInterface $ticketRepo)
    {
        $this->ticketRepo = $ticketRepo;
    }

    public function index()
    {
        return view('frontend.tickets.index');
    }

    public function data()
    {
        return $this->ticketRepo->data();
    }

    public function store(TicketStoreRequest $request)
    {
        $this->ticketRepo->store($request->validated());
        return $this->jsonResponse('success', __('Ticket submitted successfully'));
    }

    public function show($id)
    {
        $ticket = $this->ticketRepo->show($id);
        return request()->ajax()
            ? response()->json($ticket->load(['replies.repliedBy']))
            : view('frontend.tickets.show', compact('ticket'));
    }

    public function reply(TicketReplyRequest $request, $id)
    {
        try {
            $this->ticketRepo->reply($id, $request->validated());
            return $this->jsonResponse('success', __('Reply sent successfully'));
        } catch (\Exception $e) {
            return $this->jsonResponse('error', $e->getMessage());
        }
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
