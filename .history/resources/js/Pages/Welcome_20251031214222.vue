<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AOS from 'aos';
import 'aos/dist/aos.css';
import { ref, onMounted } from 'vue';

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

// Initialize AOS after component is mounted
const typedElement = ref(null);

onMounted(() => {
    // Initialize AOS
    AOS.init({
        duration: 1000,
        once: true,
    });

    // Initialize Typed.js if it's available globally (e.g., via CDN)
    if (typedElement.value && window.Typed) {
        new window.Typed(typedElement.value, {
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
        <img id="background" class="absolute -left-20 top-0 max-w-[877px]" src="https://laravel.com/assets/img/welcome/background.svg" />
        <div class="relative min-h-screen flex flex-col items-center justify-center selection:bg-[#FF2D20] selection:text-white">
            <div class="relative w-full max-w-2xl px-6 lg:max-w-7xl">
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
                        
                        <div  v-if="canLogin" class="flex gap-4 justify-center flex-wrap">
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
                                    S'inscrire
                                </Link>

                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="rounded-md px-3 py-2 text-black ring-1 ring-transparent transition hover:text-black/70 focus:outline-none focus-visible:ring-[#FF2D20] dark:text-white dark:hover:text-white/80 dark:focus-visible:ring-white"
                                >
                                    S'inscrire
                                </Link>
                            </template>

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

                <footer class="py-16 text-center text-sm text-black dark:text-white/70">
                    Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }})
                </footer>
            </div>
        </div>
    </div>
</template>