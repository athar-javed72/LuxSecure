@props(['faqs' => []])

@php $faqs = collect($faqs); @endphp

@if($faqs->isNotEmpty())
    <div {{ $attributes->class(['grid md:grid-cols-2 gap-4 md:gap-6']) }}>
        @foreach($faqs as $faq)
            <x-faq-item :question="$faq->question" :answer="$faq->answer" />
        @endforeach
    </div>
@endif
