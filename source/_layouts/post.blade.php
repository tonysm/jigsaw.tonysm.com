@extends('_layouts.main')

@section('body')
    <a href="/" class="back md:shadow-lg bg-white z-10 my-4 text-sm no-underline inline-block mx-auto text-center border border-gray-700 rounded-full px-4 py-2 md:fixed md:top-0 md:left-5">
        ᐊ back to home
    </a>
    <div class="max-w-6xl mx-auto content space-y-10 py-10 md:py-24">
        <h1 class="text-5xl md:text-6xl leading-snug text-center font-bold">{{ $page->title }}</h1>

        <div class="flex justify-center items-center space-x-2 text-base md:text-lg font-mono uppercase text-gray-500 mt-4 font-semibold">
            <p>{{ $page->author }}</p>
            <span class="w-1 h-1 bg-gray-700 rounded-full"></span>
            <time datetime="{{ $page->date }}">{{ date('F j, Y', $page->date) }}</time>
        </div>

        <div class="max-w-2xl mx-auto space-y-4 prose prose-lg">
            @yield('content')
        </div>
    </div>
@endsection