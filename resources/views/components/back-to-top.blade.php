{{-- Fixed back-to-top: appears after scroll, progress ring, Forest/Gold estate aesthetic --}}
<div
    x-data="backToTop"
    class="fs-back-to-top pointer-events-none fixed z-[190] bottom-5 end-5 sm:bottom-6 sm:end-6"
>
    <button
        type="button"
        class="fs-back-to-top__btn pointer-events-auto"
        x-show="visible"
        x-cloak
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 translate-y-2 scale-90"
        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
        x-transition:leave-end="opacity-0 translate-y-1 scale-95"
        @click="goTop()"
        aria-label="Back to top"
    >
        <svg class="fs-back-to-top__ring" viewBox="0 0 44 44" aria-hidden="true">
            <circle class="fs-back-to-top__track" cx="22" cy="22" r="18" fill="none" />
            <circle
                class="fs-back-to-top__progress"
                cx="22"
                cy="22"
                r="18"
                fill="none"
                :style="ringStyle()"
            />
        </svg>
        <span class="fs-back-to-top__icon" aria-hidden="true">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 19V5"/>
                <path d="M5 12l7-7 7 7"/>
            </svg>
        </span>
    </button>
</div>
