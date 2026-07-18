@extends('errors.minimal', ['title' => 'طلبات كثيرة جداً | توريد'])

@section('content')
    <x-error-page code="429" heading="طلبات كثيرة جداً"
        description="تم إرسال عدد كبير من الطلبات خلال فترة قصيرة. يرجى الانتظار قليلاً ثم المحاولة مرة أخرى.">
        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
            <path d="M7 3.5h10M7 20.5h10" />
            <path d="M8 3.5v3.2c0 1.6.9 3 2.3 3.7L12 11l1.7-.6a4.1 4.1 0 0 0 2.3-3.7V3.5" />
            <path d="M8 20.5v-3.2c0-1.6.9-3 2.3-3.7L12 13l1.7.6a4.1 4.1 0 0 1 2.3 3.7v3.2" />
        </svg>
    </x-error-page>
@endsection
