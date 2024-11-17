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
        <h1>Log into your account</h1>
        <small>and get your api key</small>
    </header>
    <form action="/login" method="post">
        <?php if (array_key_exists("global", $errors)): ?>
            <small class="pico-color-red-500">
                <div><?= htmlspecialchars($errors["global"]) ?></div>
            </small>
        <?php endif; ?>

        <label for="email">
            Email:
            <input aria-invalid="<?= (array_key_exists("email", $errors))  ? 'true' : 'false' ?>" type="email" name="email" id="email" placeholder="Enter email.." value="<?= htmlspecialchars($data["email"]) ?>">
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
            <input aria-invalid="<?= (array_key_exists("password", $errors))  ? 'true' : 'false' ?>" type="password" name="password" id="password" placeholder="Enter password..">
            <?php if (array_key_exists("password", $errors)): ?>
                <small>
                    <?php foreach ($errors["password"] as $error): ?>
                        <div><?= htmlspecialchars($error) ?></div>
                    <?php endforeach; ?>
                </small>
            <?php endif; ?>
        </label>

        <button type="submit" class="outline">
            Log in
        </button>
    </form>
</article>