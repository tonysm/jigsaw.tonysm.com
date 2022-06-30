@extends('_layouts.main')

@section('head')
    @include('_partials.social', [
        'title' => $page->title,
        'description' => $page->excerpt,
    ])
@endsection

@section('body')
    <a href="/" class="z-10 inline-block px-4 py-2 mx-auto my-4 text-sm text-center no-underline bg-white border border-gray-700 rounded-full button-link back md:shadow-lg md:fixed md:top-0 md:left-5">
        ᐊ back to home
    </a>
    <div class="pt-10 md:pt-36 prose sm:prose-xl mx-auto">
        <h1 class="text-center">{{ $page->title }}</h1>

        <div class="flex items-center justify-center mt-4 space-x-2 font-mono text-base font-semibold text-gray-500 uppercase md:text-lg">
            <p>{{ $page->author }}</p>
            <span class="w-1 h-1 bg-gray-700 rounded-full"></span>
            <time datetime="{{ $page->date }}">{{ date('F j, Y', $page->date) }}</time>
        </div>
    </div>

    <div class="mx-auto space-y-4 prose sm:prose-xl">
        @yield('content')
    </div>
@endsection
