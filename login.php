<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="assets/css/login.css">
    <title>Login Page</title>
</head>

<body>

    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="loginAdmin.php" method="post">
                <h1>Administrateur</h1>
                <input type="text" name="name" id="name" required placeholder="enter your username" class="form-control" aria-required="true" autocomplete="off">
                <input type="password" name="password" id="password" required placeholder="enter your password" class="form-control" aria-required="true" autocomplete="new-password" autofill="off">
                <button type="submit" class="sign">Se connecter</button>
            </form>
        </div>
        <!-- ///////////////////////LOGIN PROF\\\\\\\\\\\\\\\\\\\\\\\\\  -->
        <div class="form-container sign-in">
            <form action="loginEns.php" method="post">
                <h1>Enseignant</h1>
                <input type="text" name="nom" id="nom" required placeholder="enter your username" class="form-control" aria-required="true" autocomplete="off">
                <input type="password" name="code" id="code" required placeholder="enter your password" class="form-control" aria-required="true" autocomplete="new-password" autofill="off">
                <button type="submit">Se connecter</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Changez d'utilisateur</h1>
                    <p>Utilisez vos coordonnées personnelles pour vous connecter.</p>
                    <button class="hidden" id="login">Enseignant</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Changez d'utilisateur</h1>
                    <p>Utilisez vos coordonnées personnelles pour vous connecter.</p>
                    <button class="hidden" id="register">Administrateur</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/login.js"></script>
</body>

</html>
<!-- 
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css">

    <link rel="stylesheet" href="assets/css/login.css">
    <title>Login Page</title>

</head>

<body>

    <div class="container" id="container">
        <div class="form-container sign-up">
            <form action="login.php" method="post">
                <h1>Administrateur</h1>
                <span>Connectez vous en tant qu'Administrateur.</span>
                <input type="number" placeholder="code administrateur" name="admincode" required>
                <input type="password" placeholder="mot de passe" name="adminmdp" required>
                <a href="#">Mot de passe oublié</a>
                <button type="submit" name="btnadmin">Connexion</button>
            </form>
        </div>
        <div class="form-container sign-in">

            <form action="login.php" method="post">
                <h1>Enseignant</h1>
                <span>Connectez vous en tant qu'Enseignant.</span>
                <input type="email" placeholder="@univ-bejaia.dz" name="ensemail" required>
                <input type="password" placeholder="mot de passe" name="ensmdp" required>
                <button type="submit" name="btnens">Connexion</button>
            </form>
        </div>
        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-right">
                    <h1>Changer d'utilisateur</h1>
                    <p>Utilisez vos coordonnées personnelles pour vous connecter.</p>
                    <button class="hidden" id="login">Administrateur</button>
                </div>
                <div class="toggle-panel toggle-left">
                    <h1>Changer d'utilisateur</h1>
                    <p>Utilisez vos coordonnées personnelles pour vous connecter.</p>
                    <button class="hidden" id="register">Enseignant</button>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/js/login.js"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>


</body>

</html> -->