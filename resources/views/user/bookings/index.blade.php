@extends('layouts/contentNavbarLayout')

@section('title', 'My Bookings')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0">My Hall Bookings</h5>
        <a href="{{ route('user.bookings.create') }}" class="btn btn-primary">
            <i class="bx bx-plus"></i> New Booking
        </a>
    </div>

    @php
        $renderRows = function ($bookings, $showCancellationReason = false, $allowCancel = false) {
            $statusBadges = [
                'pending' => 'warning',
                'approved' => 'success',
                'accepted' => 'success',
                'rejected' => 'danger',
                'kept_pending' => 'info',
                'not_required' => 'secondary',
                'waiting for media' => 'info',
                'confirmed' => 'success',
                'confirmed without media' => 'success',
            ];

            foreach ($bookings as $booking) {
                $adminBadge = $statusBadges[$booking->admin_status] ?? 'secondary';
                $mediaBadge = $statusBadges[$booking->media_status] ?? 'secondary';
                $bookingBadge = $statusBadges[$booking->booking_status] ?? 'secondary';

                echo '<tr>';
                echo '<td>' . e($booking->id) . '</td>';
                echo '<td>' . e(optional($booking->hall)->name ?? 'N/A') . '</td>';
                echo '<td>' . e($booking->event_name ?? 'N/A') . '</td>';
                echo '<td>' . e($booking->formatted_datetime_range) . '</td>';

                echo '<td>';
                echo '<div class="d-flex flex-column gap-1">';
                echo '<span class="badge bg-' . $bookingBadge . '">Overall: ' . e(ucfirst(str_replace('_', ' ', $booking->booking_status))) . '</span>';
                echo '<div class="d-flex gap-1">';
                echo '<small class="badge bg-label-' . $adminBadge . '" style="font-size: 0.65rem;">Admin: ' . e(ucfirst(str_replace('_', ' ', $booking->admin_status))) . '</small>';
                if ($booking->media_status !== 'not_required') {
                    echo '<small class="badge bg-label-' . $mediaBadge . '" style="font-size: 0.65rem;">Media: ' . e(ucfirst(str_replace('_', ' ', $booking->media_status))) . '</small>';
                }
                echo '</div>';
                
                // Media Feedback Display
                if (($booking->media_status === 'rejected' || $booking->media_status === 'kept_pending') && $booking->media_feedback_reason) {
                    echo '<div class="mt-1 p-2 border rounded bg-light-warning" style="font-size: 0.75rem; line-height: 1.2;">';
                    echo '<strong>Media Note:</strong> ' . e($booking->media_feedback_reason);
                    if ($booking->media_remarks) {
                        echo '<br><i class="text-muted">' . e($booking->media_remarks) . '</i>';
                    }
                    echo '</div>';
                } elseif ($booking->media_status === 'accepted' && !empty($booking->accepted_media_requirements)) {
                    echo '<div class="mt-1 p-2 border border-success rounded bg-light text-success" style="font-size: 0.75rem; line-height: 1.2;">';
                    echo '<strong>Media Accepted:</strong> ' . e(implode(', ', array_map('ucfirst', $booking->accepted_media_requirements)));
                    echo '</div>';
                }
                echo '</div>';
                echo '</td>';

                if ($showCancellationReason) {
                    echo '<td>' . e($booking->cancellation_reason ?? '-') . '</td>';
                }
                echo '<td>' . e(optional($booking->created_at)->format('M d, Y h:i A')) . '</td>';

                if ($allowCancel) {
                    $canCancel = !in_array($booking->booking_status, ['cancelled', 'completed', 'rejected']);
                    echo '<td>';
                    if ($canCancel) {
                        $safeEvent = addslashes($booking->event_name ?? 'Event');
                        $safeHall = addslashes(optional($booking->hall)->name ?? 'Hall');
                        echo '<button type="button" class="btn btn-sm btn-outline-danger" onclick="openCancelModal(' . $booking->id . ', \'' . $safeEvent . '\', \'' . $safeHall . '\')"><i class="bx bx-x-circle me-1"></i> Cancel</button>';
                    } else {
                        echo '<span class="text-muted small">-</span>';
                    }
                    echo '</td>';
                }

                echo '</tr>';
            }
        };
    @endphp

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Upcoming Bookings</h6>
            <span class="badge bg-success">{{ $upcomingBookings->count() }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Hall</th>
                            <th>Event Name</th>
                            <th>Date / Time</th>
                            <th>Status</th>
                            <th>Created On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($upcomingBookings->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center text-muted">No upcoming bookings.</td>
                            </tr>
                        @else
                            {!! $renderRows($upcomingBookings, false, true) !!}
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Completed (Done) Bookings</h6>
            <span class="badge bg-secondary">{{ $completedBookings->count() }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Hall</th>
                            <th>Event Name</th>
                            <th>Date / Time</th>
                            <th>Status</th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($completedBookings->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center text-muted">No completed bookings.</td>
                            </tr>
                        @else
                            {!! $renderRows($completedBookings) !!}
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Cancelled Bookings</h6>
            <span class="badge bg-danger">{{ $cancelledBookings->count() }}</span>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Hall</th>
                            <th>Event Name</th>
                            <th>Date / Time</th>
                            <th>Status</th>
                            <th>Cancellation Reason</th>
                            <th>Created On</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($cancelledBookings->isEmpty())
                            <tr>
                                <td colspan="7" class="text-center text-muted">No cancelled bookings.</td>
                            </tr>
                        @else
                            {!! $renderRows($cancelledBookings, true) !!}
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Direct Cancellation Modal -->
<div class="modal fade" id="cancelBookingModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content border-danger">
            <form action="{{ route('user.bookings.cancel.submit') }}" method="POST">
                @csrf
                <input type="hidden" name="booking_id" id="modal_booking_id">

                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title text-white" id="cancelModalTitle"><i class="bx bx-error-circle me-1"></i> Confirm Cancellation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <strong>Warning:</strong> You are about to cancel the booking for <strong id="modal_event_name">...</strong> at <strong id="modal_hall_name">...</strong>. This action will immediately release the time slot.
                    </div>

                    <h6 class="mb-3 mt-4">Please provide a reason for cancellation:</h6>
                    
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="cancellation_reason_option" id="modal_reason_postponed" value="postponed" required>
                        <label class="form-check-label" for="modal_reason_postponed">Postponed</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="cancellation_reason_option" id="modal_reason_event_cancelled" value="event_cancelled" required>
                        <label class="form-check-label" for="modal_reason_event_cancelled">Event Cancelled</label>
                    </div>
                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="cancellation_reason_option" id="modal_reason_low_participation" value="low_participation" required>
                        <label class="form-check-label" for="modal_reason_low_participation">Low Participation</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="cancellation_reason_option" id="modal_reason_other" value="other" required>
                        <label class="form-check-label" for="modal_reason_other">Other</label>
                    </div>

                    <label for="cancellation_reason_other" class="form-label mt-2">Other Reason (if selected)</label>
                    <textarea id="cancellation_reason_other" name="cancellation_reason_other" rows="3" class="form-control" placeholder="Enter your detailed reason here"></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openCancelModal(bookingId, eventName, hallName) {
        document.getElementById('modal_booking_id').value = bookingId;
        document.getElementById('modal_event_name').textContent = eventName;
        document.getElementById('modal_hall_name').textContent = hallName;
        
        var myModal = new bootstrap.Modal(document.getElementById('cancelBookingModal'));
        myModal.show();
    }
</script>
@endsection
