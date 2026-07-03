@extends('errors.minimal', ['title' => 'حدث خطأ غير متوقع | توريدات'])

@section('content')
    <x-error-page code="500" heading="حدث خطأ غير متوقع"
        description="نواجه مشكلة تقنية من جانبنا، وفريقنا يعمل على حلها. يرجى المحاولة مرة أخرى بعد قليل.">
        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
            <path d="M12 4 21 19.5H3L12 4Z" />
            <path d="M12 10v4.2" />
            <path d="M12 17.2h.01" />
        </svg>
    </x-error-page>
@endsection
