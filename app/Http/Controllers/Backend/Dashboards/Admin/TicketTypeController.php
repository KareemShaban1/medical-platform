<?php

namespace App\Http\Controllers\Backend\Dashboards\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\TicketType\StoreTicketTypeRequest;
use App\Http\Requests\Admin\TicketType\UpdateTicketTypeRequest;
use App\Interfaces\Admin\TicketTypeRepositoryInterface;
use App\Models\TicketType;

class TicketTypeController extends Controller
{
    protected $ticketTypeRepo;

    public function __construct(TicketTypeRepositoryInterface $ticketTypeRepo)
    {
        $this->ticketTypeRepo = $ticketTypeRepo;
    }

    public function index()
    {
        abort_if(!hasPermission('view ticket types'), 403, __('You are not authorized to view ticket types'));
        $userTypes = TicketType::availableUserTypes();
        return view('backend.dashboards.admin.pages.ticket-types.index', compact('userTypes'));
    }

    public function data()
    {
        abort_if(!hasPermission('view ticket types'), 403, __('You are not authorized to view ticket types'));
        return $this->ticketTypeRepo->data();
    }

    public function store(StoreTicketTypeRequest $request)
    {
        abort_if(!hasPermission('create ticket type'), 403, __('You are not authorized to create ticket type'));
        $this->ticketTypeRepo->store($request->validated());
        return $this->jsonResponse('success', __('Ticket type created successfully'));
    }

    public function show($id)
    {
        abort_if(!hasPermission('view ticket types'), 403, __('You are not authorized to view ticket type'));
        $ticketType = $this->ticketTypeRepo->show($id);

        return request()->ajax()
            ? response()->json($ticketType)
            : view('backend.dashboards.admin.pages.ticket-types.show', compact('ticketType'));
    }

    public function update(UpdateTicketTypeRequest $request, $id)
    {
        abort_if(!hasPermission('update ticket type'), 403, __('You are not authorized to update ticket type'));
        $this->ticketTypeRepo->update($request->validated(), $id);
        return $this->jsonResponse('success', __('Ticket type updated successfully'));
    }

    public function destroy($id)
    {
        abort_if(!hasPermission('delete ticket type'), 403, __('You are not authorized to delete ticket type'));
        $this->ticketTypeRepo->destroy($id);
        return $this->jsonResponse('success', __('Ticket type deleted successfully'));
    }

    public function trash()
    {
        abort_if(!hasPermission('view trash ticket types'), 403, __('You are not authorized to view trash ticket types'));
        return view('backend.dashboards.admin.pages.ticket-types.trash');
    }

    public function trashData()
    {
        abort_if(!hasPermission('view trash ticket types'), 403, __('You are not authorized to view trash ticket types'));
        return $this->ticketTypeRepo->trashData();
    }

    public function restore($id)
    {
        abort_if(!hasPermission('restore ticket type'), 403, __('You are not authorized to restore ticket type'));
        $this->ticketTypeRepo->restore($id);
        return $this->jsonResponse('success', __('Ticket type restored successfully'));
    }

    public function forceDelete($id)
    {
        abort_if(!hasPermission('force delete ticket type'), 403, __('You are not authorized to force delete ticket type'));
        $this->ticketTypeRepo->forceDelete($id);
        return $this->jsonResponse('success', __('Ticket type permanently deleted successfully'));
    }

    private function jsonResponse(string $status, string $message)
    {
        if (request()->ajax()) {
            return response()->json(['status' => $status, 'message' => $message]);
        }

        return redirect()->back()->with($status, $message);
    }
}
