---
extends: _layouts.post
title: 'Rich Text Laravel Attachments With Media Library'
date:   2022-08-07
tags: rich-text-laravel richtext laravel trix
section: content
excerpt: In a previous introduction to the Rich Text Laravel package I hinted that I wanted to cover how to build a more advanced attachment handling using Spatie's Media Library package. This is the follow-up for that.
---

In a [previous introduction](https://www.tonysm.com/rich-text-laravel-introduction/) to the [Rich Text Laravel](https://github.com/tonysm/rich-text-laravel) package I hinted that I wanted to cover how to build a more advanced attachment handling using [Spatie's Media Library](https://github.com/spatie/laravel-medialibrary) package. This is the follow-up on that.

We'll pick it up from where the previous article ended to keep this one short. [Here's the GitHub repository](https://github.com/tonysm/rich-text-demo-app).

Our Trix editor component looks like this:

```blade
@props(['id', 'value', 'name', 'disabled' => false])

<input
    type="hidden"
    id="{{ $id }}_input"
    name="{{ $name }}"
    value="{{ $value?->toTrixHtml() }}"
/>
<trix-editor
    id="{{ $id }}"
    input="{{ $id }}_input"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => 'trix-content rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50']) }}
    x-data="{
        upload(event) {
            const data = new FormData();
            data.append('attachment', event.attachment.file);

            window.axios.post('/attachments', data, {
                onUploadProgress(progressEvent) {
                    event.attachment.setUploadProgress(
                        progressEvent.loaded / progressEvent.total * 100
                    );
                },
            }).then(({ data }) => {
                event.attachment.setAttributes({
                    url: data.image_url,
                });
            });
        }
    }"
    x-on:trix-attachment-add="upload"
></trix-editor>
```

This is listening to the `trix-attachment-add` event, which is fired by Trix when we attempt to upload a file, then we upload them to a `POST /attachments` endpoint using axios. From that endpoint's response, we get the `image_url` field and set that as an attribute in the Trix Attachment.

The route that handles the uploads looks like this:

```php
Route::post('/attachments', function () {
    request()->validate([
        'attachment' => ['required', 'file'],
    ]);

    $path = request()->file('attachment')->store('trix-attachments', 'public');

    return [
        'image_url' => Storage::disk('public')->url($path),
    ];
})->middleware(['auth'])->name('attachments.store');
```

We validate that the user is uploading a file and we then store it in a `trix-attachments` folder inside the `public` disk. Next, we get the URL to that file and return it back to the user as the `image_url` JSON field. Simple enough.

Now, let's add the Media Library package:

```bash
composer require spatie/laravel-medialibrary
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="migrations"
php artisan vendor:publish --provider="Spatie\MediaLibrary\MediaLibraryServiceProvider" --tag="config"
```

These steps should add the package, publish its database migrations and config file. Make sure you have the [required dependencies for Media Library's optimizations](https://spatie.be/docs/laravel-medialibrary/v10/installation-setup#content-setting-up-optimization-tools) installed.

The Media Library package ships with its own model called `Media`. There are a couple of requirements when using this model, like its expectation to have a model associated to them, which would be a problem for us since we want to allow attachments to be created before the resource itself (think you're creating a post and adding attachments to it). To simplify things, let's add our own `Attachment` model. Whenever we upload an attachment, we'll associate the Media model to a corresponding Attachment model. That `Attachment` model will have its association as nullable so we can create them before the resource that will reference it.

We can add our model like this:

```bash
php artisan make:model Attachment -mf
```

The `-m` flag will create a corresponding migration for us, and the `-f` flag creates a model factory.

Let's change the created migration to add the fields we want:

```php
Schema::create('attachments', function (Blueprint $table) {
    $table->id();
    $table->nullableMorphs('record');
    $table->string('caption')->nullable();
    $table->timestamps();
});
```

Run the migrations:

```bash
php artisan migrate
```

We're making a `record` polymorphic relationship because we could potentially have other resources receiving attachments to its rich text fields as well.

Now, let's update the `Attachment` model to configure it to receive attachments:

```php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class Attachment extends Model implements HasMedia
{
    use HasFactory;
    use InteractsWithMedia;

    protected $casts = [
        'verified_at' => 'datetime',
    ];

    protected $guarded = [];

    public function registerMediaConversions(Media $media = null): void
    {
        $this
            ->addMediaConversion('thumb')
            ->fit(Manipulations::FIT_CROP, 300, 300)
            ->nonQueued();
    }

    public function record()
    {
        return $this->morphTo();
    }
}
```

Now, let's create a new trait for the models that we want to associate attachments with. We'll call it `HasAttachments`:

```php
namespace App\Models;

trait HasAttachments
{
    public function syncAttachmentsMeta()
    {
        $this->content->attachments()
            ->filter(fn ($attachment) => $attachment->attachable instanceof Attachment)
            ->each(function ($attachment) {
                $attachment->attachable->update([
                    'record' => $this,
                    'caption' => $attachment->node->getAttribute('caption'),
                ]);
             });
    }

    public function attachments()
    {
        return $this->morphMany(Attachment::class, 'record');
    }
}
```

We added the `attachments` relationship to the trait, but also a `syncAttachmentsMeta` method. That method is supposed to be called after we save the model with attachments (whenever we change the content rich text field). It will scan the document looking for attachments of the model `Attachment` and update the model meta data syncing with the caption in the rich text document. Although we're only interested in the caption attribute for now, you can see how you could extract other metadata from the document itself.

This reminds me we need to make the `Attachment` model an *attachable* as well. Attachables, in the Rich Text Laravel package, are models that have a rich text representation inside the documents. Let's add the contract and trait to it, we'll also override some of its methods, I'll explain in a bit:

```php
namespace App\Models;

// Other use statements...
use Tonysm\RichTextLaravel\Attachables\Attachable;
use Tonysm\RichTextLaravel\Attachables\AttachableContract;

class Attachment extends Model implements HasMedia, AttachableContract
{
    // Other used traits...
    use Attachable;

    private $firstMediaCache;

    public function richTextPreviewable(): bool
    {
        return str_starts_with($this->getFirstMedia()->mime_type, 'image/');
    }

    public function richTextFilename(): ?string
    {
        return $this->firstMedia()->file_name;
    }

    public function richTextFilesize()
    {
        return $this->firstMedia()->size;
    }

    public function richTextContentType(): string
    {
        return $this->firstMedia()->mime_type;
    }

    public function richTextRender(array $options = []): string
    {
        return view('trix._attachment', [
            'attachment' => $this,
            'media' => $this->firstMedia(),
            'options' => $options,
        ])->render();
    }

    public function toTrixContent(): ?string
    {
        return null;
    }

    public function getPreviewableUrl(string $convertionName = null): string
    {
        return $this->firstMedia()->getFullUrl($convertionName);
    }

    public function firstMedia()
    {
        return $this->firstMediaCache ??= $this->getFirstMedia();
    }

    public function setRecordAttribute($record)
    {
        $this->record()->associate($record);
    }
}
```

Alright, let's go over each method:

- **richTextPreviewable**: returns a boolean that indicates whether the attachment has a preview image associated. In our case, we're checking if the associated media has a content-type starting with `image/`;
- **richTextFilename**: returns the file name. Again, we're delegating that to the associated media;
- **richTextFilesize**: returns the file size in bytes. Which we're delegating that to the associated media;
- **richTextContentType**: returns the file content type. Also delegated to the associated media;
- **richTextRender**: returns the rendered HTML to show this attachment to users (not what renders inside Trix, but the actual final version);
- **toTrixContent**: returns the rendered HTML to rendered the attachment inside the Trix editor (what we show inside Trix);

The `firstMedia`, `getPreviewableUrl`, `setRecordAttribute` are actually custom methods, not needed for the Attachable contract. We're using the `getPreviewableUrl` method inside the view, which we'll explore shortly. The `setRecordAttribute` mutator will be used when we create the attachment, which we'll also explore shortly. And the `firstMedia` method is a helper method that caches the media instance on the current attachment the first time it's used so we avoid doing another database query when the attachable methods are used.

One thing you may have noticed is that we're returning null in the `toTrixContent` method. That's because Trix already knows how to render file attachments based on the file type for images and files (see [here](https://github.com/basecamp/trix/blob/7940a9a3b7129f8190ef37e086809260d7ccfe32/src/trix/views/piece_view.coffee#L31-L34) and [here](https://github.com/basecamp/trix/blob/7940a9a3b7129f8190ef37e086809260d7ccfe32/src/trix/models/attachment.coffee#L4)), so we don't actually need a custom HTML representation here. However, we're adding a custom view for the Attachment model for the final render because we cannot use the same template as remote images use (the ones that ship with the package) since some of the APIs changed.

The `trix._attachment` Blade template should look something like this:

```blade
<figure class="attachment attachment--{{ $attachment->richTextPreviewable() ? 'preview' : 'file' }} attachment--{{ $media->extension }}">
    @if ($attachment->richTextPreviewable())
        <img src="{{ $attachment->getPreviewableUrl() }}" />
    @endif

    <figcaption class="attachment__caption">
        @if ($attachment->caption)
            {{ $attachment->caption }}
        @else
            <span class="attachment__name">{{ $media->filename }}</span>
            <span class="attachment__size">{{ $media->humanReadableSize }}</span>
        @endif
    </figcaption>
</figure>
```

That should be it. Now, let's change the upload endpoint to also create the Attachment model and associate the uploaded file as its media:

```php
Route::post('attachments', function () {
    request()->validate([
        'attachment' => ['required', 'file'],
    ]);

    /** @var Attachment */
    $attachment = Attachment::create([
        'record' => auth()->user(),
    ]);

    $media = $attachment->addMedia(request()->file('attachment'))
        ->toMediaCollection();

    return [
        'attachable_sgid' => $attachment->richTextSgid(),
        'image_url' => $media->getFullUrl(),
    ];
})->name('attachments.store');
```

Now, we're also returning a `attachable_sgid` field with the `image_url`. `SGID` is short for Signed Global IDs, which are essentually a string key that may represent any model (or object) in our application. You can think of it as a URL for your models. It's provided by the [Globalid Laravel](https://github.com/tonysm/globalid-laravel) package, which the Rich Text Laravel package uses under the hood. That should be added to the Trix attachment in the front-end. Our final version there should be something like this:

```blade
@props(['id', 'value', 'name', 'disabled' => false])

<input
    type="hidden"
    id="{{ $id }}_input"
    name="{{ $name }}"
    value="{{ $value?->toTrixHtml() }}"
/>
<trix-editor
    id="{{ $id }}"
    input="{{ $id }}_input"
    {{ $disabled ? 'disabled' : '' }}
    {{ $attributes->merge(['class' => 'trix-content rounded-md shadow-sm border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50']) }}
    x-data="{
        upload(event) {
            const data = new FormData();
            data.append('attachment', event.attachment.file);

            window.axios.post('/attachments', data, {
                onUploadProgress(progressEvent) {
                    event.attachment.setUploadProgress(
                        progressEvent.loaded / progressEvent.total * 100
                    );
                },
            }).then(({ data }) => {
                event.attachment.setAttributes({
                    sgid: data.attachable_sgid,
                    url: data.image_url,
                });
            });
        }
    }"
    x-on:trix-attachment-add="upload"
></trix-editor>
```

Now, let's add the `HasAttachments` trait to our `Post` model:

```php
class Post extends Model
{
    // Other traits...
    use HasAttachments;

    // Other methods...
}
```

In our `PostsController`, let's make sure we call the `syncAttachmentsMeta` whenever a Post is created/updated, should be something like this:

```php
class PostsController extends Controller
{
    // Other actions...

    public function store()
    {
        $post = auth()->user()->currentTeam->posts()->create(
            $this->postParams() + ['user_id' => auth()->id()]
        );

        $post->syncAttachmentsMeta();

        return redirect()->route('posts.show', $post);
    }

    public function update(Post $post)
    {
        $this->authorize('update', $post);

        tap($post)
            ->update($this->postParams())
            ->syncAttachmentsMeta();

        if (Request::wantsTurboStream() && ! Request::wasFromTurboNative()) {
            return Response::turboStream($post);
        }

        return redirect()->route('posts.show', $post);
    }
}
```

And with that, our app should be syncing our attachments from the rich text document to the Post model. What is nice about this that we can access our attachments from the Post model directly, without having to scan or even load the rich text document field, something like:

```php
// returns a list of attachments without
// having to go through the document...
$post->attachments
```

That's it!

I hope you enjoyed this more "advanced" tutorial into the package. I actually have this running on my [Turbo Demo App](https://github.com/tonysm/turbo-demo-app) repository, you can see the [Pull Request](https://github.com/tonysm/turbo-demo-app/pull/20) where I implemented this. It has a little bit more, the app there is using Stimulus instead of Alpine, but the idea is the same. And you can see the PR to the demo app from the previous article [here](https://github.com/tonysm/rich-text-demo-app/pull/5).

For the next post in this Rich Text Laravel series I'm planning on adding server-side rendered syntax highlighting for the Trix code snippets in this application using [Torchlight](https://torchlight.dev/).

See you soon!
