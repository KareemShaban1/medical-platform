@extends('frontend.layouts.app')

@section('content')
<!-- Page Header -->
<div class="bg-gradient-primary">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <h1 class="text-3xl font-bold text-white">Medical Jobs</h1>
        <p class="text-white/90 mt-2">Find your next career opportunity in healthcare</p>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <!-- Filters Sidebar -->
        <div class="lg:w-1/4">
            <div class="card p-6">
                <h3 class="text-lg font-semibold mb-4">Filters</h3>

                <!-- Search Filter -->
                <div class="mb-6">
                    <label
                        class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                    <div class="relative">
                        <input type="text" id="search" placeholder="Search jobs..."
                            class="form-input w-full pl-10 pr-4 py-2">
                        <i
                            class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                    </div>
                </div>

                <!-- Job Type Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Job
                        Type</label>
                    <select id="jobType"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Job Types</option>
                        @foreach($jobTypes as $type)
                        <option value="{{ $type }}">
                            {{ ucfirst(str_replace('-', ' ', $type)) }}
                        </option>
                        @endforeach
                    </select>
                </div>


                <!-- Location Filter -->
                <div class="mb-6">
                    <label
                        class="block text-sm font-medium text-gray-700 mb-2">Location</label>
                    <select id="location"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">All Locations</option>
                        @foreach($locations as $location)
                        <option value="{{ $location }}">
                            {{ $location }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Salary Range Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Salary
                        Range</label>
                    <div class="space-y-2">
                        <div class="flex items-center">
                            <input type="radio" id="salary-all" name="salary"
                                value="" class="mr-2" checked>
                            <label for="salary-all" class="text-sm">Any
                                Salary</label>
                        </div>
                        @foreach($salaryRanges as $range)
                        <div class="flex items-center">
                            <input type="radio" id="salary-{{ $range }}"
                                name="salary" value="{{ $range }}"
                                class="mr-2">
                            <label for="salary-{{ $range }}"
                                class="text-sm">${{ $range }}k
                                {{ $range == '80+' ? '+' : '' }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Sort Filter -->
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Sort
                        By</label>
                    <select id="sort"
                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="newest">Newest First</option>
                        <option value="salary">Highest Salary</option>
                        <option value="title">Job Title A-Z</option>
                        <option value="company">Company A-Z</option>
                    </select>
                </div>

                <!-- Clear Filters -->
                <button id="clearFilters"
                    class="btn-secondary w-full">
                    Clear All Filters
                </button>
            </div>
        </div>

        <!-- Jobs Grid -->
        <div class="lg:w-3/4">
            <!-- Results Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <span class="text-gray-600">Showing <span
                            id="resultsCount">{{ $jobs->total() }}</span>
                        jobs</span>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="text-sm text-gray-600">View:</span>
                    <button id="gridView" class="p-2 bg-gradient-primary text-white rounded">
                        <i class="fas fa-th"></i>
                    </button>
                    <button id="listView" class="p-2 bg-gray-200 text-gray-600 rounded">
                        <i class="fas fa-list"></i>
                    </button>
                </div>
            </div>

            <!-- Jobs Grid -->
            <div id="jobsGrid" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @include('frontend.partials.jobs-grid', ['jobs' => $jobs])
            </div>

            <!-- Pagination -->
            <div id="paginationContainer">
                {{ $jobs->links() }}
            </div>
        </div>
    </div>
</div>

@endsection

@include('frontend.js-scripts.jobs')
