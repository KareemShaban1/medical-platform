<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Ticket\TicketStatusUpdateRequest;
use App\Http\Requests\Admin\Ticket\TicketReplyRequest;
use App\Interfaces\Admin\TicketRepositoryInterface;
use App\Models\Ticket;

class TicketController extends Controller
{
    protected $ticketRepo;

    public function __construct(TicketRepositoryInterface $ticketRepo)
    {
        $this->ticketRepo = $ticketRepo;
    }

    public function index()
    {
        return view('backend.dashboards.admin.pages.tickets.index');
    }

    public function data()
    {
        return $this->ticketRepo->data();
    }

    public function show($id)
    {
        $ticket = $this->ticketRepo->show($id);
        return request()->ajax()
            ? response()->json($ticket->load(['replies.repliedBy']))
            : view('backend.dashboards.admin.pages.tickets.show', compact('ticket'));
    }

    public function updateStatus(TicketStatusUpdateRequest $request, $id)
    {
        $this->ticketRepo->updateStatus($id, $request->validated()['status']);
        return $this->jsonResponse('success', __('Ticket status updated successfully'));
    }

    public function reply(TicketReplyRequest $request, $id)
    {
        $this->ticketRepo->reply($id, $request->validated());
        return $this->jsonResponse('success', __('Reply sent successfully'));
    }

    public function destroy($id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return $this->jsonResponse('success', __('Ticket deleted successfully'));
    }

    public function trash()
    {
        return view('backend.dashboards.admin.pages.tickets.trash');
    }

    public function trashData()
    {
        return $this->ticketRepo->trashData();
    }

    public function restore($id)
    {
        $this->ticketRepo->restore($id);
        return $this->jsonResponse('success', __('Ticket restored successfully'));
    }

    public function forceDelete($id)
    {
        $this->ticketRepo->forceDelete($id);
        return $this->jsonResponse('success', __('Ticket permanently deleted successfully'));
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
