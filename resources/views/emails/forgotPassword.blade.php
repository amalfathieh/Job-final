@component('mail::message')
    <h1>We have received your request to reset your password</h1>
    <p>Your code is: {{ $code }}</p>
    <p>The allowed duration of the code is one hour from the time the message was sent</p>
@endcomponent
