@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
  const searchInput = document.getElementById('search');
  const heroSearch = document.getElementById('heroSearch');
  const priceSelect = document.getElementById('price');
  const availabilitySelect = document.getElementById('availability');
  const sortSelect = document.getElementById('sort');
  const clearFiltersBtn = document.getElementById('clearFilters');
  const grid = document.getElementById('rentalSpacesGrid');
  const resultsCount = document.getElementById('resultsCount');
  const paginationContainer = document.getElementById('paginationContainer');
  const gridViewBtn = document.getElementById('gridView');
  const listViewBtn = document.getElementById('listView');

  let filterTimeout;

  function filterSpaces() {
    clearTimeout(filterTimeout);
    grid.innerHTML = '<div class="col-span-full flex justify-center items-center py-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div></div>';
    filterTimeout = setTimeout(() => {
      const formData = new FormData();
      if (searchInput && searchInput.value) formData.append('search', searchInput.value);
      if (heroSearch && heroSearch.value) formData.append('search', heroSearch.value);
      if (priceSelect && priceSelect.value) formData.append('price', priceSelect.value);
      if (availabilitySelect && availabilitySelect.value) formData.append('availability', availabilitySelect.value);
      if (sortSelect && sortSelect.value) formData.append('sort', sortSelect.value);

      fetch('{{ route("rental-spaces.filter") }}', {
        method: 'POST',
        body: formData,
        headers: {
          'X-Requested-With': 'XMLHttpRequest',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
      })
      .then(r => r.json())
      .then(data => {
        if (data.success) {
          grid.innerHTML = data.html || '<div class="col-span-full text-center py-8 text-gray-500">{{ __('No rental spaces found') }}</div>';
          if (paginationContainer) paginationContainer.innerHTML = data.pagination || '';
          if (resultsCount) resultsCount.textContent = data.count ?? 0;
        } else {
          grid.innerHTML = '<div class="col-span-full text-center py-8 text-red-500">{{ __('Error loading rental spaces') }}</div>';
        }
      })
      .catch(() => {
        grid.innerHTML = '<div class="col-span-full text-center py-8 text-red-500">{{ __('Error loading rental spaces') }}</div>';
      });
    }, (searchInput && searchInput === document.activeElement) ? 500 : 0);
  }

  // listeners
  if (searchInput) searchInput.addEventListener('input', filterSpaces);
  if (heroSearch) heroSearch.addEventListener('input', filterSpaces);
  if (priceSelect) priceSelect.addEventListener('change', filterSpaces);
  if (availabilitySelect) availabilitySelect.addEventListener('change', filterSpaces);
  if (sortSelect) sortSelect.addEventListener('change', filterSpaces);

  if (clearFiltersBtn) clearFiltersBtn.addEventListener('click', function() {
    if (searchInput) searchInput.value = '';
    if (heroSearch) heroSearch.value = '';
    if (priceSelect) priceSelect.value = '';
    if (availabilitySelect) availabilitySelect.value = '';
    if (sortSelect) sortSelect.value = 'name';
    filterSpaces();
  });

  // View toggle
  if (gridViewBtn && listViewBtn) {
    gridViewBtn.addEventListener('click', function() {
      grid.className = 'grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8';
      gridViewBtn.className = 'p-3 bg-gradient-primary text-white rounded-xl';
      listViewBtn.className = 'p-3 bg-gray-200 text-gray-600 rounded-xl';
    });
    listViewBtn.addEventListener('click', function() {
      grid.className = 'grid grid-cols-1 gap-6';
      listViewBtn.className = 'p-3 bg-gradient-primary text-white rounded-xl';
      gridViewBtn.className = 'p-3 bg-gray-200 text-gray-600 rounded-xl';
    });
  }

  // Collapsible filter panel toggle
  const toggleFilters = document.getElementById('toggleFilters');
  const filtersPanel = document.getElementById('filtersPanel');
  const filterChevron = document.getElementById('filterChevron');
  const activeFiltersCount = document.getElementById('activeFiltersCount');
  const filterCount = document.getElementById('filterCount');

  let isFiltersOpen = false;
  let activeFilterCount = 0;

  if (toggleFilters && filtersPanel) {
    toggleFilters.addEventListener('click', function() {
      isFiltersOpen = !isFiltersOpen;
      if (isFiltersOpen) {
        filtersPanel.classList.remove('hidden');
        if (filterChevron) filterChevron.style.transform = 'rotate(180deg)';
        toggleFilters.classList.add('bg-primary-dark');
      } else {
        filtersPanel.classList.add('hidden');
        if (filterChevron) filterChevron.style.transform = 'rotate(0deg)';
        toggleFilters.classList.remove('bg-primary-dark');
      }
    });
  }

  function updateActiveFilters() {
    activeFilterCount = 0;
    if (searchInput && searchInput.value) activeFilterCount++;
    if (availabilitySelect && availabilitySelect.value) activeFilterCount++;
    if (priceSelect && priceSelect.value) activeFilterCount++;
    if (sortSelect && sortSelect.value && sortSelect.value !== 'name') activeFilterCount++;
    if (activeFiltersCount) {
      if (activeFilterCount > 0) {
        activeFiltersCount.classList.remove('hidden');
        if (filterCount) filterCount.textContent = activeFilterCount;
      } else {
        activeFiltersCount.classList.add('hidden');
      }
    }
  }

  if (searchInput) searchInput.addEventListener('input', updateActiveFilters);
  if (availabilitySelect) availabilitySelect.addEventListener('change', updateActiveFilters);
  if (priceSelect) priceSelect.addEventListener('change', updateActiveFilters);
  if (sortSelect) sortSelect.addEventListener('change', updateActiveFilters);
});
</script>
@endpush
