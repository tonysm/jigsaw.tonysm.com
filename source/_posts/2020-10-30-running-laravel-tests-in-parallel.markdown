---
extends: _layouts.post
title: "Running Laravel Feature Tests in Parallel with Paratest"
date:   2020-10-30
tags: laravel testing
section: content
excerpt: Here's how to use Paratest to run Tests in Parallel with Laravel.
---

<blockquote class="twitter-tweet"><p lang="en" dir="ltr">Pretty neat to see the Rails parallel test runner peg all cores and hyperthreads on my 8-core iMac. 10,000 assertions across 2,000 tests completing in 1 minute, 29 seconds. No fancy magic! All hitting the Dockerized DB. (This is for a 0.8 test ratio on an app that&#39;s 25KLOC.) <a href="https://t.co/59xCf1lMp6">pic.twitter.com/59xCf1lMp6</a></p>&mdash; DHH (@dhh) <a href="https://twitter.com/dhh/status/1321829617867653126?ref_src=twsrc%5Etfw">October 29, 2020</a></blockquote> <script async src="https://platform.twitter.com/widgets.js" charset="utf-8"></script>

Ever since I saw that Rails 6 was shipping with a parallel test runner I got curious if we couldn't have something like this for Laravel and PHPUnit.

I knew there was [Paratest](https://github.com/paratestphp/paratest), which allows running your PHPUnit tests in parallel without much trouble. By default, it separates test classes in groups and run each group in a different process.

That was awesome, but I faced an issue. My feature tests hit the database and, since each process will try to migrate the database, instead of getting a speed boost, I got a lot of errors.

So I started tinkering with a package to make this experience easier. After exploring Rails itself, I noticed that each process creates its own database, which makes sense.

At this point I had two options:

1. I could swap the RefreshDatabase trait for the DatabaseTransaction one and manage the test database migration myself (probably would be the easiest route); or
2. I coul find a way to programatically create one database for each test process.

I decided to follow the second route, because I'd like to avoid having to remember to run the migrations before running the tests every time I pulled some changes. This turned out to be possible. Paratest, by default, creates an environment variable called `TEST_TOKEN` and each process gets assigned a unique one (unique for the test run).

So I implemented some artisan commands, such as the `db:create` one, and also a custom test runner that would create the database before the process runs the test. Essentially, this ends up mimicking the same behavior from Rails: each process creates its own database, which is migrated once per process and each test runs in a transaction, which is rolled back after each test.

Here's the project on [GitHub](https://github.com/tonysm/laravel-paratest), I've recently upgraded it to Laravel 8. It's already available on [Packagist](https://packagist.org/packages/tonysm/laravel-paratest), so you can already pull it locally and try to use it yourself.

![Laravel and Paratest](/assets/images/paratest/laravel-paratest.png)

To be honest, I don't *need* such feature because my tests tend to run quite fast. But something like this might be handy on bigger projects or in a CI environment.
