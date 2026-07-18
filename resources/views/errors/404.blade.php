@extends('errors.minimal', ['title' => 'الصفحة غير موجودة | توريد'])

@section('content')
    <x-error-page code="404" heading="الصفحة غير موجودة"
        description="الرابط الذي تحاول الوصول إليه غير متوفر، ربما تم نقله أو حذفه. يمكنك العودة للرئيسية أو تصفح آخر الأخبار.">
        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
            <path d="M9.5 3.5h5l4 4v11a1.5 1.5 0 0 1-1.5 1.5h-2" />
            <path d="M14.5 3.5V7a1 1 0 0 0 1 1h3.5" />
            <path d="M9.5 3.5h-4A1.5 1.5 0 0 0 4 5v14a1.5 1.5 0 0 0 1.5 1.5h6" />
            <circle cx="10" cy="15" r="3" />
            <path d="m13.2 18.2 2.3 2.3" />
        </svg>
    </x-error-page>
@endsection
