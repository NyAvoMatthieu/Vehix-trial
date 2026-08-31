<script setup>
import { Head, Link } from '@inertiajs/vue3';
import Typed from 'typed.js';
import AOS from 'aos';
import 'aos/dist/aos.css';

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

function handleImageError() {
    document.getElementById('screenshot-container')?.classList.add('!hidden');
    document.getElementById('docs-card')?.classList.add('!row-span-1');
    document.getElementById('docs-card-content')?.classList.add('!flex-row');
    document.getElementById('background')?.classList.add('!hidden');
}

// Initialize Typed.js and AOS after component is mounted
const typedElement = ref(null);

onMounted(() => {
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true,
    });

    // Initialize Typed.js
    if (typedElement.value) {
        new Typed(typedElement.value, {
            strings: ['Maintenance', 'Carburant', 'Trajets', 'Entretien'],
            typeSpeed: 100,
            backSpeed: 50,
            loop: true,
        });
    }
});
</script>

<template>
    <Head title="VEHIX - Vehicle Manager Digital" />
    <div class="bg-gray-50 text-black/50 dark:bg-black dark:text-white/50">
        <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="https://laravel.com/assets/img/welcome/background.svg  " />
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
                <header class="grid grid-cols-2 items-center gap-2 py-10 lg:grid-cols-3">
                    <div class="flex lg:justify-center lg:col-start-2">
                        <svg class="h-12 w-auto text-white lg:h-16 lg:text-[#FF2D20]" viewBox="0 0 62 65" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M61.8548 14.6253C61.8778 14.7102 61.8895 14.7978 61.8897 14.8858V28.5615C61.8898 28.737 61.8434 28.9095 61.7554 29.0614C61.6675 29.2132 61.5409 29.3392 61.3887 29.4265L49.9104 36.0351V49.1337C49.9104 49.4902 49.7209 49.8192 49.4118 49.9987L25.4519 63.7916C25.3971 63.8227 25.3372 63.8427 25.2774 63.8639C25.255 63.8714 25.2338 63.8851 25.2101 63.8913C25.0426 63.9354 24.8666 63.9354 24.6991 63.8913C24.6716 63.8838 24.6467 63.8689 24.6205 63.8589C24.5657 63.8389 24.5084 63.8215 24.456 63.7916L0.501061 49.9987C0.348882 49.9113 0.222437 49.7853 0.134469 49.6334C0.0465019 49.4816 0.000120578 49.3092 0 49.1337L0 8.10652C0 8.01678 0.0124642 7.92953 0.0348998 7.84477C0.0423783 7.8161 0.0598282 7.78993 0.0697995 7.76126C0.0884958 7.70891 0.105946 7.65531 0.133367 7.6067C0.152063 7.5743 0.179485 7.54812 0.20192 7.51821C0.230588 7.47832 0.256763 7.43719 0.290416 7.40229C0.319084 7.37362 0.356476 7.35243 0.388883 7.32751C0.425029 7.29759 0.457436 7.26518 0.498568 7.2415L12.4779 0.345059C12.6296 0.257786 12.8015 0.211853 12.9765 0.211853C13.1515 0.211853 13.3234 0.257786 13.475 0.345059L25.4531 7.2415H25.4556C25.4955 7.26643 25.5292 7.29759 25.5653 7.32626C25.5977 7.35119 25.6339 7.37362 25.6625 7.40104C25.6974 7.43719 25.7224 7.47832 25.7523 7.51821C25.7735 7.54812 25.8021 7.5743 25.8196 7.6067C25.8483 7.65656 25.8645 7.70891 25.8844 7.76126C25.8944 7.78993 25.9118 7.8161 25.9193 7.84602C25.9423 7.93096 25.954 8.01853 25.9542 8.10652V33.7317L35.9355 27.9844V14.8846C35.9355 14.7973 35.948 14.7088 35.9704 14.6253C35.9792 14.5954 35.9954 14.5692 36.0053 14.5405C36.0253 14.4882 36.0427 14.4346 36.0702 14.386C36.0888 14.3536 36.1163 14.3274 36.1375 14.2975C36.1674 14.2576 36.1923 14.2165 36.2272 14.1816C36.2559 14.1529 36.292 14.1317 36.3244 14.1068C36.3618 14.0769 36.3942 14.0445 36.4341 14.0208L48.4147 7.12434C48.5663 7.03694 48.7383 6.99094 48.9133 6.99094C49.0883 6.99094 49.2602 7.03694 49.4118 7.12434L61.3899 14.0208C61.4323 14.0457 61.4647 14.0769 61.5021 14.1055C61.5333 14.1305 61.5694 14.1529 61.5981 14.1803C61.633 14.2165 61.6579 14.2576 61.6878 14.2975C61.7103 14.3274 61.7377 14.3536 61.7551 14.386C61.7838 14.4346 61.8 14.4882 61.8199 14.5405C61.8312 14.5692 61.8474 14.5954 61.8548 14.6253ZM59.893 27.9844V16.6121L55.7013 19.0252L49.9104 22.3593V33.7317L59.8942 27.9844H59.893ZM47.9149 48.5566V37.1768L42.2187 40.4299L25.953 49.7133V61.2003L47.9149 48.5566ZM1.99677 9.83281V48.5566L23.9562 61.199V49.7145L12.4841 43.2219L12.4804 43.2194L12.4754 43.2169C12.4368 43.1945 12.4044 43.1621 12.3682 43.1347C12.3371 43.1097 12.3009 43.0898 12.2735 43.0624L12.271 43.0586C12.2386 43.0275 12.2162 42.9888 12.1887 42.9539C12.1638 42.9203 12.1339 42.8916 12.114 42.8567L12.1127 42.853C12.0903 42.8156 12.0766 42.7707 12.0604 42.7283C12.0442 42.6909 12.023 42.656 12.013 42.6161C12.0005 42.5688 11.998 42.5177 11.9931 42.4691C11.9881 42.4317 11.9781 42.3943 11.9781 42.3569V15.5801L6.18848 12.2446L1.99677 9.83281ZM12.9777 2.36177L2.99764 8.10652L12.9752 13.8513L22.9541 8.10527L12.9752 2.36177H12.9777ZM18.1678 38.2138L23.9574 34.8809V9.83281L19.7657 12.2459L13.9749 15.5801V40.6281L18.1678 38.2138ZM48.9133 9.14105L38.9344 14.8858L48.9133 20.6305L58.8909 14.8846L48.9133 9.14105ZM47.9149 22.3593L42.124 19.0252L37.9323 16.6121V27.9844L43.7219 31.3174L47.9149 33.7317V22.3593ZM24.9533 47.987L39.59 39.631L46.9065 35.4555L36.9352 29.7145L25.4544 36.3242L14.9907 42.3482L24.9533 47.987Z" fill="currentColor"/></svg>
                    </div>
                    <nav v-if="canLogin" class="-mx-3 flex flex-1 justify-end">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                        >
                            Dashboard
                        </Link>

                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Log in
                            </Link>

                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                            >
                                Register
                            </Link>
                        </template>
                    </nav>
                </header>


                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </footer>
            </div>
        </div>



        <!-- Hero Section -->
        <section id="hero" class="relative min-h-screen flex items-center justify-center bg-gray-900 text-white">
            <img src="/assets/img/collage.png" alt="VEHIX" class="absolute inset-0 w-full h-full object-cover opacity-30">
            
            <div class="container mx-auto px-4 z-10 text-center" data-aos="fade-up">
                <h1 class="text-5xl md:text-7xl font-bold mb-6 font-mono tracking-wider">
                    VEHIX - VEHICLE MANAGER DIGITAL
                </h1>
                <p class="text-2xl mb-4">CARNET DE BORD NUMERIQUE de votre véhicule</p>
                <p class="text-xl mb-8">
                    Application de Suivi <span ref="typed" class="text-blue-400"></span>
                </p>
                
                <div class="flex gap-4 justify-center flex-wrap">
                    <Link :href="route('register')" 
                          class="bg-blue-600 hover:bg-blue-700 px-8 py-3 rounded-lg font-semibold transition">
                        S'inscrire
                    </Link>
                    <Link :href="route('login')" 
                          class="bg-transparent border-2 border-white hover:bg-white hover:text-gray-900 px-8 py-3 rounded-lg font-semibold transition">
                        Accéder à l'Application
                    </Link>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="about" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-4xl font-bold mb-4">Qu'est-ce que Vehix?</h2>
                    <div class="prose prose-lg max-w-4xl mx-auto">
                        <p>
                            Découvrez VEHIX, l'application innovante qui révolutionne la gestion de votre véhicule 
                            (voitures, motos, camions,...). Accessible sur le web et les mobiles, VEHIX est l'outil 
                            idéal pour les usagers de la route souhaitant suivre et gérer leur bien en temps réel.
                        </p>
                        <p>Avec Véhix, vous pouvez:</p>
                        <ul class="text-left">
                            <li>Suivre l'état de votre véhicule en temps réel</li>
                            <li>Gérer vos dépenses et vos rendez-vous</li>
                            <li>Accéder à des informations personnalisées sur votre véhicule</li>
                        </ul>
                    </div>
                </div>

                <div class="grid md:grid-cols-2 gap-12 items-center mt-16" data-aos="fade-up">
                    <div class="flex justify-center">
                        <img src="/assets/img/logovehix.png" alt="VEHIX" class="max-w-md w-full">
                    </div>
                    
                    <div>
                        <h2 class="text-3xl font-bold mb-6">Fonctionnalités et avantages</h2>
                        <p class="text-lg mb-6 italic">
                            Simplifiez votre gestion automobile en quelques clics :
                        </p>
                        <ul class="space-y-2 mb-6">
                            <li class="flex items-start">
                                <span class="text-blue-600 mr-2">▪</span>
                                Téléchargez l'application "VEHIX" sur votre ordinateur ou smartphone
                            </li>
                            <li class="flex items-start">
                                <span class="text-blue-600 mr-2">▪</span>
                                Inscrivez-vous en ligne en toute sécurité
                            </li>
                            <li class="flex items-start">
                                <span class="text-blue-600 mr-2">▪</span>
                                Ajoutez vos véhicules et propriétaires en quelques étapes
                            </li>
                        </ul>
                        <p class="text-lg font-semibold text-blue-600">C'est gratuit, simple et efficace!</p>
                        
                        <div class="grid md:grid-cols-2 gap-4 mt-8">
                            <ul class="space-y-2">
                                <li class="flex items-start">
                                    <i class="bi bi-chevron-right text-blue-600 mr-2"></i>
                                    <strong>Maîtriser les dépenses</strong>
                                </li>
                                <li class="flex items-start">
                                    <i class="bi bi-chevron-right text-blue-600 mr-2"></i>
                                    <strong>Programmer les maintenances</strong>
                                </li>
                                <li class="flex items-start">
                                    <i class="bi bi-chevron-right text-blue-600 mr-2"></i>
                                    <strong>Contrôler les performances</strong>
                                </li>
                                <li class="flex items-start">
                                    <i class="bi bi-chevron-right text-blue-600 mr-2"></i>
                                    <strong>Diminuer les gaspillages - Economiser</strong>
                                </li>
                            </ul>
                            <ul class="space-y-2">
                                <li class="flex items-start">
                                    <i class="bi bi-chevron-right text-blue-600 mr-2"></i>
                                    <strong>Retrouver les garages spécialisés</strong>
                                </li>
                                <li class="flex items-start">
                                    <i class="bi bi-chevron-right text-blue-600 mr-2"></i>
                                    <strong>Rechercher des vendeurs</strong>
                                </li>
                                <li class="flex items-start">
                                    <i class="bi bi-chevron-right text-blue-600 mr-2"></i>
                                    <strong>Éviter les pannes</strong>
                                </li>
                                <li class="flex items-start">
                                    <i class="bi bi-chevron-right text-blue-600 mr-2"></i>
                                    <strong>Comparer les stations services</strong>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-20 bg-gray-50">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-4xl font-bold mb-4">Services</h2>
                    <p class="text-lg">Plusieurs services sont disponibles dans VEHIX</p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <div class="bg-white p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="100">
                        <div class="text-4xl text-blue-600 mb-4">
                            <i class="bi bi-briefcase"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-3">Tableau de bord et Papiers</h4>
                        <p class="text-gray-600">
                            Vous pouvez voir le Tableau de bord de votre véhicule en ligne ou sur Téléphone.
                            Ajouter les informations de votre véhicule, de son propriétaire, de sa dernière visite technique et de sa dernière assurance.
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="200">
                        <div class="text-4xl text-blue-600 mb-4">
                            <i class="bi bi-card-checklist"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-3">Trajets</h4>
                        <p class="text-gray-600">
                            Mettre votre déplacement dans Vehix et l'application se chargera d'historiser votre déplacement et vous indique l'état de votre véhicule.
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="300">
                        <div class="text-4xl text-blue-600 mb-4">
                            <i class="bi bi-bar-chart"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-3">Ravitaillements</h4>
                        <p class="text-gray-600">
                            Vehix permet de gérer votre ravitaillement en carburant, de suivre les coûts, de voir les consommations.
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="400">
                        <div class="text-4xl text-blue-600 mb-4">
                            <i class="bi bi-binoculars"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-3">Entretiens</h4>
                        <p class="text-gray-600">
                            Vous pouvez gérer ici votre plan d'entretien comme les vidanges, changement de courroie, ...
                            Vous pouvez trouver aussi les garages spécialisés.
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="500">
                        <div class="text-4xl text-blue-600 mb-4">
                            <i class="bi bi-brightness-high"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-3">Pièces</h4>
                        <p class="text-gray-600">
                            La gestion des pièces est un atout majeur. Vous pouvez comparer les marques, les prix, les revendeurs.
                            L'application permet de déterminer l'état de chaque pièce dans votre véhicule.
                        </p>
                    </div>

                    <div class="bg-white p-6 rounded-lg shadow-lg" data-aos="fade-up" data-aos-delay="600">
                        <div class="text-4xl text-blue-600 mb-4">
                            <i class="bi bi-calendar4-week"></i>
                        </div>
                        <h4 class="text-xl font-bold mb-3">Véhicule et Propriétaire</h4>
                        <p class="text-gray-600">
                            Vous pouvez stocker ici les informations essentielles de votre véhicule. En cas de perte, ces informations serviront de base de recherche.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Stats Section -->
        <section id="stats" class="py-20 bg-blue-900 text-white">
            <div class="container mx-auto px-4">
                <div class="grid md:grid-cols-4 gap-8 text-center">
                    <div data-aos="fade-up">
                        <i class="bi bi-emoji-smile text-5xl mb-4"></i>
                        <div class="text-4xl font-bold mb-2">{{ stats?.vehicles || 232 }}</div>
                        <p class="text-lg"><strong>Véhicules et moto enregistrés</strong></p>
                        <p class="text-sm">Merci pour votre confiance</p>
                    </div>
                    <div data-aos="fade-up" data-aos-delay="100">
                        <i class="bi bi-journal-richtext text-5xl mb-4"></i>
                        <div class="text-4xl font-bold mb-2">{{ stats?.projects || 521 }}</div>
                        <p class="text-lg"><strong>Projets</strong></p>
                    </div>
                    <div data-aos="fade-up" data-aos-delay="200">
                        <i class="bi bi-headset text-5xl mb-4"></i>
                        <div class="text-4xl font-bold mb-2">{{ stats?.support_hours || 1453 }}</div>
                        <p class="text-lg"><strong>Heures de Support</strong></p>
                    </div>
                    <div data-aos="fade-up" data-aos-delay="300">
                        <i class="bi bi-people text-5xl mb-4"></i>
                        <div class="text-4xl font-bold mb-2">{{ stats?.workers || 32 }}</div>
                        <p class="text-lg"><strong>Collaborateurs</strong></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section id="contact" class="py-20 bg-white">
            <div class="container mx-auto px-4">
                <div class="text-center mb-12" data-aos="fade-up">
                    <h2 class="text-4xl font-bold mb-4">Contact</h2>
                    <p class="text-lg">
                        VEHIX est un produit conçu et développé par HASNREZIGA Informatique. 
                        N'hésitez pas à nous contacter en cas de besoin!
                    </p>
                </div>

                <div class="grid md:grid-cols-2 gap-12">
                    <div data-aos="fade-up">
                        <div class="space-y-6">
                            <div class="flex items-start">
                                <i class="bi bi-geo-alt text-2xl text-blue-600 mr-4"></i>
                                <div>
                                    <h3 class="font-bold text-lg mb-2">Adresse</h3>
                                    <p>Ambohipo LOT VT 31 C Bis, 101 Antananarivo - Madagascar</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="bi bi-telephone text-2xl text-blue-600 mr-4"></i>
                                <div>
                                    <h3 class="font-bold text-lg mb-2">Téléphone</h3>
                                    <p>+261 34 40 994 35</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <i class="bi bi-envelope text-2xl text-blue-600 mr-4"></i>
                                <div>
                                    <h3 class="font-bold text-lg mb-2">Email</h3>
                                    <p>hasnreziga@gmail.com</p>
                                </div>
                            </div>
                        </div>
                        <div class="mt-6">
                            <iframe 
                                src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3774.0108703217525!2d47.559384074383836!3d-18.930912982244024!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2s!5e0!3m2!1sfr!2smg!4v1761679728125!5m2!1sfr!2smg" 
                                class="w-full h-64 rounded-lg"
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy">
                            </iframe>
                        </div>
                    </div>

                    
                </div>
            </div>
        </section>


</template>