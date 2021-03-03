---
extends: _layouts.post
title: 'Queues and Async Apps (Video)'
date:   2019-01-18
tags: laravel queues background-jobs
section: content
---

Right after I posted the [video](https://www.youtube.com/watch?v=GtphrhnFwZQ) where I introduce the [Laravel WebSockets Package](https://beyondco.de/docs/laravel-websockets/getting-started/introduction), I got a request to maybe talk more about a preview of an old talk I had on my YouTube channel. So I decided to record it and share it.

In this talk, I walk-through a problem of a server provisioning application, where we need to deal with long-running operations (like install dependencies in a server), and how to approach that using Queues and Workers. Then we jump in to enrich the UI with some real-time feedback using WebSockets.

<div class="embed-responsive">
  <iframe src="https://www.youtube.com/embed/mhmkap7jdu8" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
</div>