<style>
    article {
        max-width: 70%;

        header {

            h1 {
                margin-block: 0.5rem;
            }
        }

        footer {
            ul {
                li {
                    margin-bottom: 0;
                }
            }
        }

    }
</style>

<article>
    <header>
        <h1>Create your account</h1>
        <small>and get your api key</small>
    </header>
    <form action="/signup" method="post">

        <label for="username">
            Username:
            <input aria-invalid="<?= (array_key_exists("username", $errors))  ? 'true' : 'false' ?>" type="text" name="username" id="username" placeholder="Enter username.." value="<?= htmlspecialchars($data["username"]) ?>">
            <?php if (array_key_exists("username", $errors)): ?>
                <small>
                    <?php foreach ($errors["username"] as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </small>
            <?php endif; ?>
        </label>

        <label for="email">
            Email:
            <input aria-invalid="<?= array_key_exists("email", $errors) ? 'true' : 'false' ?>" type="email" name="email" id="email" placeholder="Enter email.." value="<?= htmlspecialchars($data["email"]) ?>">
            <?php if (array_key_exists("email", $errors)): ?>
                <small>
                    <?php foreach ($errors["email"] as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </small>
            <?php endif; ?>
        </label>

        <label for="password">
            Password:
            <input aria-invalid="<?= (array_key_exists("password", $errors)) ? 'true' : 'false' ?>" type="password" name="password" id="password" placeholder="Enter password..">
            <?php if (array_key_exists("password", $errors)): ?>
                <small>
                    <?php foreach ($errors["password"] as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </small>
            <?php endif; ?>
        </label>

        <label for="confirm_password">
            Confirm Password:
            <input aria-invalid="<?= (array_key_exists("confirm_password", $errors))  ? 'true' : 'false' ?>" type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password..">
            <?php if (array_key_exists("confirm_password", $errors)): ?>
                <small>
                    <?php foreach ($errors["confirm_password"] as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </small>
            <?php endif; ?>
        </label>

        <button type="submit" class="outline">
            Sign in
        </button>
    </form>
</article>