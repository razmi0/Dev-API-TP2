<!-- * @property string $id
 * @property string $username
 * @property string $email
 * @property string $password_hash
 * @property string $api_key_hash
 * @property string $created_at
 * @property string $updated_at -->
<article>
    <header>
        <h1>Profile</h1>
        <small>Your data is our concern</small>
    </header>

    <section>
        <h2>Account</h2>
        <ul>
            <li>
                <strong>Username:</strong>
                <span><?= htmlspecialchars($user->getUsername()) ?></span>
            </li>
            <li>
                <strong>Email:</strong>
                <span><?= htmlspecialchars($user->getEmail()) ?></span>
            </li>
            <li>
                <strong>API Key:</strong>
                <span><?= htmlspecialchars($user->getApiKeyHash()) ?></span>
            </li>
        </ul>
    </section>

    <section>
        <h2>Actions</h2>
        <ul>
            <li>
                <a href="/profile/edit">Edit</a>
            </li>
            <li>
                <a href="/profile/delete">Delete</a>
            </li>
        </ul>
    </section>

    <section>
        <h2>History</h2>
        <ul>
            <li>
                Account created at : <strong><?= htmlspecialchars($user->getCreatedAt()) ?></strong>
            </li>
            <li>
                Last updated at : <strong><?= htmlspecialchars($user->getUpdatedAt()) ?></strong>
            </li>
        </ul>
    </section>




</article>