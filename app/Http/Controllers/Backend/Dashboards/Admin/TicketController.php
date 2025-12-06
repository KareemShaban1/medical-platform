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
        // apply permissions
        abort_if(!hasPermission('view tickets'), 403, __('You are not authorized to view tickets'));
        return view('backend.dashboards.admin.pages.tickets.index');
    }

    public function data()
    {
        // apply permissions
        abort_if(!hasPermission('view tickets'), 403, __('You are not authorized to view tickets'));
        return $this->ticketRepo->data();
    }

    public function show($id)
    {
        // apply permissions
        abort_if(!hasPermission('view tickets'), 403, __('You are not authorized to view ticket'));
        $ticket = $this->ticketRepo->show($id);
        return request()->ajax()
            ? response()->json($ticket->load(['replies.repliedBy']))
            : view('backend.dashboards.admin.pages.tickets.show', compact('ticket'));
    }

    public function updateStatus(TicketStatusUpdateRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('update ticket'), 403, __('You are not authorized to update ticket'));
        $this->ticketRepo->updateStatus($id, $request->validated()['status']);
        return $this->jsonResponse('success', __('Ticket status updated successfully'));
    }

    public function reply(TicketReplyRequest $request, $id)
    {
        // apply permissions
        abort_if(!hasPermission('reply ticket'), 403, __('You are not authorized to reply to ticket'));
        $this->ticketRepo->reply($id, $request->validated());
        return $this->jsonResponse('success', __('Reply sent successfully'));
    }

    public function destroy($id)
    {
        // apply permissions
        abort_if(!hasPermission('delete ticket'), 403, __('You are not authorized to delete ticket'));
        $ticket = Ticket::findOrFail($id);
        $ticket->delete();
        return $this->jsonResponse('success', __('Ticket deleted successfully'));
    }

    public function trash()
    {
        // apply permissions
        abort_if(!hasPermission('view trash tickets'), 403, __('You are not authorized to view trash tickets'));
        return view('backend.dashboards.admin.pages.tickets.trash');
    }

    public function trashData()
    {
        // apply permissions
        abort_if(!hasPermission('view trash tickets'), 403, __('You are not authorized to view trash tickets'));
        return $this->ticketRepo->trashData();
    }

    public function restore($id)
    {
        // apply permissions
        abort_if(!hasPermission('restore ticket'), 403, __('You are not authorized to restore ticket'));
        $this->ticketRepo->restore($id);
        return $this->jsonResponse('success', __('Ticket restored successfully'));
    }

    public function forceDelete($id)
    {
        // apply permissions
        abort_if(!hasPermission('force delete ticket'), 403, __('You are not authorized to force delete ticket'));
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
