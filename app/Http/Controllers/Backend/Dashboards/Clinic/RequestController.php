<?php

namespace App\Http\Controllers\Backend\Dashboards\Clinic;

use App\Http\Controllers\Controller;
use App\Http\Requests\Clinic\CreateRequestRequest;
use App\Http\Requests\Clinic\UpdateRequestRequest;
use App\Interfaces\Clinic\RequestRepositoryInterface;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    protected $requestRepository;

    public function __construct(RequestRepositoryInterface $requestRepository)
    {
        $this->requestRepository = $requestRepository;
    }

    public function index()
    {
        return view('backend.dashboards.clinic.pages.requests.index');
    }

    public function data()
    {
        return $this->requestRepository->data();
    }

    public function create()
    {
        $categories = $this->requestRepository->getCategories();
        return view('backend.dashboards.clinic.pages.requests.create', compact('categories'));
    }

    public function store(CreateRequestRequest $request)
    {
        try {
            $this->requestRepository->store($request->validated());
            return response()->json([
                'success' => true,
                'message' => 'Request created successfully and sent to suppliers.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    public function show($id)
    {
        try {
            $request = $this->requestRepository->show($id);
            return view('backend.dashboards.clinic.pages.requests.show', compact('request'));
        } catch (\Exception $e) {
            return redirect()->route('clinic.requests.index')->with('error', $e->getMessage());
        }
    }

    public function edit($id)
    {
        try {
            $request = $this->requestRepository->show($id);
            $categories = $this->requestRepository->getCategories();
            return view('backend.dashboards.clinic.pages.requests.edit', compact('request', 'categories'));
        } catch (\Exception $e) {
            return redirect()->route('clinic.requests.index')->with('error', $e->getMessage());
        }
    }

    public function update(UpdateRequestRequest $request, $id)
    {
        try {
            $this->requestRepository->update($request->validated(), $id);
            return response()->json([
                'success' => true,
                'message' => 'Request updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function destroy($id)
    {
        try {
            $this->requestRepository->destroy($id);
            return response()->json([
                'success' => true,
                'message' => 'Request deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function acceptOffer(Request $request, $requestId)
    {
        try {
            $offerId = $request->input('offer_id');
            $this->requestRepository->acceptOffer($requestId, $offerId);
            return response()->json([
                'success' => true,
                'message' => 'Offer accepted successfully. Request has been closed.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function cancel($id)
    {
        try {
            $this->requestRepository->cancelRequest($id);
            return response()->json([
                'success' => true,
                'message' => 'Request canceled successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function getCategories()
    {
        try {
            $categories = $this->requestRepository->getCategories();
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
}
