@php
    $toast = session('toast');
    $type = is_array($toast) ? ($toast['type'] ?? null) : null;
    $message = is_array($toast) ? ($toast['message'] ?? null) : null;
    $role = $type === 'error' ? 'alert' : 'status';
    $live = $type === 'error' ? 'assertive' : 'polite';
@endphp

@if ($type && $message)
    <div class="toast-stack" data-toast-stack aria-live="{{ $live }}" aria-atomic="true">
        <div class="toast toast--{{ $type }}" role="{{ $role }}" data-timeout="3500">
            <div class="toast__content">{{ $message }}</div>
            <button class="toast__close" type="button" data-toast-close aria-label="Fermer">
                &times;
            </button>
        </div>
    </div>
@endif
