---
extends: _layouts.post
title: 'On Serverless and Monoliths'
date:   2020-02-12
tags: laravel vapor serverless monoliths
section: content
excerpt: Some folks see Severless as the end of the Monolith. Quite the contrary. Here's how Laravel Vapor makes it easy to breakdown a Laravel application in 3 functions (Web, Queue, and CLI) to deploy to AWS Lambda.
---

I've just read the article "[The Serverless Supremacy: The fall of the Monolith](https://blog.webiny.com/the-serverless-supremacy-204fbf5add75?gi=c634008b1d6d)" and wanted to share my thoughts here real quick.

I feel like it's missing the point. It's again saying that monoliths are a "thing of the past", which is just silly.

I don't think of Serverless as the opposite of the Monolith. Nor as a "next step after Microservices". Quite the contrary, Serverless is a great environment to deploy your monolith.

Monoliths consists of 3 layers (entry-points): web, worker, and scheduler (clock). They need a connecting message broker, usually Redis or SQS to connect these pieces, and a database.

If you think of Serverless as a deployment target, your deployment pipeline should generate 3 functions: web, cli, and queue.

All these share the same runtime, but they have different "roles". The web function will invoked whenever a new HTTP event happens on your Serverless environment. It might send background jobs to SQS, which will cause another event on your Serverless enviroment and invoke the queue function to handle it.

You still develop it locally as a monolith, but your deployment target is Serverless.

That's the approach used for services such as [Laravel Vapor](https://vapor.laravel.com/), for instance.

> Due to serverless, they can use different services such as Algolia, Stripe, Lambda and others to get that power and those features and integrations.

This is misleading. You can also be "serviceful" and leverage most of these 3rd-party services to reduce the same operational costs as a monolith application. No need to wait for a "microservice" to use something like Algolia or Stripe.

**Update (15/02/2020)**: [Mohamed Said](https://twitter.com/themsaid/) from Laravel has posted a talk explaining [Laravel Vapor](https://vapor.laravel.com/) a bit more. Check it out [here](https://www.youtube.com/watch?v=vHw9tSSRJGw).
