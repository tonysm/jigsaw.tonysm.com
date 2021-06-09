---
extends: _layouts.post
title: 'Multiprocess Containers with S6 Overlay'
date:   2019-07-17
tags: laravel docker pid process container s6overlay
section: content
excerpt: Containers are supposed to run a single process. Sometimes you need more than that. Here's how to use S6 Overlay to achieve that.
---

Note: I originally wrote this article as an introduction to S6 Overlay in the internal blog at [madewithlove](https://madewithlove.com/).

Containers really shine when your service has a single OS process (or have the main process that handles children processes). That process is the PID 1 of the container. This makes scaling containers a breeze. For instance, if you need more processing power, you can spin up more containers in your cluster. This way of building and running containers works fine with languages that are self-contained, like Go or Node, for instance, where you can spin up a single process that binds to a port and that's it.

But when it comes to PHP, at least the more traditional way of running PHP, it gets tricky. In the pre-container era, the most common way of running PHP applications was with Nginx+phpfpm. This works out really well, actually. But in the container era, it's tricky. To run PHP like that you need two processes: Nginx and fpm. They will talk with each other via Unix sockets (basically a shared file in the same filesystem).

And both processes are important for the container. You want your container terminating if either of these processes dies, so the orchestrator can detect it and spin up a new container to take over. They are equally important. How can you make it so they are both handled as PID 1 in the container?

Some folks just give up and go with apache2+modphp, which is actually fine, I guess (I have done that myself), but I would prefer to stick with Nginx and fpm. If you are like me, there is a way: process supervision.

## Process Supervision

A process supervisor is exactly what it sounds like: some process that the only job is to watch other processes. If they are running, or if they have stopped, things like that.

The most common supervisor might be [Supervisord](supervisord.org), and in most cases, it does the job really well. I've used it for running queue workers and schedulers, for instance. But it's not suited for running as the init process in the era of containers, it even states that in the first page of the [documentation](http://supervisord.org/#supervisor-a-process-control-system):

> It shares some of the same goals of programs like launchd, daemontools, and runit. Unlike some of these programs, it is not meant to be run as a substitute for init as “process id 1”. Instead it is meant to be used to control processes related to a project or a customer, and is meant to start like any other program at boot time.

That's ok, actually. We have other options, and some are even linked in Supervisord's documentation. One option that is not listed is called: [S6](https://github.com/just-containers/s6-overlay).

## S6 Overlay

S6 all the functionality required for running as the PID 1 in a container, essentially:

* Well, process supervision; and
* Forwarding signals (any signals the PID 1 receives, it will forward to the other processes, so they can terminate gracefully or re-read the configs);

There are other functionalities built-in in S6, but these are the main ones, as I see it. You can read more about S6 [here](https://skarnet.org/software/s6/index.html).

Let's see what it looks like to add S6 overlay to our Docker images.

### Example
I've created a repository [here](https://github.com/madewithlove/php-s6-overlay-demo) so I won't go over it step-by-step. The Dockerfile has some comments if you are curious. To run this app you first have to build the Docker image:

```bash
git clone git\@github.com:madewithlove/php-s6-overlay-demo.git
cd php-s6-overlay-demo/
docker build -t php-s6-demo-app:0.0.1 .
docker run --rm -p "8000:80" php-s6-demo-app:0.0.1
```

You should see an output like:

```bash
[s6-init] making user provided files available at /var/run/s6/etc...exited 0.
[s6-init] ensuring user provided files have correct perms...exited 0.
[fix-attrs.d] applying ownership & permissions fixes...
[fix-attrs.d] done.
[cont-init.d] executing container initialization scripts...
[cont-init.d] done.
[services.d] starting services
[services.d] done.
This account is currently not available.
[10-Jul-2019 17:55:48] NOTICE: fpm is running, pid 178
[10-Jul-2019 17:55:48] NOTICE: ready to handle connections
```

Nice! And if you open your browser, you should see the familiar phpinfo screen:

![phpinfo output](/assets/images/multiproc-containers/phpinfo-output.png)

This means everything is working! Great. Now, you can go ahead and kill the docker container by pressing Ctrl+d in the container terminal screen. You should see some output that S6 is sending the services the TERM signal and then the KILL (for the ones that did not handle the TERM):

```bash
^C[10-Jul-2019 18:04:39] NOTICE: Terminating ...
[10-Jul-2019 18:04:39] NOTICE: exiting, bye-bye!
[cont-finish.d] executing container finish scripts...
[cont-finish.d] done.
[s6-finish] waiting for services.
[s6-finish] sending all processes the TERM signal.
[s6-finish] sending all processes the KILL signal and exiting.
```

Great! That was it.

## Conclusion

Some people might argue that containers are meant to be single-process services, and maybe they are right by the book, but I do think it's fine if you need multiple processes in a single container in this case. As always: it depends.

You can split your application in two containers one for the Nginx and another for the fpm processing and make them talk via TCP sockets, but that gets weird, you have to add a copy of your assets (usually the public/ folder to your Nginx image, and you also need that in the fpm container). It would be just easier to spin this application as a single container.

There are no silver bullets, only trade-offs. And in this case, they are worth it, IMO.

Cheers.

_P.S: Maybe worth saying that the S6 Overlay init script (located at /init after you add the S6 Overlay files to your image), must be the [ENTRYPOINT](https://serversforhackers.com/c/div-entrypoint-vs-cmd) of your container, this way you can override the default command and S6 will still apply process supervision to your command. This is very useful when running workers, you can re-use the same container image and change the command to something like php artisan horizon and S6 will apply the process supervision on this process as well._
