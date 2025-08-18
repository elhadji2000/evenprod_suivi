<head>
    <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png" />
    <link rel="icon" type="image/png" href="../assets/img/favicon.png" />
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700,900" />
    <!-- Nucleo Icons -->
    <link href="../assets/css/nucleo-icons.css" rel="stylesheet" />
    <link href="../assets/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0" />
    <style>
    .card-img-fixed {
        width: 100%;
        /* pleine largeur de la colonne */
        height: 200px;
        /* hauteur fixe pour toutes les images */
        object-fit: cover;
        /* recadre l'image sans la déformer */
        border-radius: .5rem;
        box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }
    </style>

</head>
<?php include '../includes/header.php'; ?>

<body class="bg-light">
    <div class="container-fluid">

        <!-- Section équipe -->
        <section class="pb-5 bg-dark text-white">
            <div class="container">
                <div class="row">
                    <div class="col-md-8 mb-5 mt-5">
                        <h3 style="color:white;">The Executive Team</h3>
                        <p class="opacity-75 mb-0">
                            There’s nothing I really wanted to do in life that I wasn’t able to get good at. That’s my
                            skill.
                        </p>
                    </div>
                </div>

                <div class="row g-4">
                    <!-- Card 1 -->
                    <div class="col-lg-6 col-12">
                        <div class="card shadow-sm border-0">
                            <div class="row g-0">
                                <div class="col-lg-4 col-md-6 mt-n3">
                                    <img src="../assets/images/series1.jpg" class="card-img-fixed" alt="Emma Roberts">
                                </div>
                                <div class="col-lg-8 col-md-6 d-flex align-items-center">
                                    <div class="card-body">
                                        <h5 class="card-title mb-1">Emma Roberts</h5>
                                        <h6 class="mb-2">
                                            <a href="http://localhost/projet_suivi/public/appManager/series/about.php" class="text-primary text-decoration-none">UI Designer</a>
                                        </h6>

                                        <p class="card-text mb-0">
                                            Artist is a term applied to a person who engages in an activity deemed to be
                                            an art.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div class="col-lg-6 col-12">
                        <div class="card shadow-sm border-0 mt-lg-0 mt-4">
                            <div class="row g-0">
                                <div class="col-lg-4 col-md-6 mt-n3">
                                    <img src="../assets/images/images1.jpg" class="card-img-fixed" alt="William Pearce">
                                </div>
                                <div class="col-lg-8 col-md-6 d-flex align-items-center">
                                    <div class="card-body">
                                        <h5 class="mb-1">William Pearce</h5>
                                        <h6 class="text-primary mb-2">Boss</h6>
                                        <p class="mb-0">
                                            Artist is a term applied to a person who engages in an activity deemed to be
                                            an art.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ligne suivante -->
                <div class="row g-4 mt-4">
                    <!-- Card 3 -->
                    <div class="col-lg-6 col-12">
                        <div class="card shadow-sm border-0">
                            <div class="row g-0">
                                <div class="col-lg-4 col-md-6 mt-n3">
                                    <img src="../assets/images/series3.jpg" class=" card-img-fixed" alt="Ivana Flow">
                                </div>
                                <div class="col-lg-8 col-md-6 d-flex align-items-center">
                                    <div class="card-body">
                                        <h5 class="mb-1">Ivana Flow</h5>
                                        <h6 class="text-primary mb-2">Athlete</h6>
                                        <p class="mb-0">
                                            Artist is a term applied to a person who engages in an activity deemed to be
                                            an art.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div class="col-lg-6 col-12">
                        <div class="card shadow-sm border-0 mt-lg-0 mt-4">
                            <div class="row g-0">
                                <div class="col-lg-4 col-md-6 mt-n3">
                                    <img src="../assets/images/series2.jpg" class="card-img-fixed" alt="Marquez Garcia">
                                </div>
                                <div class="col-lg-8 col-md-6 d-flex align-items-center">
                                    <div class="card-body">
                                        <h5 class="mb-1">Marquez Garcia</h5>
                                        <h6 class="text-primary mb-2">JS Developer</h6>
                                        <p class="mb-0">
                                            Artist is a term applied to a person who engages in an activity deemed to be
                                            an art.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- Logos & Stats -->
        <section class="pt-4 pb-6" id="count-stats">
            <div class="container">
                <div class="row mb-4 g-4 text-center">
                    <div class="col-lg-2 col-md-4 col-6">
                        <img class="img-fluid opacity-50" src="../assets/img/logos/gray-logos/logo-coinbase.svg"
                            alt="logo" />
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <img class="img-fluid opacity-50" src="../assets/img/logos/gray-logos/logo-nasa.svg"
                            alt="logo" />
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <img class="img-fluid opacity-50" src="../assets/img/logos/gray-logos/logo-netflix.svg"
                            alt="logo" />
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <img class="img-fluid opacity-50" src="../assets/img/logos/gray-logos/logo-pinterest.svg"
                            alt="logo" />
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <img class="img-fluid opacity-50" src="../assets/img/logos/gray-logos/logo-spotify.svg"
                            alt="logo" />
                    </div>
                    <div class="col-lg-2 col-md-4 col-6">
                        <img class="img-fluid opacity-50" src="../assets/img/logos/gray-logos/logo-vodafone.svg"
                            alt="logo" />
                    </div>
                </div>

                <section id="count-stats" class="py-5 bg-light">
                    <div class="container">
                        <div class="row justify-content-center text-center g-4">
                            <div class="col-md-3">
                                <h1 class="text-primary" id="state1" data-count="5234">0</h1>
                                <h5>Projects</h5>
                                <p>Led by certified project managers</p>
                            </div>
                            <div class="col-md-3">
                                <h1 class="text-primary" id="state2" data-count="3400">0</h1>
                                <h5>Hours</h5>
                                <p>Meeting quality standards required by users</p>
                            </div>
                            <div class="col-md-3">
                                <h1 class="text-primary" id="state3" data-count="24">0</h1>
                                <h5>Support</h5>
                                <p>Actively engage team members finishing on time</p>
                            </div>
                        </div>
                    </div>
                </section>
        </section>

        <script src="https://cdnjs.cloudflare.com/ajax/libs/countup.js/2.6.2/countUp.min.js"></script>
        <script>
        document.addEventListener("DOMContentLoaded", function() {
            const counters = ["state1", "state2", "state3"];
            let animated = false;

            function inView(el) {
                const rect = el.getBoundingClientRect();
                return rect.top < window.innerHeight && rect.bottom >= 0;
            }

            function animateCounters() {
                if (animated) return;
                const container = document.getElementById("count-stats");
                if (inView(container)) {
                    counters.forEach(id => {
                        const el = document.getElementById(id);
                        const endVal = parseInt(el.getAttribute("data-count"), 10);
                        const countUp = new CountUp(id, endVal, {
                            duration: 2
                        });
                        if (!countUp.error) countUp.start();
                    });
                    animated = true;
                }
            }

            window.addEventListener("scroll", animateCounters);
            animateCounters(); // au cas où déjà visible au chargement
        });
        </script>


</body>

</html>