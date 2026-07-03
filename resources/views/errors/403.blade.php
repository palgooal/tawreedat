@extends('errors.minimal', ['title' => 'غير مصرح بالوصول | توريدات'])

@section('content')
    <x-error-page code="403" heading="غير مصرح لك بالوصول"
        description="ليس لديك الصلاحية الكافية لعرض هذه الصفحة. إذا كنت تعتقد أن هذا خطأ، يرجى التواصل مع فريق توريدات.">
        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
            <path d="M12 3.5 5 6.2v5.4c0 4.6 3 8.1 7 9.4 4-1.3 7-4.8 7-9.4V6.2L12 3.5Z" />
            <circle cx="12" cy="11.5" r="3.2" />
            <path d="m9.8 9.3 4.4 4.4" />
        </svg>
    </x-error-page>
@endsection
