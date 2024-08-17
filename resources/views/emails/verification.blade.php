@component('mail::message')
    <h1>We have received your request to verificaction your email</h1>
    <p>Your verification code is: {{ $code }}</p>
    <p>The allowed duration of the code is one hour from the time the message was sent</p>
@endcomponent
