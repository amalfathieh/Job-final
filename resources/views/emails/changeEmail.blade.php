@component('mail::message')
    <h1>You want to change your email? </h1>
    <ul>
        <li>Previous email: {{$pre_email}}</li>
        <li>New email: {{$new_email}}</li>
    </ul>
    <p>Your code is: {{ $code }}</p>
    <p>If you don't, don't matter and skip this mail</p>
    <p>The allowed duration of the code is one hour from the time the message was sent</p>
@endcomponent
