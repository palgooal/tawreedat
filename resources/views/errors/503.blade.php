@extends('errors.minimal', ['title' => 'المنصة تحت الصيانة | توريد'])

@section('content')
    <x-error-page code="503" heading="المنصة تحت الصيانة حالياً"
        description="نعمل على تحسين منصة توريد وسنعود قريباً. نعتذر عن أي إزعاج.">
        <svg class="h-9 w-9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7">
            <path
                d="M14.7 6.3a3.5 3.5 0 0 1-4.6 4.6L5 16l2.9 2.9 5.1-5.1a3.5 3.5 0 0 1 4.6-4.6l-2.4 2.4-2-2 2.4-2.4Z" />
        </svg>
    </x-error-page>
@endsection
