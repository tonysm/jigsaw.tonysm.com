---
extends: _layouts.post
title: 'On using Frameworks'
date:   2020-01-05
tags: laravel frameworks
section: content
---

I'm a huge fan of frameworks. Particularly the Full-Stack ones. My framework of choice (and the one I use daily) is [Laravel](https://laravel.com/). It has a really powerful ecosystem for writing modern applications in PHP.

Before I found Laravel, I had been using other frameworks and even had to maintain a "pure" PHP application that I inherited for a while, and if you only take one idea out of this article: use a framework.

Some people don't like frameworks. I don't know if that's because they think they can do better than a group of very experienced folks working collaboratively on something or if it's something psychological.

Others think that just by "using" a framework it makes their application as robust as the framework itself. Even better if the framework is fully broken in components or, better yet, if they are "micro".

Some even use frameworks as some kind of "competitive advantage" to sell their work "we use this framework because it gives us all the structure we need to write robust web applications with high code quality". Well, I'm not convinced this is true.

See, it's not that you use a framework that matters, it's how you use it.

When it comes to software, I like to think that there are many solutions to the same problems. And most of the time the possibilities are endless (or so it seems).

Frameworks provide the building blocks you can use to write your own stories in the form of software. And, given that there are many different solutions to the same problem, you can pick the ones that better fit the building blocks you are given by sticking to the frameworks' conventions.

It's not like the framework "limits" you. Quite the contrary: it empowers you. Not just you, your team too. Especially the small teams (but also big teams!).

To build a modern application these days, you need a lot of powerful building blocks. We are not talking about sending e-mails, submitting forms, and things like that. Think highly interactive applications: background jobs, notifications, Web Sockets, etc.

And if you are going to build something like that today on your own without a framework, well, good luck. It's not impossible, it's just that you would be much better off by using a framework.

Need to send notifications and they can be via SMS, E-Mail, or Dekstop Notifications? Sure, no problem, the framework already has the abstractions for that built-in. "This report is really slow, and sometimes it times-out" what if you make it async? Dispatch a job, generate it in the background, create a temporary link, and send it back to the customer so they can download it. Easy when you already have all the building blocks available.

Don't overthink it either. Try as much as you can to write the simplest code possible. Something that is easy to delete. Don't put layers and layers of abstractions just because someone told you to. Think. Does this make your code easy to read/understand? If you want to remove this feature entirely next week, how hard would it be?

This is definitely one of the things that fit the category of "easier said than done". Simplicity is hard to achieve.

Next time you are starting a project, use a framework. Take a look at Laravel, for instance. You might find it gives you most of the things you need (and even some that you don't even know you need yet, but will be very handy very soon).

And, as the Laravel CLI application generator says: Build something amazing.