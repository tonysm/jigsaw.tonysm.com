@extends('_layouts.main')

@section('body')
    <a href="/" class="back md:shadow-lg bg-white z-10 my-4 text-sm no-underline inline-block mx-auto text-center border border-gray-700 rounded-full px-4 py-2 md:fixed md:top-0 md:left-5">
        ᐊ back to home
    </a>

    <div class="leading-relaxed">
        <div class="max-w-6xl mx-auto pt-10 md:pt-36">
            <h1 class="text-5xl md:text-6xl text-center font-bold mb-4">Kubernetes for Laravel Developers</h1>
        </div>
        <div class="max-w-2xl mx-auto content py-10 prose prose-lg">
            <p>In this video series, I’m going to show you how I wrap a Laravel application in containers to deploy it
                to a Kubernetes cluster.</p>

            <p>Kubernetes can feel cumbersome at times, but it’s no denying that it’s a really powerful environment for
                your applications.</p>

            <p>You might have heard the benefits of Containers and Kubernetes, but haven’t paid close attention to yet.
                Some people think containers are too hard so they are not worth the effort or that Kubernetes is for
                microservices. That’s not true.</p>

            <p>I’m not gonna lie. There’s quite a learning curve. But there are a lot of benefits too, like:</p>

            <ul>
                <li>
                    Independence of host/cloud provider;
                </li>
                <li>
                    Everything runs the same.
                </li>
            </ul>

            <h3>Independence of host/cloud provider</h3>

            <p>
                Although infrastructure migrations like this a quite hard to do, using a tool like Kubernetes makes the
                work a bit easier. You can even have a hybrid cloud infrastructure.
            </p>

            <h3>Everything runs the same</h3>

            <p>
                This is mostly applicable for applications that run different pieces of software. I’m not talking
                exclusively about microservices here. An application consists of a series of tools combined together to
                generate some value to either companies or customers. These tools can be microservices, but they can
                also be other backing services, such as load balancers, a WebSockets app your application uses, or other
                pieces of infrastructure like an E-mail service.
            </p>
            <p>
                By adopting containers, you make most things “the same”. So you don’t have to worry about different
                release tools, or different build strategies for your tooling. They all of a sudden can share most
                infrastructure and workflows.
            </p>
            <p>
                Anyways, I’ve had to deploy multiple applications to Kubernetes. Even built deployment pipelines using
                GitOps. So I think I have something to share with you. If you are into Containers or just want to know
                more about making the most out of Kubernetes for your Laravel applications, this series is for you.
            </p>

            <h3>Videos</h3>

            <h4 id="00-course-overview">00 Course overview and brief introduction to Containers</h4>

            <p>
                This is the first video of the series. I’m going to be talking about what we are going to see in this
                course showing some bits of the rest of the course.
            </p>

            <p><em>video soon…</em></p>

            <h4 id="01-creating-the-laravel-application-using-docker">01 Creating the Laravel application using
                Docker</h4>

            <p>In this video, we will create our example Laravel application, but I’m assuming you have nothing but
                Docker installed locally (no local PHP binaries).</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/f02rH-lRW24"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h4 id="02-local-development">02 Local development</h4>

            <p>Next, we are going to set up a development environment for our Laravel application using Docker
                Compose.</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/Fv4qj3K8wSk"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h4 id="03-optimizing-our-docker-image-for-production">03 Optimizing our Docker Image for Production</h4>

            <p>In this video, we are going to make some tweaks in our application’s Docker images for a better
                production tuning.</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/r_9PnDWPl6s"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h4 id="04-setting-up-a-production-environment-on-kubernetes">04 Setting up a production environment on
                Kubernetes</h4>

            <p>It’s time to talk about our production environment. Let’s discuss our options of Container Platforms and
                create a Kubernetes cluster on DigitalOcean.</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/HPf_i1a8xC0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h4 id="05-deploying-the-application">05 Deploying the Application</h4>

            <p>With our cluster ready, it’s time to create the manifest for our application and deploy it. We’ll
                visualize a rolling update!</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/4epidR7AZP4"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h4 id="06-configuration-management-and-secrets">06 Configuration management and Secrets</h4>

            <p>We ended up with an ugly (and not that secure) manifest files, filled with secrets in plain-text. Let’s
                make it a bit more safe and explore the concept of Secrets in Kubernetes.</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/wy6kXFFAum0"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h4 id="07-database-migrations">07 Database migrations</h4>

            <p>So far, we have been running migrations manually. That’s not optimal. Let’s automate that and discuss
                possible solutions.</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/exCMUEJt6DQ"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h4 id="08-cicd-docker-and-kubernetes">08 CI/CD, Docker, and Kubernetes</h4>

            <p>It’s time to automate everything! Let’s create a git repository for our infrastructure and change our
                application’s CI to automatically patch the Kubernetes manifests whenever a new image is built.</p>

            <div class="embed-responsive">
                <iframe src="https://www.youtube.com/embed/uLcAca-gDVI"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                        allowfullscreen="" frameborder="0"></iframe>
            </div>

            <h4 id="09-closing-thoughts">09 Closing thoughts</h4>

            <p>Time to wrap up. I’m going clean and tell what I like and what I don’t like about all setup, and where I
                would and wouldn’t use something like this.</p>

            <p><em>video soon…</em></p>
        </div>
    </div>
@endsection
