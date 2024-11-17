<h1>Welcome <?= isset($user) ? $user->getUsername() : ''; ?></h1>
<section>
    <h2>What's next?</h2>
    <p>
        You've successfully signed up. Please <a href="/login">login</a> to continue.
    </p>
</section>
<section>
    <h2>Why sign up?</h2>
    <p>
        Signing up allows you to access your profile, grab your api key and more.
        To know more about the application please go to <a href="/">home</a> page.
    </p>
</section>