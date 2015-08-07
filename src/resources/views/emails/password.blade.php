<style>
    @font-face {
        font-family: 'Garamond';
        font-style: normal;
        font-weight: 400;
        src: url('{{ url("font/EBGaramond.otf") }}') format('opentype');
    }
</style>

<div style="padding: 30px; background-image: url('{{ url('img/classic-background.jpg')}}'); color: #fff; font-family: 'Garamond', Helvetica, Arial, sans-serif; font-size: 20px;">

    <h2>
        {{ trans('passwords.resetemail.body.header') }}
    </h2>
    
    <p>
        {{ trans('passwords.resetemail.body.salutation') }}
    </p>
    
    {{ trans('passwords.resetemail.body.requested') }}
    <a style="color: #fff" href="{{ route('passwordreset.confirm', $token) }}">
        {{ trans('passwords.resetemail.body.link.click') }}
    </a>
    <br>
    {{ trans('passwords.resetemail.body.ignore') }}
    <br>
    {{ trans('passwords.resetemail.body.support') }}: <a style="color:#fff" href="mailto:support@risk.net">support@risk.net</a>.
    <br>
    <br>
    {{ trans('passwords.resetemail.body.havefun') }}
    
    <p>
        {{ trans('passwords.resetemail.body.goodbye') }}
        <br>
        {{ trans('passwords.resetemail.body.signature') }}
    </p>
</div>