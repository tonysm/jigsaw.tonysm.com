---
extends: _layouts.post
title: 'Globalid Laravel'
date:   2022-02-21
tags: globalid laravel
section: content
excerpt: Globalids are very useful in all sorts of situations where you want to use polymorphism. I'm using that in the Rich Text Laravel package, for instance, to store references to models when you use them as attachments. Instead of serializing the model, we can store the URI to that model and use the Locator to find it for us when it's time to render the document again.
---

Polymorphism is a very known concept in programming. To put it simply: it's the idea that many things can play the same role in the system. For instance, think about the Pull Request Reviewer feature on GitHub. You can assign a single team member, multiple, or an entire team as the reviewer. You may have code that does something like this:

```php
class User extends Model
{
}

class Team extends Model
{
}

class Reviewer extends Model
{
  use SoftDeletes;

  public function reviewer()
  {
    return $this->morphTo();
  }

  public function setReviewerAttribute($reviewer)
  {
    $this->reviewer()->associate($reviewer);
  }
}

class PullRequest extends Model
{
  public function reviewers()
  {
    return $this->hasMany(Reviewer::class);
  }

  public function syncReviewers(Collection $reviewers): void
  {
    DB::transaction(function () use ($reviewers) {
      $this->reviewers()->delete();
      $this->reviewers()->saveMany($reviewers);
    });
  }
}
```

Then, in the `PullRequestReviewersController@update` action, you would have something like:

```php
class PullRequestReviewersController extends Controller
{
  public function store(PullRequest $pullRequest, Request $request)
  {
    $pullRequest->syncReviewers($this->reviewers($request));
  }

  private function reviewers(Request $request)
  {
    // Returns new Reviewers based on the request...
  }
}
```

The `PullRequestReviewersController::reviewers` method will return a Collection of `Reviewer` instances. Building those new model instances can be tricky. Think about the form that is needed for this. The bare-minimum version of it would consist of a select field where you would list all Teams and Users as options. You could even group them in [`optgroup` tags](https://developer.mozilla.org/pt-BR/docs/Web/HTML/Element/optgroup) and label them accordingly: 

```blade
<x-select name="reviewers[]" id="reviewers" multiple class="block mt-1 w-full">
  <option value="" disabled selected>Select the reviewers...</option>
  <optgroup label="Teams">
    @foreach ($teams as $team)
      <option value="{{ $team->id }}">{{ $team->name }}</option>
    @endforeach
  </optgroup>
  <optgroup label="Users">
    @foreach ($users as $user)
      <option value="{{ $user->id }}">{{ $user->name }}</option>
    @endforeach
  </optgroup>
</x-select>
```

Not so fast... teams and users may have colliding IDs. Both their Database tables have different sequences. Even if it didn't, let's say you're using UUIDs or something like that, how would you go about deciding which model the UUID belongs to when you're processing the request? All solutions I can think of would require some kind of ad-hoc differentiation between teams and users. Maybe you do something like `<table>:<id>`, so users options would render to `user:1`, `user:2`, etc., while teams options would render to something like `team:1`, `team:2`, etc.

Then, you'd have to encode that mapping logic to do the actual fetching. It's messy. There's a better way.

## Globalids

The [Globalid Laravel package](https://github.com/tonysm/globalid-laravel) solves this problem. This package is a port of a Rails gem called [globalid](https://github.com/rails/globalid). Instead of coming up with an ad-hoc solution that would probably be different every time we have a problem like this, we can solve it this way:

```blade
<x-select name="reviewers[]" id="reviewers" multiple class="block mt-1 w-full">
  <option value="" disabled selected>Select the reviewers...</option>
  <optgroup label="Teams">
    @foreach ($teams as $team)
      <option value="{{ $team->toGid()->toString() }}">{{ $team->name }}</option>
    @endforeach
  </optgroup>
  <optgroup label="Users">
    @foreach ($users as $user)
      <option value="{{ $user->toGid()->toString() }}">{{ $user->name }}</option>
    @endforeach
  </optgroup>
</x-select>
```

You would need to add the [`HasGlobalIdentification`](https://github.com/tonysm/globalid-laravel/blob/main/src/Models/HasGlobalIdentification.php) trait to both the Group and User models:

```php
use Tonysm\Globalid\Models\HasGlobalIdentification;

class User extends Model
{
  use HasGlobalIdentification;
}

class Team extends Model
{
  use HasGlobalIdentification;
}
```

The options' value fields would look something like this:

```
gid://laravel/App%5CModels%5CTeam/1
gid://laravel/App%5CModels%5CUser/1
```

The `%5C` here is the backslash (`\`) encoded to be URL-safe. This will work fine for a quick demo, but I'd highly recommend using something like `Relation::enforceMorphMap()` and avoiding using the model's FQCN for things like this. If you have a mapped morph, the package will use that. Something like this:

```php
Relation::enforceMorphMap([
  'team' => Models\Team::class,
  'user' => Models\User::class,
]);
```

And the options' values will then render like this:

```
gid://laravel/team/1
gid://laravel/user/1
```

Then, our backend can be simplified quite a lot, we can leverage the Globalids using the `Locator` Facade, like so:

```php
use Tonysm\GlobalId\Facades\Locator;

class PullRequestReviewersController extends Controller
{
  public function store(PullRequest $pullRequest, Request $request)
  {
    $pullRequest->syncReviewers($this->reviewers($request));
  }

  private function reviewers(Request $request)
  {
    return Locator::locateMany(Arr::wrap($request->input('reviewers')))
      ->map(fn ($reviewer) => Reviewer::make([
        'reviewer' => $reviewer,
      ]);
  }
}
```

That's nice, isn't it? The `Locator::locateMany` accepts a list of Globalids and will return its equivalent models. It's smart enough to only do a single query per model type to avoid unnecessary hops to the database and all. In this case, we used the `Locator::locateMany` but if we were only dealing with a single option, we could stick to the `Locator::locate` method, which would take a global ID and return the model instance based on that.

In our case, since we're only dealing with form payloads we could use the globalid path like that, but that's not really safe to use it as a route param, for instance. Instead of encoding the globalid to string, we could call the `->toParam()` method, which would return a base64 URL-safe version of the globalid that you can use as a route param. Something like this:

```
Z2lkOi8vbGFyYXZlbC9ncm91cC8x
```

This could be useful if you were passing that as a route param like:

```
POST /pull-requests/123/reviewers/Z2lkOi8vbGFyYXZlbC9ncm91cC8x
```

Preventing Tampering
Ok, all that is fine and all, but there's an issue with this implementation. It's not very secure. Users could tamper with the HTML form and start poking around with your payload. That's not cool. Would be cool if there was a way to prevent users from tampering with the globalids like that, right? Well, there is! It's called SignedGlobalids. The API is slightly the same, but instead of calling `->toGid()` on the model, you would call `->toSgid()`. Like following:

```blade
<x-select name="reviewers[]" id="reviewers" multiple class="block mt-1 w-full">
  <option value="" disabled selected>Select the reviewers...</option>
  <optgroup label="Teams">
    @foreach ($teams as $team)
      <option value="{{ $team->toSgid()->toString() }}">{{ $team->name }}</option>
    @endforeach
  </optgroup>
  <optgroup label="Users">
    @foreach ($users as $user)
      <option value="{{ $user->toSgid()->toString() }}">{{ $user->name }}</option>
    @endforeach
  </optgroup>
</x-select>
```

SignedGlobalids are cryptographically signed using a key derived from your app's `APP_KEY`, which means users cannot tamper with the form payload. Consuming this on your backend would then look like this:

```php
use Tonysm\GlobalId\Facades\Locator;

class PullRequestReviewersController extends Controller
{
  public function store(PullRequest $pullRequest, Request $request)
  {
    $pullRequest->syncReviewers($this->reviewers($request));
  }

  private function reviewers(Request $request)
  {
    return Locator::locateManySigned(Arr::wrap($request->input('reviewers')))
      ->map(fn ($reviewer) => Reviewer::make([
        'reviewer' => $reviewer,
      ]);
  }
}
```

The only difference is using `locateManySigned` instead of `locateMany`. Similarly, fetching a single resource would be `locateSigned` instead of the regular `locate`.

This can prevent users from tampering with the option values, but this does not prevent them from poking around in other places where you also use SignedGlobalids and find a signed option that they want to send in another form. If in your application you had another form that would also show them polymorphic options like that but to other models, for instance. They could then pick those options from the other form and use them on the one for reviewers. Since the options would be signed, your code would be tricked to accept it. That's not cool.

There are actually two ways you could go about it. When locating, you could tell the Locator that you're only interested in SignedGlobalids of the User model, for instance:

```php
private function reviewers(Request $request)
{
  return Locator::locateManySigned(Arr::wrap($request->input('reviewers')), [
     'only' => User::class,
   ])
    ->map(fn ($reviewer) => Reviewer::make([
      'reviewer' => $reviewer,
    ]);
}
```

That would only locate SignedGlobalids for the User model, ignoring every other non-User SignedGlobalId you may have. You can also define purposes for SignedGlobaids. This way, you can prevent users from reusing options just by copying and pasting the values from one form to a totally different one. For instance, our reviewers form could render the options passing the `for` option to the `toSgid()`:

```blade
<x-select name="reviewers[]" id="reviewers" multiple class="block mt-1 w-full">
  <option value="" disabled selected>Select the reviewers...</option>
  <optgroup label="Teams">
    @foreach ($teams as $team)
      <option value="{{ $team->toSgid(['for' => 'reviewers-form'])->toString() }}">{{ $team->name }}</option>
    @endforeach
  </optgroup>
  <optgroup label="Users">
    @foreach ($users as $user)
      <option value="{{ $user->toSgid(['for' => 'reviewers-form'])->toString() }}">{{ $user->name }}</option>
    @endforeach
  </optgroup>
</x-select>
```

Then, in our backend we would also have to specify the same purpose when locating the models, like this:

```php
private function reviewers(Request $request)
{
  return Locator::locateManySigned(Arr::wrap($request->input('reviewers')), [
     'for' => 'reviewers-form',
   ])
    ->map(fn ($reviewer) => Reviewer::make([
      'reviewer' => $reviewer,
    ]);
}
```

If the purpose encoded and signed on the SignedGlobalid doesn't match with the purpose you specify when locating, it wouldn't work.

Alternatively, you could also specify how long this SignedGlobalid will be valid, for instance, which could be useful if you're generating a public access link for some resource but you don't want it to be available forever, which helps preventing data from leaking out of your app in some cases. Read more about [SignedGlobalids here](https://github.com/tonysm/globalid-laravel#signed-global-ids).

Globalids are very useful in all sorts of situations where you want to use polymorphism. I'm using that in the [Rich Text Laravel package](https://github.com/tonysm/rich-text-laravel), for instance, to store references to models when you use them as attachments. Instead of serializing the model, we can store the URI to that model and use the Locator to find it for us when it's time to render the document again.