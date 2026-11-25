{{-- PAIR 3: the decision buttons for one row of the management viewing queue. --}}
@if ($viewing->isRequested())
    <form method="POST" action="{{ route('management.viewings.approve', $viewing) }}">
        @csrf
        <input class="form-control" type="datetime-local" name="scheduled_at"
               value="{{ $viewing->scheduled_at->format('Y-m-d\TH:i') }}"
               min="{{ now()->format('Y-m-d\TH:i') }}">
        <button type="submit" class="btn mt-1">Approve slot</button>
    </form>

    <form method="POST" action="{{ route('management.viewings.reject', $viewing) }}" class="mt-2">
        @csrf
        <input  class="form-control" type="text" name="management_note" maxlength="500"
                placeholder="Reason for rejecting" required>
        <button type="submit" class="btn btn-outline mt-1">Reject</button>
    </form>
@elseif ($viewing->isApproved())
    <form method="POST" action="{{ route('management.viewings.complete', $viewing) }}"
          onsubmit="return confirm('Mark this visit as completed? This lets the client pay the deposit.');">
        @csrf
        <button type="submit" class="btn">Mark completed</button>
    </form>
/**Done by Jewel */
    <form method="POST" action="{{ route('management.viewings.reschedule', $viewing) }}" class="mt-2">
        @csrf
        <input class="form-control" type="datetime-local" name="scheduled_at"
               value="{{ $viewing->scheduled_at->format('Y-m-d\TH:i') }}"
               min="{{ now()->format('Y-m-d\TH:i') }}" required>
        <button type="submit" class="btn btn-outline mt-1">Move slot</button>
    </form>
@elseif ($viewing->isRescheduleRequested())
    <form method="POST" action="{{ route('management.viewings.approve', $viewing) }}">
        @csrf
        <input type="hidden" name="scheduled_at" value="{{ $viewing->proposed_at?->format('Y-m-d\TH:i') }}">
        <button type="submit" class="btn" @disabled(! $viewing->proposed_at)>Accept new time</button>
    </form>

    <form method="POST" action="{{ route('management.viewings.reschedule', $viewing) }}" class="mt-2">
        @csrf
        <input class="form-control" type="datetime-local" name="scheduled_at"
               value="{{ $viewing->proposed_at?->format('Y-m-d\TH:i') ?? $viewing->scheduled_at->format('Y-m-d\TH:i') }}"
               min="{{ now()->format('Y-m-d\TH:i') }}" required>
        <button type="submit" class="btn btn-outline mt-1">Assign another slot</button>
    </form>

    <form method="POST" action="{{ route('management.viewings.reject', $viewing) }}" class="mt-2">
        @csrf
        <input class="form-control" type="text" name="management_note" maxlength="500"
               placeholder="Reason for rejecting" required>
        <button type="submit" class="btn btn-outline mt-1">Reject</button>
    </form>
@else
    <span class="text-muted">No action needed</span>
@endif
