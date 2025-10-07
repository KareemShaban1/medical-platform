{{--
    Comprehensive Input Component Examples
    This file demonstrates all the different ways to use the input component
--}}

@extends('backend.dashboards.clinic.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Input Component Examples</h4>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="row">
                            <!-- Basic Text Inputs -->
                            <div class="col-md-6">
                                <h5>Basic Text Inputs</h5>

                                <x-input
                                    type="text"
                                    name="name"
                                    label="Full Name"
                                    placeholder="Enter your full name"
                                    required />

                                <x-input
                                    type="email"
                                    name="email"
                                    label="Email Address"
                                    placeholder="Enter your email"
                                    required />

                                <x-input
                                    type="password"
                                    name="password"
                                    label="Password"
                                    placeholder="Enter your password"
                                    required />

                                <x-input
                                    type="tel"
                                    name="phone"
                                    label="Phone Number"
                                    placeholder="Enter your phone number" />

                                <x-input
                                    type="url"
                                    name="website"
                                    label="Website"
                                    placeholder="https://example.com" />

                                <x-input
                                    type="search"
                                    name="search"
                                    label="Search"
                                    placeholder="Search..." />
                            </div>

                            <!-- Number and Date Inputs -->
                            <div class="col-md-6">
                                <h5>Number and Date Inputs</h5>

                                <x-input
                                    type="number"
                                    name="age"
                                    label="Age"
                                    min="0"
                                    max="120"
                                    step="1" />

                                <x-input
                                    type="number"
                                    name="price"
                                    label="Price"
                                    min="0"
                                    step="0.01"
                                    help-text="Enter price in dollars" />

                                <x-input
                                    type="date"
                                    name="birth_date"
                                    label="Birth Date" />

                                <x-input
                                    type="time"
                                    name="appointment_time"
                                    label="Appointment Time" />

                                <x-input
                                    type="datetime-local"
                                    name="meeting_datetime"
                                    label="Meeting Date & Time" />

                                <x-input
                                    type="color"
                                    name="favorite_color"
                                    label="Favorite Color" />
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Textarea and Select -->
                            <div class="col-md-6">
                                <h5>Textarea and Select</h5>

                                <x-input
                                    type="textarea"
                                    name="description"
                                    label="Description"
                                    rows="4"
                                    placeholder="Enter a description" />

                                <x-input
                                    type="select"
                                    name="country"
                                    label="Country"
                                    :options="[
                                        'us' => 'United States',
                                        'ca' => 'Canada',
                                        'uk' => 'United Kingdom',
                                        'au' => 'Australia'
                                    ]"
                                    placeholder="Select a country" />

                                <x-input
                                    type="select"
                                    name="skills"
                                    label="Skills"
                                    :options="[
                                        'Frontend' => [
                                            'html' => 'HTML',
                                            'css' => 'CSS',
                                            'js' => 'JavaScript'
                                        ],
                                        'Backend' => [
                                            'php' => 'PHP',
                                            'python' => 'Python',
                                            'java' => 'Java'
                                        ]
                                    ]"
                                    multiple />
                            </div>

                            <!-- Checkbox and Radio -->
                            <div class="col-md-6">
                                <h5>Checkbox and Radio</h5>

                                <x-input
                                    type="checkbox"
                                    name="newsletter"
                                    label="Subscribe to newsletter" />

                                <x-input
                                    type="checkbox"
                                    name="terms"
                                    label="I agree to the terms and conditions"
                                    required />

                                <x-input
                                    type="radio"
                                    name="gender"
                                    label="Gender"
                                    :options="[
                                        'male' => 'Male',
                                        'female' => 'Female',
                                        'other' => 'Other'
                                    ]"
                                    required />

                                <x-input
                                    type="radio"
                                    name="experience"
                                    label="Experience Level"
                                    :options="[
                                        'beginner' => 'Beginner',
                                        'intermediate' => 'Intermediate',
                                        'advanced' => 'Advanced'
                                    ]"
                                    inline />
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- File Uploads -->
                            <div class="col-md-6">
                                <h5>File Uploads</h5>

                                <x-input
                                    type="file"
                                    name="avatar"
                                    label="Profile Picture"
                                    accept="image/*"
                                    preview />

                                <x-input
                                    type="file"
                                    name="documents"
                                    label="Documents"
                                    accept=".pdf,.doc,.docx"
                                    multiple />

                                <x-input
                                    type="file"
                                    name="gallery"
                                    label="Photo Gallery"
                                    accept="image/*"
                                    multiple
                                    preview />
                            </div>

                            <!-- Range and Special Inputs -->
                            <div class="col-md-6">
                                <h5>Range and Special Inputs</h5>

                                <x-input
                                    type="range"
                                    name="satisfaction"
                                    label="Satisfaction Level"
                                    min="0"
                                    max="10"
                                    step="1" />

                                <x-input
                                    type="checkbox"
                                    name="notifications"
                                    label="Enable Notifications"
                                    switch
                                    color="success" />

                                <x-input
                                    type="checkbox"
                                    name="dark_mode"
                                    label="Dark Mode"
                                    switch
                                    color="primary" />
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Input with Icons -->
                            <div class="col-md-6">
                                <h5>Inputs with Icons</h5>

                                <x-input
                                    type="text"
                                    name="username"
                                    label="Username"
                                    icon="fas fa-user"
                                    placeholder="Enter username" />

                                <x-input
                                    type="email"
                                    name="contact_email"
                                    label="Contact Email"
                                    icon="fas fa-envelope"
                                    placeholder="Enter email" />

                                <x-input
                                    type="tel"
                                    name="contact_phone"
                                    label="Phone"
                                    icon="fas fa-phone"
                                    placeholder="Enter phone number" />
                            </div>

                            <!-- Different Sizes -->
                            <div class="col-md-6">
                                <h5>Different Sizes</h5>

                                <x-input
                                    type="text"
                                    name="small_input"
                                    label="Small Input"
                                    size="sm"
                                    placeholder="Small input" />

                                <x-input
                                    type="text"
                                    name="medium_input"
                                    label="Medium Input"
                                    size="md"
                                    placeholder="Medium input" />

                                <x-input
                                    type="text"
                                    name="large_input"
                                    label="Large Input"
                                    size="lg"
                                    placeholder="Large input" />
                            </div>
                        </div>

                        <div class="row mt-4">
                            <!-- Floating Labels -->
                            <div class="col-md-6">
                                <h5>Floating Labels</h5>

                                <x-input
                                    type="text"
                                    name="floating_name"
                                    label="Full Name"
                                    floating
                                    placeholder="Enter your name" />

                                <x-input
                                    type="email"
                                    name="floating_email"
                                    label="Email Address"
                                    floating
                                    placeholder="Enter your email" />

                                <x-input
                                    type="textarea"
                                    name="floating_message"
                                    label="Message"
                                    floating
                                    rows="3"
                                    placeholder="Enter your message" />
                            </div>

                            <!-- Custom Styling -->
                            <div class="col-md-6">
                                <h5>Custom Styling</h5>

                                <x-input
                                    type="text"
                                    name="custom_input"
                                    label="Custom Styled Input"
                                    input-class="border-success"
                                    label-class="text-success fw-bold"
                                    placeholder="Custom styled input" />

                                <x-input
                                    type="text"
                                    name="grouped_input"
                                    label="Grouped Input"
                                    group-class="mb-4 p-3 border rounded bg-light"
                                    placeholder="Input with custom group styling" />
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary">Submit Form</button>
                                <button type="reset" class="btn btn-secondary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .form-switch .form-check-input:checked {
        background-color: var(--bs-primary);
        border-color: var(--bs-primary);
    }

    .input-group-text {
        background-color: var(--bs-light);
        border-color: var(--bs-border-color);
    }

    .form-control:focus {
        border-color: var(--bs-primary);
        box-shadow: 0 0 0 0.2rem rgba(var(--bs-primary-rgb), 0.25);
    }
</style>
@endpush


