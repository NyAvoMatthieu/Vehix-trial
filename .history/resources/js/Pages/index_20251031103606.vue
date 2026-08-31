<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

onMounted(() => {
    // Initialisation des scripts du template original
    if (window.AOS) {
        window.AOS.init();
    }
    
    // Typed.js initialization
    if (window.Typed) {
        const typed = new window.Typed('.typed', {
            strings: ["d'état de votre véhicule", "de Maintenance", "de dépenses", "de circulabilité"],
            typeSpeed: 50,
            backSpeed: 50,
            loop: true
        });
    }
});
</script>

<template>
    <Head title="Vehix - Vehicle Manager digital" />
    
    <div class="index-page">
        <!-- Header -->
        <header id="header" class="header dark-background d-flex flex-column">
            <i class="header-toggle d-xl-none bi bi-list"></i>

            <div class="profile-img">
                <img src="/assets/img/logovehix.png" alt="" class="img-fluid">
            </div>

            <a href="#hero" class="logo d-flex align-items-center justify-content-center">
                <!-- <h1 class="sitename digifont">VEHIX</h1> -->
            </a>

            <div class="social-links text-center">
                <a href="#" class="twitter"><i class="bi bi-twitter-x"></i></a>
                <a href="#" class="facebook"><i class="bi bi-facebook"></i></a>
                <a href="#" class="instagram"><i class="bi bi-instagram"></i></a>
                <a href="#" class="linkedin"><i class="bi bi-linkedin"></i></a>
            </div>

            <nav id="navmenu" class="navmenu">
                <ul>
                    <li><a href="#hero" class="active"><i class="bi bi-house navicon"></i>Accueil</a></li>
                    <li><a href="#about"><i class="bi bi-person navicon"></i>Qu'est-ce que VEHIX?</a></li>
                    <li><a href="#services"><i class="bi bi-hdd-stack navicon"></i>Services</a></li>
                    <li><a href="#portfolio"><i class="bi bi-images navicon"></i>Partenaires</a></li>
                    <li><a href="#contact"><i class="bi bi-envelope navicon"></i>Contact</a></li>
                    <li><a href="/cgu.html" target="_blank"><i class="bi bi-images navicon"></i>Conditions d'utilisation</a></li>
                    
                    <!-- Auth Links -->
                    <li v-if="canLogin && !$page.props.auth.user">
                        <Link :href="route('login')" class="text-white">
                            <i class="bi bi-box-arrow-in-right navicon"></i>Connexion
                        </Link>
                    </li>
                    <li v-if="canRegister && !$page.props.auth.user">
                        <Link :href="route('register')" class="text-white">
                            <i class="bi bi-person-plus navicon"></i>S'inscrire
                        </Link>
                    </li>
                    <li v-if="$page.props.auth.user">
                        <Link :href="route('dashboard')" class="text-white">
                            <i class="bi bi-speedometer2 navicon"></i>Dashboard
                        </Link>
                    </li>
                </ul>
            </nav>
            
            <div class="profile-img">
                <img src="/assets/img/hasnreziga.png" alt="" class="img-fluid rounded-circle">
            </div>
        </header>

        <!-- Main -->
        <main class="main">
            <!-- Hero Section -->
            <section id="hero" class="hero section dark-background">
                <img src="/assets/img/collage.png" alt="" data-aos="fade-in" class="">

                <div class="container" data-aos="fade-up" data-aos-delay="100">
                    <h2 class="digifont">VEHIX - VEHICLE MANAGER DIGITAL</h2>
                    <p> CARNET DE BORD NUMERIQUE de votre véhicule </p>
                    <p> Application de Suivi <span class="typed" data-typed-items="d'état de votre véhicule, de Maintenance, de dépenses, de circulabilité"> d'état de votre véhicule</span><span class="typed-cursor typed-cursor--blink" aria-hidden="true"></span><span class="typed-cursor typed-cursor--blink" aria-hidden="true"></span></p>
                
                    <Link v-if="canRegister" :href="route('register')">
                        <button type="submit">S'inscrire</button>
                    </Link>
                    <Link v-if="canLogin && !$page.props.auth.user" :href="route('login')">
                        <button type="submit">Accéder à l'Application</button>
                    </Link>
                    <Link v-if="$page.props.auth.user" :href="route('dashboard')">
                        <button type="submit">Accéder à l'Application</button>
                    </Link>
                </div>
            </section><!-- /Hero Section -->

            <!-- About Section -->
            <section id="about" class="about section">
                <!-- Section Title -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>Qu'est-ce que Vehix?</h2>
                    <p>Découvrez VEHIX, l'application innovante qui révolutionne la gestion de votre véhicule (voitures, motos, camions,...).
                    <br> Accessible sur le web et les mobiles, VEHIX est l'outil idéal pour les usagers de la route souhaitant suivre et gérer leur bien en temps réel. Conçu pour les conducteurs exigeants, VEHIX offre une solution complète et intuitive pour une gestion sécurisée et efficace de votre véhicule.
                    <br>Avec Véhix, vous pouvez:
                        <ul>
                            <li type="square"> suivre l'état de votre véhicule en temps réel </li>
                            <li type="square"> gérer vos dépenses et vos rendez-vous </li>
                            <li type="square"> accéder à des informations personnalisées sur votre véhicule </li>
                        </ul>

                    <br>Voyez comment Véhix peut vous aider à optimiser votre expérience de conduite 
                    <br>Utilisez l'application dès aujourd'hui et prenez le contrôle de votre véhicule.
                    </p>
                </div><!-- End Section Title -->

                <div class="container" data-aos="fade-up" data-aos-delay="100">
                    <div class="row gy-4 justify-content-center">
                        <div class="col-lg-4">
                            <img src="/assets/img/logovehix.png" class="img-fluid" alt="VEHIX">
                        </div>
                        <div class="col-lg-8 content">
                            <h2> Fonctionnalités et avantages</h2>
                            <p class="fst-italic py-3">
                                Simplifiez votre gestion automobile en quelques clics :
                                <ul>
                                    <li type="square"> Téléchargez l'application "VEHIX" sur votre ordinateur ou smartphone. </li>
                                    <li type="square"> Inscrivez-vous en ligne en toute sécurité.</li>
                                    <li type="square"> Ajoutez vos véhicules et propriétaires en quelques étapes. </li>
                                </ul>

                                <br> C'est gratuit, simple et efficace!
                            </p>
                            <div class="row">
                                <div class="col-lg-6">
                                    <ul>
                                        <li><i class="bi bi-chevron-right"></i> <strong>Maîtriser les dépenses </strong> <span> </span></li>
                                        <li><i class="bi bi-chevron-right"></i> <strong>Programmer les maintenances</strong> <span> </span></li>
                                        <li><i class="bi bi-chevron-right"></i> <strong>Contrôler les performances</strong> <span> </span></li>
                                        <li><i class="bi bi-chevron-right"></i> <strong>Diminuer les gaspillages - Economiser</strong> <span> </span></li>
                                    </ul>
                                </div>
                                <div class="col-lg-6">
                                    <ul>
                                        <li><i class="bi bi-chevron-right"></i> <strong>Retrouver les garages spécialisés</strong> <span> </span></li>
                                        <li><i class="bi bi-chevron-right"></i> <strong>Rerchercher des vendeurs</strong> <span> </span></li>
                                        <li><i class="bi bi-chevron-right"></i> <strong>Eviter les pannes</strong> <span> </span></li>
                                        <li><i class="bi bi-chevron-right"></i> <strong>Comparer les stations services</strong> <span> </span></li>
                                    </ul>
                                </div>
                            </div>
                            <p class="py-3">
                                Par ailleurs, le concepteur, <a href="https://hasnreziga.mg" target="_blank">HASNREZIGA Informatique </a>, est une entreprise experte en informatique et dans le domaine de l'intelligence artificielle, garantissant ainsi une solution de gestion automobile de haute qualité.
                            </p>
                        </div>
                    </div>
                </div>
            </section><!-- /About Section -->

            <!-- Services Section -->
            <section id="services" class="services section">
                <!-- Section Title -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>Services</h2>
                    <p>Plusieurs services sont disponibles dans VEHIX. Ces services sont listés ci-dessous : </p>
                </div><!-- End Section Title -->

                <div class="container">
                    <div class="row gy-4">
                        <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="100">
                            <div class="icon flex-shrink-0"><i class="bi bi-briefcase"></i></div>
                            <div>
                                <h4 class="title"><a href="service-details.html" class="stretched-link">Tableau de bord et Papiers</a></h4>
                                <p class="description"> Vous pouvez voir le Tableau de bord de votre véhicule en ligne ou sur Téléphone.
                                    <br> Ajouter les informations de votre véhicule, de son propriétaire, de sa dernière visite technique et de sa dernière assurance.
                                </p>
                            </div>
                        </div><!-- End Service Item -->

                        <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="200">
                            <div class="icon flex-shrink-0"><i class="bi bi-card-checklist"></i></div>
                            <div>
                                <h4 class="title"><a href="service-details.html" class="stretched-link">Trajets</a></h4>
                                <p class="description">Mettre votre déplacement dans Vehix et l'application se chargera d'historiser votre déplacement et vous indique l'état de votre véhicule.</p>
                            </div>
                        </div><!-- End Service Item -->

                        <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="300">
                            <div class="icon flex-shrink-0"><i class="bi bi-bar-chart"></i></div>
                            <div>
                                <h4 class="title"><a href="service-details.html" class="stretched-link">Ravitaillements</a></h4>
                                <p class="description">Vehix permet de gérer votre ravitaillement en carburant, de suivre les coûts, de voir les consommations. 
                                </p>
                            </div>
                        </div><!-- End Service Item -->

                        <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="400">
                            <div class="icon flex-shrink-0"><i class="bi bi-binoculars"></i></div>
                            <div>
                                <h4 class="title"><a href="service-details.html" class="stretched-link">Entretiens</a></h4>
                                <p class="description">Vous pouvez gerer ici votre plan d'entretien comme les vidanges, changement de courroie, ... 
                                    <br> Vous pouvez trouver aussi les garages spécialisés dans la réalisation des diverses entretiens.</p>
                            </div>
                        </div><!-- End Service Item -->

                        <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="500">
                            <div class="icon flex-shrink-0"><i class="bi bi-brightness-high"></i></div>
                            <div>
                                <h4 class="title"><a href="service-details.html" class="stretched-link">Pièces</a></h4>
                                <p class="description">La gestion des pièces est un atout majeur dans l'utilisation de Vehix. Vous pouvez comparer les marques, les prix, les revendeurs, ....
                                    <br>En plus, l'application permet de determiner l'état de chaque pièce dans votre véhicule.
                                </p>
                            </div>
                        </div><!-- End Service Item -->

                        <div class="col-lg-4 col-md-6 service-item d-flex" data-aos="fade-up" data-aos-delay="600">
                            <div class="icon flex-shrink-0"><i class="bi bi-calendar4-week"></i></div>
                            <div>
                                <h4 class="title"><a href="service-details.html" class="stretched-link">Vehicule et Propriétaire</a></h4>
                                <p class="description">Vous pouvez stocker ici les informations essentielles de votre véhicule. En cas de perte, ces informations serviront de base de recherche.</p>
                            </div>
                        </div><!-- End Service Item -->
                    </div>
                </div>
            </section><!-- /Services Section -->

            <!-- Stats Section -->
            <section id="stats" class="stats section">
                <div class="container" data-aos="fade-up" data-aos-delay="100">
                    <div class="row gy-4">
                        <div class="col-lg-3 col-md-6">
                            <div class="stats-item">
                                <i class="bi bi-emoji-smile"></i>
                                <span data-purecounter-start="0" data-purecounter-end="232" data-purecounter-duration="1" class="purecounter"></span>
                                <p><strong>Véhicules et moto enregistrés</strong> <span> Merci pour votre confiance</span></p>
                            </div>
                        </div><!-- End Stats Item -->

                        <div class="col-lg-3 col-md-6">
                            <div class="stats-item">
                                <i class="bi bi-journal-richtext"></i>
                                <span data-purecounter-start="0" data-purecounter-end="521" data-purecounter-duration="1" class="purecounter"></span>
                                <p><strong>Projects</strong> <span>adipisci atque cum quia aut</span></p>
                            </div>
                        </div><!-- End Stats Item -->

                        <div class="col-lg-3 col-md-6">
                            <div class="stats-item">
                                <i class="bi bi-headset"></i>
                                <span data-purecounter-start="0" data-purecounter-end="1453" data-purecounter-duration="1" class="purecounter"></span>
                                <p><strong>Hours Of Support</strong> <span>aut commodi quaerat</span></p>
                            </div>
                        </div><!-- End Stats Item -->

                        <div class="col-lg-3 col-md-6">
                            <div class="stats-item">
                                <i class="bi bi-people"></i>
                                <span data-purecounter-start="0" data-purecounter-end="32" data-purecounter-duration="1" class="purecounter"></span>
                                <p><strong>Hard Workers</strong> <span>rerum asperiores dolor</span></p>
                            </div>
                        </div><!-- End Stats Item -->
                    </div>
                </div>
            </section><!-- /Stats Section -->

            <!-- Portfolio Section -->
            <section id="portfolio" class="portfolio section light-background">
                <!-- Section Title -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>Partenaires</h2>
                    <p>Voici les différents partenaires de cette application: </p>
                </div><!-- End Section Title -->

                <div class="container">
                    <div class="isotope-layout" data-default-filter="*" data-layout="masonry" data-sort="original-order">
                        <ul class="portfolio-filters isotope-filters" data-aos="fade-up" data-aos-delay="100">
                            <li data-filter="*" class="filter-active">Tous</li>
                            <li data-filter=".filter-app">PLATINIUM</li>
                            <li data-filter=".filter-product">GOLD</li>
                            <li data-filter=".filter-branding">SILVER</li>
                            <li data-filter=".filter-books">BRONZE</li>
                        </ul><!-- End Portfolio Filters -->

                        <div class="row gy-4 isotope-container" data-aos="fade-up" data-aos-delay="200">
                            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-app">
                                <div class="portfolio-content h-100">
                                    <img src="/assets/img/portfolio/hasnreziga.png" class="img-fluid" alt="">
                                    <div class="portfolio-info">
                                        <h4>Hasnreziga Informatique</h4>
                                        <p> Entreprise spécialisée en Informatique : 
                                            <br>Maintenance et Administration réseaux/systèmes
                                            <br>Developpement d'Applications
                                            <br>ERP 
                                            <br>Analyse des données
                                        </p>
                                        <a href="/assets/img/portfolio/hasnreziga.png" title="Hasnreziga Informatique" data-gallery="portfolio-gallery-app" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                                        <a href="https://hasnreziga.mg" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                                    </div>
                                </div>
                            </div><!-- End Portfolio Item -->

                            <!-- Répéter pour les autres items du portfolio -->
                            <div class="col-lg-4 col-md-6 portfolio-item isotope-item filter-product">
                                <div class="portfolio-content h-100">
                                    <img src="/assets/img/portfolio/hasnreziga.png" class="img-fluid" alt="">
                                    <div class="portfolio-info">
                                        <h4>HASNREZIGA SARLU</h4>
                                        <p>Fournisseur d'équipements informatiques, bureautiques, ....</p>
                                        <a href="/assets/img/portfolio/hasnreziga.png" title="HASNREZIGA SARLU" data-gallery="portfolio-gallery-product" class="glightbox preview-link"><i class="bi bi-zoom-in"></i></a>
                                        <a href="https://hasnreziga.mg" title="More Details" class="details-link"><i class="bi bi-link-45deg"></i></a>
                                    </div>
                                </div>
                            </div><!-- End Portfolio Item -->

                            <!-- Les autres items du portfolio... (je les abrège pour la lisibilité) -->
                        </div><!-- End Portfolio Container -->
                    </div>
                </div>
            </section><!-- /Portfolio Section -->

            <!-- Testimonials Section -->
            <section id="testimonials" class="testimonials section light-background">
                <!-- Section Title -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>Avis</h2>
                    <p>Voici les avis et témoignages des utilisateurs</p>
                </div><!-- End Section Title -->

                <div class="container" data-aos="fade-up" data-aos-delay="100">
                    <div class="swiper init-swiper">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide">
                                <div class="testimonial-item">
                                    <p>
                                        <i class="bi bi-quote quote-icon-left"></i>
                                        <span>Tsy voatery "<i>Harena mandany harena</i>" ny fiara na ny moto anananao.</span><br>
                                        <span>Cet outil va diminuer les gaspillages en termes de gestion de votre véhicule.</span>
                                        <i class="bi bi-quote quote-icon-right"></i>
                                    </p>
                                    <img src="/assets/img/testimonials/hasina.png" class="testimonial-img" alt="">
                                    <h3>RAKOTOARISOA Hasina Patrick</h3>
                                    <h4>CEO &amp; Founder HASNREZIGA</h4>
                                </div>
                            </div><!-- End testimonial item -->

                            <div class="swiper-slide">
                                <div class="testimonial-item">
                                    <p>
                                        <i class="bi bi-quote quote-icon-left"></i>
                                        <span>Export tempor illum tamen malis malis eram quae irure esse labore quem cillum quid malis quorum velit fore eram velit sunt aliqua noster fugiat irure amet legam anim culpa.</span>
                                        <i class="bi bi-quote quote-icon-right"></i>
                                    </p>
                                    <img src="/assets/img/testimonials/hasnreziga.png" class="testimonial-img" alt="">
                                    <h3>RASOA Sara Wilsson</h3>
                                    <h4>Propriétaire</h4>
                                </div>
                            </div><!-- End testimonial item -->

                            <!-- Autres témoignages... -->
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </section><!-- /Testimonials Section -->

            <!-- Contact Section -->
            <section id="contact" class="contact section">
                <!-- Section Title -->
                <div class="container section-title" data-aos="fade-up">
                    <h2>Contact</h2>
                    <p> VEHIX est un produit conçu et developpé par HASNREZIGA Informatique. N'hésitez pas à nous contacter en cas de besoin!</p>
                </div><!-- End Section Title -->

                <div class="container" data-aos="fade-up" data-aos-delay="100">
                    <div class="row gy-4">
                        <div class="col-lg-5">
                            <div class="info-wrap">
                                <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="200">
                                    <i class="bi bi-geo-alt flex-shrink-0"></i>
                                    <div>
                                        <h3>Adresse</h3>
                                        <p>Ambohipo LOT VT 31 C Bis, 101 Antananarivo - Madagascar</p>
                                    </div>
                                </div><!-- End Info Item -->

                                <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="300">
                                    <i class="bi bi-telephone flex-shrink-0"></i>
                                    <div>
                                        <h3>Téléphone</h3>
                                        <p>+261 34 40 994 35</p>
                                    </div>
                                </div><!-- End Info Item -->

                                <div class="info-item d-flex" data-aos="fade-up" data-aos-delay="400">
                                    <i class="bi bi-envelope flex-shrink-0"></i>
                                    <div>
                                        <h3>Email</h3>
                                        <p>hasnreziga@gmail.com</p>
                                    </div>
                                </div><!-- End Info Item -->
                                
                                <iframe src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3774.0108703217525!2d47.559384074383836!3d-18.930912982244024!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2s!5e0!3m2!1sfr!2smg!4v1761679728125!5m2!1sfr!2smg" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>
                        </div>

                        <div class="col-lg-7">
                            <form action="/forms/contact.php" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="200">
                                <div class="row gy-4">
                                    <div class="col-md-6">
                                        <label for="name-field" class="pb-2">Votre Nom</label>
                                        <input type="text" name="name" id="name-field" class="form-control" required="">
                                    </div>

                                    <div class="col-md-6">
                                        <label for="email-field" class="pb-2">Votre Email</label>
                                        <input type="email" class="form-control" name="email" id="email-field" required="">
                                    </div>

                                    <div class="col-md-12">
                                        <label for="subject-field" class="pb-2">Sujet</label>
                                        <input type="text" class="form-control" name="subject" id="subject-field" required="">
                                    </div>

                                    <div class="col-md-12">
                                        <label for="message-field" class="pb-2">Message</label>
                                        <textarea class="form-control" name="message" rows="10" id="message-field" required=""></textarea>
                                    </div>

                                    <div class="col-md-12 text-center">
                                        <div class="loading">Loading</div>
                                        <div class="error-message"></div>
                                        <div class="sent-message">Votre message est envoyé. Nous vous remercions!</div>

                                        <button type="submit">Envoyer Message</button>
                                    </div>
                                </div>
                            </form>
                        </div><!-- End Contact Form -->
                    </div>
                </div>
            </section><!-- /Contact Section -->
        </main>

        <!-- Footer -->
        <footer id="footer" class="footer position-relative light-background">
            <div class="container">
                <div class="copyright text-center ">
                    <p>© <span>Copyright</span> <strong class="px-1 sitename">Vehix</strong> <span>All Rights Reserved</span></p>
                </div>
                <div class="credits">
                    Designed by <a href="https://www.hasnreziga.mg/">HASNREZIGA</a>
                </div>
                <div class="text-center mt-2 text-muted small">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </div>
            </div>
        </footer>

        <!-- Scroll Top -->
        <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center">
            <i class="bi bi-arrow-up-short"></i>
        </a>

        <!-- Preloader -->
        <div id="preloader"></div>
    </div>
</template>

<style>
/* Importez vos CSS du template original */
@import '/assets/vendor/bootstrap/css/bootstrap.min.css';
@import '/assets/vendor/bootstrap-icons/bootstrap-icons.css';
@import '/assets/vendor/aos/aos.css';
@import '/assets/vendor/glightbox/css/glightbox.min.css';
@import '/assets/vendor/swiper/swiper-bundle.min.css';
@import '/assets/css/main.css';
</style>