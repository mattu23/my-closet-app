@component('mail::message')
# メールアドレスの確認

{{ $user->name }} 様

アカウントのメールアドレスを確認するために、以下のボタンをクリックしてください。

@component('mail::button', ['url' => $verificationUrl])
メールアドレスを確認
@endcomponent

このメールに心当たりがない場合は、無視していただいて構いません。

Thanks,<br>
{{ config('app.name') }}
@endcomponent 