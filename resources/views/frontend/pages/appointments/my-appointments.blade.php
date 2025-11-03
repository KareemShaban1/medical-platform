@extends('frontend.layouts.app')

@section('title', __('My Appointments'))

@section('content')
<div class="min-h-screen bg-gray-50 py-10 px-4">
  <div class="max-w-5xl mx-auto">
    <div class="bg-white shadow-2xl rounded-2xl overflow-hidden">
      <div class="flex justify-between items-center bg-gradient-to-r from-sky-600 to-blue-600 text-white px-6 py-4">
        <h2 class="text-2xl font-bold text-gray-800">{{ __('My Appointments') }}</h2>
        <a href="{{ route('user.dashboard') }}" class="px-4 py-2 rounded-lg bg-gray-200 text-gray-800 hover:bg-gray-300 transition">
          <i class="fas fa-arrow-left"></i> {{ __('Back to Dashboard') }}
        </a>
      </div>

      <div class="p-6">
        <form method="GET" action="{{ route('user.appointments.my') }}" class="mb-4">
          <div class="flex flex-wrap items-end gap-3">
            <div>
              <label class="block text-sm text-gray-600 mb-1">{{ __('From') }}</label>
              <input type="date" name="from" value="{{ request('from') }}" class="border rounded-md px-3 py-2">
            </div>
            <div>
              <label class="block text-sm text-gray-600 mb-1">{{ __('To') }}</label>
              <input type="date" name="to" value="{{ request('to') }}" class="border rounded-md px-3 py-2">
            </div>
            <div class="flex gap-2">
              <button type="submit" class="px-4 py-2 bg-sky-600 text-white rounded-md">{{ __('Filter') }}</button>
              <a href="{{ route('user.appointments.my') }}" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md">{{ __('Clear') }}</a>
            </div>
          </div>
        </form>
        @if($appointments->count())
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Date') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Time') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Doctor') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Clinic') }}</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('Status') }}</th>
                <th class="px-6 py-3"></th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              @foreach($appointments as $appointment)
                <tr class="hover:bg-gray-50 transition">
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                    {{ optional($appointment->period?->date)->format('M d, Y') ?? '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ $appointment->period?->start_time ?? '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                    {{ $appointment->doctorProfile?->name ?? '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                    {{ optional($appointment->doctorProfile?->clinicUser?->clinic)->name ?? '-' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    {!! $appointment->status_badge !!}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                    @if($appointment->status === \App\Models\Appointment::STATUS_CONFIRMED || $appointment->status === \App\Models\Appointment::STATUS_PENDING)
                      <button onclick="cancelAppointment({{ $appointment->id }})" class="px-3 py-1.5 rounded-md bg-red-500 hover:bg-red-600 text-white text-xs font-semibold">
                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                      </button>
                    @endif
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="mt-4">
          {{ $appointments->links() }}
        </div>
        @else
          <div class="text-center py-10">
            <i class="fas fa-calendar-times text-5xl text-gray-400 mb-3"></i>
            <p class="text-gray-600">{{ __('No appointments found') }}</p>
          </div>
        @endif
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function cancelAppointment(id) {
  Swal.fire({
    title: '{{ __('Cancel appointment?') }}',
    text: '{{ __('You can’t undo this action.') }}',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#6b7280',
    confirmButtonText: '{{ __('Yes, cancel it!') }}',
    cancelButtonText: '{{ __('No, keep it') }}'
  }).then((result) => {
    if (!result.isConfirmed) return;

    fetch(`{{ route('user.appointments.cancel', ':id') }}`.replace(':id', id), {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    })
    .then(res => res.json())
    .then(data => {
      if (data.status === 'success') {
        Swal.fire({
          icon: 'success',
          title: '{{ __('Cancelled!') }}',
          text: '{{ __('Appointment cancelled successfully') }}',
          timer: 1200,
          showConfirmButton: false
        }).then(() => location.reload());
      } else {
        Swal.fire('Error', data.message || 'Error', 'error');
      }
    })
    .catch(() => Swal.fire('Error', 'Something went wrong', 'error'));
  });
}
</script>
@endsection
