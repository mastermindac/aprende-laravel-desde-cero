@component('mail::message')
# New contact was shared with you

User {{ $fromUser }} shared contact {{ $sharedContact }} with you.

@component('mail::button', ['url' => route('contact-shares.index')])
See Here
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
