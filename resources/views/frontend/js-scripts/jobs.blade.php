@push('scripts')
<script>
    // AJAX Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('search');
        const jobTypeSelect = document.getElementById('jobType');
        const specializationSelect = document.getElementById('specialization');
        const experienceRadios = document.querySelectorAll('input[name="experience"]');
        const locationSelect = document.getElementById('location');
        const salaryRadios = document.querySelectorAll('input[name="salary"]');
        const sortSelect = document.getElementById('sort');
        const clearFiltersBtn = document.getElementById('clearFilters');
        const jobsGrid = document.getElementById('jobsGrid');
        const resultsCount = document.getElementById('resultsCount');
        const paginationContainer = document.getElementById('paginationContainer');
        const gridViewBtn = document.getElementById('gridView');
        const listViewBtn = document.getElementById('listView');

        let filterTimeout;

        function filterJobs() {
            // Clear existing timeout
            clearTimeout(filterTimeout);

            // Add loading state
            jobsGrid.innerHTML =
                '<div class="col-span-full flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';

            // Set timeout for search input to avoid too many requests
            filterTimeout = setTimeout(() => {
                const formData = new FormData();

                // Get filter values
                if (searchInput.value) formData.append('search',
                    searchInput.value);
                if (jobTypeSelect.value) formData.append(
                    'job_type', jobTypeSelect
                    .value);
                if (specializationSelect.value) formData.append(
                    'specialization',
                    specializationSelect.value);

                const selectedExperience = document.querySelector(
                    'input[name="experience"]:checked'
                );
                if (selectedExperience && selectedExperience
                    .value) formData.append('experience',
                    selectedExperience.value);

                if (locationSelect.value) formData.append(
                    'location', locationSelect
                    .value);

                const selectedSalary = document.querySelector(
                    'input[name="salary"]:checked'
                );
                if (selectedSalary && selectedSalary.value)
                    formData.append('salary', selectedSalary
                        .value);

                if (sortSelect.value) formData.append('sort',
                    sortSelect.value);

                // Make AJAX request
                fetch('{{ route("jobs.filter") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document
                                .querySelector(
                                    'meta[name="csrf-token"]'
                                )
                                .getAttribute(
                                    'content'
                                )
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            jobsGrid.innerHTML =
                                data
                                .html;
                            paginationContainer
                                .innerHTML =
                                data
                                .pagination;
                            resultsCount
                                .textContent =
                                data
                                .count;
                        } else {
                            jobsGrid.innerHTML =
                                '<div class="col-span-full text-center py-8 text-gray-500">No jobs found</div>';
                            paginationContainer
                                .innerHTML =
                                '';
                            resultsCount
                                .textContent =
                                '0';
                        }
                    })
                    .catch(error => {
                        console.error('Error:',
                            error
                        );
                        jobsGrid.innerHTML =
                            '<div class="col-span-full text-center py-8 text-red-500">Error loading jobs</div>';
                    });
            }, searchInput === document.activeElement ? 500 : 0);
        }

        // Event listeners
        searchInput.addEventListener('input', filterJobs);
        jobTypeSelect.addEventListener('change', filterJobs);
        locationSelect.addEventListener('change', filterJobs);
        salaryRadios.forEach(radio => radio.addEventListener('change', filterJobs));
        sortSelect.addEventListener('change', filterJobs);

        clearFiltersBtn.addEventListener('click', function() {
            searchInput.value = '';
            jobTypeSelect.value = '';
            locationSelect.value = '';
            salaryRadios.forEach(radio => radio.checked = false);
            sortSelect.value = 'newest';
            filterJobs();
        });

        // View toggle
        gridViewBtn.addEventListener('click', function() {
            jobsGrid.className =
                'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6';
            gridViewBtn.className =
                'p-2 bg-gradient-primary text-white rounded';
            listViewBtn.className =
                'p-2 bg-gray-200 text-gray-600 rounded';
        });

        listViewBtn.addEventListener('click', function() {
            jobsGrid.className = 'grid grid-cols-1 gap-6';
            listViewBtn.className =
                'p-2 bg-gradient-primary text-white rounded';
            gridViewBtn.className =
                'p-2 bg-gray-200 text-gray-600 rounded';
        });
    });
</script>

@endpush
