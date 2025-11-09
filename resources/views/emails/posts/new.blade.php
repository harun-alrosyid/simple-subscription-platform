@component('mail::message')
# {{ $post->title }}

{{ $post->description }}

@component('mail::button', ['url' => $post->website->url])
View on Website
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent