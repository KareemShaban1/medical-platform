<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    /**
     * Store a new contact message
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'company' => 'nullable|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string|min:10|max:1000',
            'agree_to_policies' => 'required|accepted',
        ], [
            'first_name.required' => __('First name is required'),
            'last_name.required' => __('Last name is required'),
            'email.required' => __('Email is required'),
            'email.email' => __('Please enter a valid email address'),
            'phone.required' => __('Phone number is required'),
            'message.required' => __('Message is required'),
            'message.min' => __('Message must be at least 10 characters'),
            'message.max' => __('Message cannot exceed 1000 characters'),
            'agree_to_policies.required' => __('You must agree to our privacy policy'),
            'agree_to_policies.accepted' => __('You must agree to our privacy policy'),
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            ContactMessage::create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'company' => $request->company,
                'email' => $request->email,
                'phone' => $request->phone,
                'message' => $request->message,
                'agree_to_policies' => $request->agree_to_policies ? true : false,
                'status' => 'new',
            ]);

            return response()->json([
                'success' => true,
                'message' => __('Thank you for contacting us! We will get back to you soon.')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while sending your message. Please try again.')
            ], 500);
        }
    }
}
