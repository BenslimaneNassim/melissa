<?php
try {
    $myproject = new PDO("mysql:host=localhost;dbname=myproject;charset=utf8", "root", "");
    $myproject->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('<p style="color: red;">Erreur de connexion à la base de données : ' . $e->getMessage() . '</p>');
}
?>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Application de Gestion</title>



    <!-- Bootstrap Link -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Bootstrap Link -->

    <link rel="stylesheet" href="style.css">

    <!-- Font Awesome Cdn -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css">
    <!-- Font Awesome Cdn -->


    <!-- Google Fonts -->
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet"> -->
    <!-- Google Fonts -->
</head>

<body>






    <!-- Navbar Start -->
    <!-- Nav bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#"><img src="img/Logo.png" width="40" alt=""></a>
            <!-- c le button du menu sandwitch -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#home">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#packages">Cours</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">Consignes</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#book">Contacts</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#footer">Info</a>
                    </li>
                </ul>
                <!-- <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="rechercher" aria-label="Search">
                    <button class="btn btn-outline-success" type="submit">rechercher</button>
                </form> -->
                <div id="divheader">
                    <a href="login.php"><button type="button" data-bs-toggle="modal" data-bs-target="#exampleModal" class="btn btn-outline-primary">Enseignant</button></a>
                    <!-- <a href="login.php"><button type="" class="btn btn-outline-success">Admin</button></a> -->
                </div>
            </div>
        </div>
    </nav>
    <!-- Navbar End -->





    <!-- Home Section Start -->
    <div class="home">
        <div class="content">
            <h5>Université de Bajaia</h5>
            <h1>Gestion <span class="changecontent"></span></h1>
            <p>Gestion des plannings des examens de l'université de bejaia.</p>
            <!-- <a href="#book">Cantactez nous</a> -->
        </div>
    </div>
    <!-- Home Section End -->










    <main>
        <!-- Section universite Start -->
        <section class="packages" id="packages">
            <div class="container">

                <div class="main-txt">
                    <h1><span>C</span>ours</h1>
                </div>

                <div class="row" style="margin-top: 30px;">

                    <div class="col-md-4 py-3 py-md-0">

                        <div class="card">
                            <img src="img/informatique.jpeg" height="350" alt="">
                            <div class="card-body">
                                <h3>Informatique</h3>
                                <p>Nos cours en informatique offrent une introduction complète à ce domaine en évolution
                                    rapide, vous préparant à relever les défis technologiques du monde moderne.</p>
                                <a href="#book"><button id="about-btn">Contact</button></a>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-4 py-3 py-md-0">

                        <div class="card">
                            <img src="img/physique.jpeg" height="350" alt="">
                            <div class="card-body">
                                <h3>Physique</h3>
                                <p>Plongez dans les mystères de l'univers avec nos cours de physique qui couvrent une
                                    variété de sujets, de la mécanique classique à la physique quantique. </p>
                                <a href="#book"><button id="about-btn">Contact</button></a>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-4 py-3 py-md-0">

                        <div class="card">
                            <img src="img/architecture.jpeg" height="350" alt="">
                            <div class="card-body">
                                <h3>Architecture</h3>
                                <p>Découvrez l'art et la science de la conception architecturale à travers nos cours qui
                                    explorent les principes de la conception spatiale, de la durabilité environnementale
                                    et architecturale.</p>
                                <a href="#book"><button id="about-btn">Contact</button></a>
                            </div>
                        </div>

                    </div>



                </div>




            </div>
        </section>
        <!-- Section Packages End -->




        <!-- About Start -->
        <section class="about" id="about">
            <div class="container">

                <div class="main-txt">
                    <h1><span>C</span>onsignes</h1>
                </div>

                <div class="row" style="margin-top: 50px;">

                    <div class="col-md-5 py-3 py-md-0">
                        <div class="card">
                            <img src="img/education.jpeg" alt="">
                        </div>
                    </div>

                    <div class="col-md-7 py-3 py-md-0">
                        <h3>Consignes de bon déroulement des examens.</h3>
                        <p>Pour assurer le bon déroulement des examens, les étudiants sont tenus de respecter les règles
                            suivantes :<br>
                            - L’étudiant doit se munir de sa carte d’étudiant.<br>
                            - Pendant toute la durée de l’épreuve, l’usage des téléphones portables, ou de tout autre
                            matériel programmable ou d’écoute est strictement interdit.<br>
                            - L’étudiant doit se munir du matériel nécessaire (effaceur, stylos, etc.).<br>
                            - L’utilisation du stylo rouge est strictement interdite.<br>
                            - Tout retard de plus de 20 min va entraîner l'interdiction d'entrer dans la salle
                            d'examen.<br>
                            - Aucun étudiant n’est autorisé à quitter le lieu d’examen avant les 30 minutes qui suivent
                            la
                            remise des sujets.<br>
                            - Toute fraude ou tentative de fraude conduit automatiquement le contrevenant à sa
                            traduction
                            devant le conseil de discipline.<br><br></p>
                        <a href="#book"><button id="about-btn">Contact...</button></a>
                    </div>

                </div>

            </div>
        </section>
        <!-- About End -->






        <!-- Section Contact -->
        <section class="book" id="book">
            <div class="container">

                <div class="main-text">
                    <h1><span>C</span>ontactez nous</h1>
                </div>

                <div class="row">

                    <div class="col-md-6 py-3 py-md-0">
                        <div class="card">
                            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3174.7641203914186!2d5.0381952040218145!3d36.75026264845937!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x128d3367dd909473%3A0xb5a389343ac0ce10!2sUniversit%C3%A9%20Abderrahmane%20Mira%20de%20B%C3%A9jaia!5e0!3m2!1sfr!2sdz!4v1709997391750!5m2!1sfr!2sdz" height="500" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                        </div>
                    </div>

                    <div class="col-md-6 py-3 py-md-0">
                        <form action="#">

                            <input type="text" class="form-control" placeholder="Name" required id="name" name="user_name"><br>
                            <input type="text" class="form-control" placeholder="Email" required id="mail" name="user_email"><br>
                            <input type="text" class="form-control" placeholder="Objet" required id="proj" name="user_project"><br>
                            <textarea class="form-control" rows="5" name="text" placeholder="Message" name="user_message" id="mess"></textarea>
                            <!-- <input type="submit" value="Book Now" class="submit" required> -->
                            <br>
                            <div>
                                <button type="submit" class="btn btn-outline-success">
                                    Envoyer <i class="uil uil-message button__icon"></i>
                                </button>
                            </div>
                        </form>
                    </div>

                </div>
            </div>
        </section>
        <!-- Section Contact End -->

    </main>





    <!-- Footer Start -->
    <!-- <footer id="footer">
        <h3><span></span>Université de bejaia</h3>
        <p>Gestion des plannings des examens de l'université de bejaia</p>
        <div class="col-md-2 py-3 py-md-0">
            <img src="img/fondUniv.png" width="90" height="90" alt="">
        </div>

        <div class="social-links">
            <i class="fa-brands fa-google"></i>
            <i class="fa-solid fa-phone"></i>
            <i class="fa-solid fa-map-marker-alt"></i>
        </div>
        <div class="social-span">
            <span>univbejaia@gmail.com</span>
            <span>Tel : (+213) 754327896</span>
            <span>Q22R+475, Targa ouzemour, Béjaïa</span>
        </div>
        <div class="credit">
            <a href="#home" class="footer__link">Home</a>
            <a href="#consignes" class="footer__link">Consignes</a>
            <a href="#contact" class="footer__link">Contactez nous</a>
        </div>
        <div class="copyright">
            <p>&copy;Copyright All Rights Reserved</p>
        </div>

    </footer> -->

    <footer id="footer">
        <div class="row">
            <h3><span></span>Université de bejaia</h3>
            <p>Gestion des plannings des examens de l'université de bejaia</p>
        </div>
        <div class="row">
            <div class="col-md-3 py-3 py-md-0">
                <img src="img/fondUniv.png" width="90" height="90" alt="">
            </div>
            <div class="col-md-9">

                <div class="social-info">
                    <div class="social-item">
                        <i class="fa-brands fa-google"></i>
                        <span>univbejaia@gmail.com</span>
                    </div>
                    <div class="social-item">
                        <i class="fa-solid fa-phone"></i>
                        <span>Tel : (+213) 754327896</span>
                    </div>
                    <div class="social-item">
                        <i class="fa-solid fa-map-marker-alt"></i>
                        <span>Q22R+475, Targa ouzemour, Béjaïa</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- <div class="credit">
                    <a href="#home" class="footer__link">Home</a>
                    <a href="#consignes" class="footer__link">Consignes</a>
                    <a href="#contact" class="footer__link">Contactez nous</a>
                </div> -->
                <div class="copyright">
                    <p>&copy; Copyright All Rights Reserved</p>
                </div>
            </div>
        </div>
    </footer>



    <!-- Footer End -->










    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

</body>

</html>