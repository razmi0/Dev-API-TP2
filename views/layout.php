<!DOCTYPE html>
<html lang="en" data-theme="dark">

<head>
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.colors.min.css">
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.pumpkin.min.css">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | <?= $title ?></title>
</head>

<body class="container-fluid" style="max-height: 100vh;">
    <header style="margin-block: 1rem;">
        <nav>
            <ul>
                <li><strong>API Interface</strong></li>
            </ul>
            <ul>
                <li>
                    <svg height="24" width="24" viewBox="0 -4 24 24" fill="<?= isset($_SESSION["user_id"]) ? "green" : "red" ?>" stroke="<?= isset($_SESSION["user_id"]) ? "green" : "red" ?>">
                        <circle cx="12" cy="7" r="4"></circle>
                    </svg>
                </li>
                <li><a href="/" class="secondary">Home</a></li>
                <li>
                    <details class="dropdown">
                        <summary>
                            Account
                        </summary>
                        <ul dir="rtl">
                            <?php if (isset($_SESSION["user_id"])) : ?>
                                <li><a href="/profile">Profile</a></li>
                                <li><a href="/logout">Logout</a></li>
                            <?php else : ?>
                                <li><a href="/signup">Signup</a></li>
                                <li><a href="/login">Login</a></li>
                            <?php endif; ?>
                        </ul>
                    </details>
                </li>
            </ul>
        </nav>
    </header>
    <main class="container-fluid">
        <?= $content ?>
    </main>
</body>

</html>