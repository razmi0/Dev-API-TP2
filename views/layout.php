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

<body class="container-fluid">
    <header style="margin-block: 1rem;">
        <nav>
            <ul>
                <li><strong>API Interface</strong></li>
            </ul>
            <ul>
                <li><a href="/" class="secondary">Home</a></li>
                <li>
                    <details class="dropdown">
                        <summary>
                            Account
                        </summary>
                        <ul dir="rtl">
                            <li><a href="/signup">Signup</a></li>
                            <li><a href="#">Login</a></li>
                            <li><a href="#">Profile</a></li>
                            <li><a href="#">Logout</a></li>
                        </ul>
                    </details>
                </li>
            </ul>
        </nav>
    </header>
    <?= $content ?>
</body>

</html>