<style>
    article {
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
        <h1>Sign in</h1>
    </header>
    <form action="/signup" method="post">

        <label for="username">
            Username:
            <input type="text" name="username" id="username" placeholder="Enter username.." value="<?= htmlspecialchars($data["username"]) ?>">
        </label>

        <label for="email">
            Email:
            <input type="email" name="email" id="email" placeholder="Enter email.." value="<?= htmlspecialchars($data["email"]) ?>">
        </label>

        <label for="password">
            Password:
            <input type="password" name="password" id="password" placeholder="Enter password..">
        </label>

        <label for="confirm_password">
            Confirm Password:
            <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm password..">
        </label>

        <button type="submit" class="outline">
            Sign in
        </button>
    </form>

    <?php if (isset($errors)): ?>
        <footer>
            <ul class="pico-color-yellow-100">
                <?php foreach ($errors as $field): ?>
                    <?php foreach ($field as $error): ?>
                        <li><?= $error ?></li>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </ul>
        </footer>
    <?php endif; ?>

</article>