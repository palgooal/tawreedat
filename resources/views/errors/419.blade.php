@extends('errors.minimal', ['title' => 'انتهت صلاحية الصفحة | توريد'])

@section('content')
    <x-error-page code="419" heading="انتهت صلاحية الجلسة"
        description="انتهت صلاحية هذه الصفحة، غالباً بسبب مرور وقت طويل قبل الإرسال. يرجى العودة وإعادة تعبئة النموذج مرة أخرى.">
        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
            <path d="M4 5v4h4" />
            <path d="M4.6 13.5A8 8 0 1 0 6.3 7" />
            <path d="M12 8.5v4l3 2" />
        </svg>
    </x-error-page>
@endsection
