@php
    $initial = [];
    $flashMap = [
        'success' => 'success',
        'error' => 'error',
        'warning' => 'warning',
        'info' => 'info',
        'status' => 'info',
    ];

    foreach ($flashMap as $sessionKey => $type) {
        $message = session($sessionKey);
        if (! is_string($message) || trim($message) === '') {
            continue;
        }
        $message = trim($message);
        $already = collect($initial)->contains(fn ($toast) => $toast['message'] === $message);
        if (! $already) {
            $initial[] = ['type' => $type, 'message' => $message];
        }
    }

    if (isset($errors) && $errors->any()) {
        foreach ($errors->all() as $message) {
            if (! is_string($message) || trim($message) === '') {
                continue;
            }
            $message = trim($message);
            $already = collect($initial)->contains(fn ($toast) => $toast['message'] === $message);
            if (! $already) {
                $initial[] = ['type' => 'error', 'message' => $message];
            }
        }
    }

    $initial = array_slice($initial, 0, 6);
    $config = ['duration' => 3000, 'initial' => $initial];
@endphp

<div
    x-data="toastStack"
    data-alpine-config="{{ json_encode($config, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) }}"
    class="fs-toast-host pointer-events-none fixed inset-x-0 bottom-0 z-[200] flex justify-end p-4 sm:p-5 md:p-6"
    aria-live="polite"
    aria-relevant="additions"
>
    <div class="pointer-events-none flex w-full max-w-[min(100%,22rem)] sm:max-w-sm flex-col gap-3">
        <template x-for="toast in items" :key="toast.id">
            <div
                x-show="toast.visible"
                x-cloak
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 sm:translate-y-1 sm:translate-x-2"
                x-transition:enter-end="opacity-100 translate-y-0 sm:translate-x-0"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-y-0 sm:translate-x-0"
                x-transition:leave-end="opacity-0 translate-y-1 sm:translate-x-2"
                class="pointer-events-auto"
                :class="toneClass(toast.type)"
                :role="roleFor(toast.type)"
            >
                <div class="fs-toast__body" x-text="toast.message"></div>
                <button
                    type="button"
                    class="fs-toast__dismiss"
                    :data-toast-id="String(toast.id)"
                    @click="onDismissClick($event)"
                    aria-label="Dismiss notification"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>
</div>
