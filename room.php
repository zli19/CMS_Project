<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="main.css">
</head>

<body class="box-border">
    <header class="flex justify-center py-4">
        <nav class="flex justify-between w-4/5 max-w-5xl">
            <a href="./index.php" style="text-decoration: none; color: black;">
                <h1 class="text-2xl font-bold">Molijun Inn</h1>
            </a>
            <ul>
                <li><a href="./login.php"><b class="px-6 py-2 border rounded">Sign in</b></a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section class="flex justify-center px-4 py-8">
            <div class="w-4/5 max-w-5xl grid grid-cols-5 gap-8">
                <div class="col-span-3"><img src="./images/room1.jpg" alt=""></div>
                <div class="col-span-2">
                    <h2>room_name</h2>
                    <p>description</p>
                </div>
            </div>
        </section>
        <section class="flex justify-center px-4 py-8">
            <ul class="w-4/5 max-w-5xl">
                <li class="grid grid-cols-4">
                    <div class="col-span-1">
                        <h4>user_name</h4>
                        <p>created_at</p>
                    </div>
                    <div class="col-span-3">
                        <div>star_no</div>
                        <p>content</p>
                    </div>
                </li>
            </ul>
        </section>
    </main>
</body>

</html>