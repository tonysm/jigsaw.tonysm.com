@extends('_layouts.main')

@section('body')
    <a href="/" class="my-4 text-sm no-underline inline-block md:fixed top-0 left-5">
        ᐊ back to home
    </a>
    <div class="max-w-6xl mx-auto content space-y-10 pt-10 md:pt-36">
        <h1 class="text-5xl md:text-6xl leading-snug text-center font-bold">{{ $page->title }}</h1>

        <div class="flex justify-center items-center space-x-2 text-base md:text-lg font-mono text-gray-700 mt-4 font-semibold">
            <p>{{ $page->author }}</p>
            <span class="w-1 h-1 bg-gray-700 rounded-full"></span>
            <time datetime="{{ $page->date }}">{{ date('F j, Y', $page->date) }}</time>
        </div>

        <div class="max-w-2xl mx-auto space-y-4 py-10 prose">
            @yield('content')
        </div>
    </div>
@endsection