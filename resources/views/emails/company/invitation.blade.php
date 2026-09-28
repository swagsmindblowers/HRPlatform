@component('mail::message')
# You are invited to join {{ $employee->company->name }}.

An employee of {{ $employee->company->name }} would like you to join them on BetterOff HR, the software used to manage human resources.

If you do already have an account on BetterOff HR, you will be able to use your existing account.

@component('mail::button', ['url' => $employee->getPathInvitationLink()])
Join {{ $employee->company->name }} on BetterOff HR
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
