<x-mail::message>
# Use this token to verify your account.

{{$token}}

<x-mail::button :url="''">
Hmmmm....
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
