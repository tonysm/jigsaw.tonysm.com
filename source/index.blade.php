@extends('_layouts.main')

@section('head')
    @include('_partials.social', [
        'title' => 'Tony Messias',
        'description' => "Hey, there. I'm a programer from Brazil. This is my personal website and blog.",
    ])
@endsection

@section('body')
    <div class="py-8">
        <div class="w-full max-w-5xl mx-auto text-gray-800">
            <section class="space-y-4">
                <h1 class="uppercase font-bold">Tony Messias</h1>
                <p class="">Programmer at <a class="text-indigo-600" href="https://tighten.com/">Tighten</a>.</p>
            </section>

            <hr class="w-1/5 mx-auto my-6" />

            <div class="md:flex md:divide-x">
                <div class="md:w-4/6 md:pr-4">
                    <div class="space-y-4">
                        <h2 class="uppercase">Writings</h2>
                        <p>My technical writings are hosted here, but I also have a <a
                                    href="https://world.hey.com/tonysm">Hey
                                World</a> newsletter. Subscribe there if you want to receive updates and new posts.</p>
                    </div>

                    <div class="mt-4">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($posts as $post)
                                <li>
                                    <a class="text-indigo-600" href="{{ $post->getUrl() }}">
                                        {{ $post->title }}
                                    </a>

                                    <time datetime="{{ $post->date }}"
                                          class="text-sm text-gray-500">{{ date('F j, Y', $post->date) }}</time>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <hr class="md:hidden w-3/6 mx-auto my-6" />

                <div class="md:w-2/6 md:pl-4 space-y-6">
                    <div class="space-y-4">
                        <h2 class="uppercase">Open-Source</h2>

                        <p>I have a couple of open-source packages you may be interested in checking out. I've been trying to bridge the Rails and Laravel worlds!</p>

                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($repositories as $repository)
                            <li><a class="text-indigo-500" href="{{ $repository->link }}">{{ $repository->name }}</a>: {{ $repository->description }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <hr class="w-3/6 mx-auto" />

                    <div class="space-y-4">
                        <h2 class="uppercase">Courses</h2>

                        <p>Sometimes I record a screencast about a topic I'm tinkering, a problem I've encountered, or a tool that I'm building or learning.</p>

                        <ul class="list-inside list-disc space-y-1">
                            @foreach ($courses as $course)
                            <li><a class="text-indigo-600" href="{{ $course->link }}">{{ $course->name }}:</a> {{ $course->description }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <hr class="w-1/5 mx-auto my-6" />

            <section>
                <p class="w-sm mx-auto text-center">
                    Want to receive updates on new content? Here's my <a href="/rss">RSS</a> feed. <br class="hidden sm:inline" />
                    My email address is <a href="mailto:tonysm@hey.com">tonysm@hey.com</a>
                </p>
            </section>
        </div>
    </div>
@endsection
