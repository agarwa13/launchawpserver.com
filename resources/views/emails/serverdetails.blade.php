@component('mail::message')
# Server Provisioned

A server, perfectly setup to run your WordPress site is ready to use.

This email contains all the security details related to your site.

More than likely, you do not need this information.
However, we recommend that you keep this information safely.

Without this information, any developer you hire may not be able to help you.
We do not retain this information on your behalf, therefore, if you were to lose it, you are losing access to your server. Therefore, we cannot stress this enough, that you should keep this information securely.

MySQL Database Username: {{ $launcher->database_username }}
MySQL Database Password: {{ $launcher->database_password }}

SSH via Password is disabled for Security of your Server.
Therefore, You will need to use a Key to SSH into the server.
You may add your computer's key through Launch a WP Server.

@component('mail::button', ['url' => '#'])
Add Websites to Your Server
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent