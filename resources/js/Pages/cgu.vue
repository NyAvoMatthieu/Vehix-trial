<script setup>
import { ref, onMounted } from 'vue';
import { Head, Link} from '@inertiajs/vue3';

// Importation de AOS pour les animations
import AOS from 'aos';
import 'aos/dist/aos.css';

// Initialisation de AOS
onMounted(() => {
  AOS.init({
    duration: 800,
    easing: 'ease-in-out',
    once: true,
    offset: 100,
  });
});

// État pour le menu mobile
const isMobileMenuOpen = ref(false);

// État pour la section active (pour la navigation sticky)
const activeSection = ref('objet');

// Sections du CGU
const sections = ref([
  { id: 'objet', title: '1. Objet', icon: 'bi-file-text' },
  { id: 'acces', title: '2. Accès au service', icon: 'bi-unlock' },
  { id: 'utilisation', title: '3. Utilisation du service', icon: 'bi-check-circle' },
  { id: 'donnees-vehicules', title: '4. Données sur les véhicules', icon: 'bi-car-front' },
  { id: 'donnees-personnelles', title: '5. Données personnelles', icon: 'bi-shield-lock' },
  { id: 'responsabilite', title: '6. Responsabilité', icon: 'bi-exclamation-triangle' },
  { id: 'modification', title: '7. Modification des CGU', icon: 'bi-pencil-square' },
  { id: 'droit-applicable', title: '8. Droit applicable', icon: 'bi-scales' }
]);

// Fonction pour scroll vers une section
const scrollToSection = (sectionId) => {
  const element = document.getElementById(sectionId);
  if (element) {
    const offset = 100;
    const elementPosition = element.getBoundingClientRect().top;
    const offsetPosition = elementPosition + window.pageYOffset - offset;

    window.scrollTo({
      top: offsetPosition,
      behavior: 'smooth'
    });

    activeSection.value = sectionId;
    isMobileMenuOpen.value = false;
  }
};

// Observer pour mettre à jour la section active
onMounted(() => {
  const observerOptions = {
    root: null,
    rootMargin: '-100px 0px -70% 0px',
    threshold: 0
  };

  const observerCallback = (entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        activeSection.value = entry.target.id;
      }
    });
  };

  const observer = new IntersectionObserver(observerCallback, observerOptions);

  sections.value.forEach(section => {
    const element = document.getElementById(section.id);
    if (element) {
      observer.observe(element);
    }
  });
});

// Date de dernière mise à jour
const lastUpdated = ref('30 Janvier 2026');
</script>

<template>
  <div>
    <Head title="Conditions Générales d'Utilisation - VEHIX" />

    <!-- Navigation Header -->
    <header class="fixed top-0 left-0 right-0 bg-white shadow-md z-50">
      <nav class="container mx-auto px-6 py-4">
        <div class="flex items-center justify-between">
          <!-- Logo -->
          <Link href="/" class="flex items-center gap-3">
            <img src="assets/img/logovehix.png" alt="VEHIX Logo" class="h-10 w-10">
            <span class="text-2xl font-bold text-gray-900">VEHIX</span>
          </Link>

          <!-- Desktop Navigation -->
          <div class="hidden md:flex items-center gap-6">
            <Link href="/" class="text-gray-700 hover:text-[#eb912b] transition font-medium">
              <i class="bi bi-house mr-2"></i>Accueil
            </Link>
            <Link href="/#about" class="text-gray-700 hover:text-[#eb912b] transition font-medium">
              À propos
            </Link>
            <Link href="/#contact" class="text-gray-700 hover:text-[#eb912b] transition font-medium">
              Contact
            </Link>
          </div>

          <!-- Mobile Menu Button -->
          <button
            @click="isMobileMenuOpen = !isMobileMenuOpen"
            class="md:hidden text-gray-700 hover:text-[#eb912b] transition"
          >
            <i :class="isMobileMenuOpen ? 'bi bi-x-lg' : 'bi bi-list'" class="text-2xl"></i>
          </button>
        </div>

        <!-- Mobile Menu -->
        <div
          v-show="isMobileMenuOpen"
          class="md:hidden mt-4 pb-4 border-t border-gray-200"
        >
          <div class="flex flex-col gap-4 mt-4">
            <Link href="/" class="text-gray-700 hover:text-[#eb912b] transition font-medium">
              <i class="bi bi-house mr-2"></i>Accueil
            </Link>
            <Link href="/#about" class="text-gray-700 hover:text-[#eb912b] transition font-medium">
              À propos
            </Link>
            <Link href="/#contact" class="text-gray-700 hover:text-[#eb912b] transition font-medium">
              Contact
            </Link>
          </div>
        </div>
      </nav>
    </header>

    <!-- Hero Section -->
    <section class="pt-32 pb-16 bg-[#273628]">
      <div class="container mx-auto px-6">
        <div class="text-center text-white" data-aos="fade-up">
          <div class="inline-flex items-center justify-center w-20 h-20 bg-white/10 rounded-full mb-6">
            <i class="bi bi-file-earmark-text text-5xl"></i>
          </div>
          <h1 class="text-4xl md:text-5xl font-bold mb-4">
            Conditions Générales d'Utilisation
          </h1>
          <p class="text-xl text-blue-100 mb-6">
            VEHIX - Votre gestionnaire de véhicules intelligent
          </p>
          <p class="text-sm text-blue-200">
            Dernière mise à jour : {{ lastUpdated }}
          </p>
        </div>
      </div>
    </section>

    <!-- Main Content -->
    <div class="container mx-auto px-6 py-12">
      <div class="grid lg:grid-cols-4 gap-8">
        <!-- Sidebar Navigation (Desktop) -->
        <aside class="hidden lg:block lg:col-span-1">
          <div class="sticky top-24">
            <div class="bg-white rounded-lg shadow-lg p-6">
              <h3 class="text-lg font-bold text-gray-900 mb-4">Navigation</h3>
              <nav class="space-y-2">
                <button
                  v-for="section in sections"
                  :key="section.id"
                  @click="scrollToSection(section.id)"
                  :class="[
                    'w-full text-left px-4 py-3 rounded-lg transition flex items-center gap-3',
                    activeSection === section.id
                      ? 'bg-[#2736281f] text-[#eb912b] font-semibold'
                      : 'text-gray-700 hover:bg-gray-100'
                  ]"
                >
                  <i :class="section.icon" class="text-lg"></i>
                  <span class="text-sm">{{ section.title }}</span>
                </button>
              </nav>

              <!-- Quick Links -->
              <div class="mt-8 pt-6 border-t border-gray-200">
                <h4 class="text-sm font-bold text-gray-900 mb-3">Liens Utiles</h4>
                <div class="space-y-2">
                  <Link href="/#about" class="block text-sm text-gray-600 hover:text-[#eb912b] transition">
                    <i class="bi bi-info-circle mr-2"></i>À propos
                  </Link>
                  <Link href="/#contact" class="block text-sm text-gray-600 hover:text-[#eb912b] transition">
                    <i class="bi bi-envelope mr-2"></i>Nous contacter
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </aside>

        <!-- Content -->
        <main class="lg:col-span-3">
          <!-- Introduction -->
          <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg mb-8" data-aos="fade-up">
            <div class="flex items-start gap-4">
              <i class="bi bi-info-circle-fill text-blue-600 text-2xl mt-1"></i>
              <div>
                <h3 class="text-lg font-bold text-gray-900 mb-2">Bienvenue sur VEHIX</h3>
                <p class="text-gray-700">
                  Les présentes Conditions Générales d'Utilisation régissent votre utilisation de l'application VEHIX.
                  En utilisant notre service, vous acceptez ces conditions. Veuillez les lire attentivement.
                </p>
              </div>
            </div>
          </div>

          <!-- Section 1: Objet -->
          <section id="objet" class="mb-12 scroll-mt-24" data-aos="fade-up">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                  <i class="bi bi-file-text text-blue-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">1. Objet</h2>
              </div>
              <div class="prose max-w-none text-gray-700">
                <p class="leading-relaxed">
                  Les présentes conditions générales d'utilisation (CGU) ont pour objet de définir les modalités et
                  conditions d'utilisation de l'application <strong class="text-gray-900">VEHIX</strong>, ainsi que les
                  droits et obligations des parties dans ce cadre.
                </p>
                <p class="leading-relaxed mt-4">
                  VEHIX est une plateforme de gestion digitale de véhicules qui permet aux utilisateurs de suivre,
                  gérer et optimiser l'entretien et l'utilisation de leurs véhicules de manière efficace et économique.
                </p>
              </div>
            </div>
          </section>

          <!-- Section 2: Accès au service -->
          <section id="acces" class="mb-12 scroll-mt-24" data-aos="fade-up">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                  <i class="bi bi-unlock text-green-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">2. Accès au service</h2>
              </div>
              <div class="prose max-w-none text-gray-700">
                <p class="leading-relaxed">
                  Le service est accessible gratuitement à tout utilisateur disposant d'un accès à Internet et ayant
                  créé un compte valide sur la plateforme VEHIX.
                </p>

                <div class="mt-6 bg-gradient-to-r from-amber-50 to-orange-50 border border-amber-200 rounded-lg p-6">
                  <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="bi bi-star-fill text-amber-500"></i>
                    Formules d'abonnement
                  </h4>
                  <p class="mb-4">
                    VEHIX propose plusieurs formules pour répondre à tous les besoins :
                  </p>

                  <div class="space-y-4">
                    <!-- Formule Gratuite -->
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                      <div class="flex items-center justify-between mb-2">
                        <h5 class="font-bold text-gray-900">Formule Gratuite</h5>
                        <span class="bg-green-100 text-green-700 text-xs font-semibold px-3 py-1 rounded-full">
                          0 Ar/mois
                        </span>
                      </div>
                      <ul class="text-sm space-y-2 ml-4">
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i>
                          <span>Gestion basique de vos véhicules</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i>
                          <span>Rappels d'entretien</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-green-500 mt-0.5"></i>
                          <span>Jusqu'à 2 véhicules</span>
                        </li>
                      </ul>
                    </div>

                    <!-- Formule Premium -->
                    <div class="bg-white rounded-lg p-4 border-2 border-blue-300">
                      <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-2">
                          <h5 class="font-bold text-gray-900">Formule Premium</h5>
                          <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-2 py-0.5 rounded">
                            Populaire
                          </span>
                        </div>
                        <span class="bg-blue-100 text-blue-700 text-xs font-semibold px-3 py-1 rounded-full">
                          À partir de -- Ar/mois
                        </span>
                      </div>
                      <ul class="text-sm space-y-2 ml-4">
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-blue-500 mt-0.5"></i>
                          <span>Toutes les fonctionnalités gratuites</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-blue-500 mt-0.5"></i>
                          <span>Statistiques avancées et analyses détaillées</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-blue-500 mt-0.5"></i>
                          <span>Historique complet des interventions</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-blue-500 mt-0.5"></i>
                          <span>Véhicules illimités</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-blue-500 mt-0.5"></i>
                          <span>Support prioritaire</span>
                        </li>
                      </ul>
                    </div>

                    <!-- Formule Pro -->
                    <div class="bg-white rounded-lg p-4 border border-purple-200">
                      <div class="flex items-center justify-between mb-2">
                        <h5 class="font-bold text-gray-900">Formule Pro</h5>
                        <span class="bg-purple-100 text-purple-700 text-xs font-semibold px-3 py-1 rounded-full">
                          Sur devis
                        </span>
                      </div>
                      <ul class="text-sm space-y-2 ml-4">
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-purple-500 mt-0.5"></i>
                          <span>Toutes les fonctionnalités Premium</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-purple-500 mt-0.5"></i>
                          <span>Gestion de flotte professionnelle</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-purple-500 mt-0.5"></i>
                          <span>API et intégrations personnalisées</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-purple-500 mt-0.5"></i>
                          <span>Support dédié 24/7</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check-circle-fill text-purple-500 mt-0.5"></i>
                          <span>Formation et accompagnement personnalisé</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <p class="leading-relaxed mt-6">
                  L'utilisateur s'engage à utiliser le service de manière responsable et conforme aux présentes CGU.
                  Tous les frais de connexion et d'équipement nécessaires pour accéder au service sont à la charge
                  exclusive de l'utilisateur.
                </p>
              </div>
            </div>
          </section>

          <!-- Section 3: Utilisation du service -->
          <section id="utilisation" class="mb-12 scroll-mt-24" data-aos="fade-up">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center">
                  <i class="bi bi-check-circle text-purple-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">3. Utilisation du service</h2>
              </div>
              <div class="prose max-w-none text-gray-700">
                <p class="leading-relaxed mb-6">
                  En utilisant VEHIX, l'utilisateur s'engage à respecter les règles suivantes :
                </p>

                <div class="space-y-4">
                  <!-- Règle 1 -->
                  <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <i class="bi bi-shield-x text-red-600"></i>
                    </div>
                    <div>
                      <h4 class="font-bold text-gray-900 mb-2">Usage légal et conforme</h4>
                      <p class="text-sm">
                        Ne pas utiliser le service à des fins illégales, frauduleuses ou contraires à l'ordre public.
                        Toute activité criminelle ou tentative de fraude sera immédiatement signalée aux autorités
                        compétentes.
                      </p>
                    </div>
                  </div>

                  <!-- Règle 2 -->
                  <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <i class="bi bi-clipboard-check text-blue-600"></i>
                    </div>
                    <div>
                      <h4 class="font-bold text-gray-900 mb-2">Informations exactes et à jour</h4>
                      <p class="text-sm">
                        Fournir des informations exactes, précises et à jour concernant vos véhicules pour garantir un
                        suivi juste et conforme. Les données inexactes peuvent compromettre l'efficacité du service et
                        la pertinence des rappels d'entretien.
                      </p>
                    </div>
                  </div>

                  <!-- Règle 3 -->
                  <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="w-8 h-8 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <i class="bi bi-c-circle text-amber-600"></i>
                    </div>
                    <div>
                      <h4 class="font-bold text-gray-900 mb-2">Respect de la propriété intellectuelle</h4>
                      <p class="text-sm">
                        Respecter les droits de propriété intellectuelle de VEHIX et de ses partenaires. Toute
                        reproduction, distribution ou utilisation non autorisée du contenu est strictement interdite.
                      </p>
                    </div>
                  </div>

                  <!-- Règle 4 -->
                  <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <i class="bi bi-heart text-green-600"></i>
                    </div>
                    <div>
                      <h4 class="font-bold text-gray-900 mb-2">Respect de l'éthique et des bonnes mœurs</h4>
                      <p class="text-sm">
                        Ne pas porter atteinte à l'ordre public et aux bonnes mœurs, que ce soit sur l'application ou
                        dans les sondages et retours utilisateurs. Cela inclut le respect des lois, de l'éthique, de la
                        moralité, de la sécurité et du respect d'autrui.
                      </p>
                    </div>
                  </div>

                  <!-- Règle 5 -->
                  <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <div class="w-8 h-8 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mt-1">
                      <i class="bi bi-lock text-indigo-600"></i>
                    </div>
                    <div>
                      <h4 class="font-bold text-gray-900 mb-2">Sécurité du compte</h4>
                      <p class="text-sm">
                        Maintenir la confidentialité de vos identifiants de connexion. Vous êtes responsable de toutes
                        les activités effectuées sous votre compte. En cas de suspicion d'utilisation non autorisée,
                        veuillez nous contacter immédiatement.
                      </p>
                    </div>
                  </div>
                </div>

                <div class="mt-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-lg">
                  <div class="flex items-start gap-3">
                    <i class="bi bi-exclamation-triangle-fill text-red-600 text-xl mt-0.5"></i>
                    <div>
                      <h4 class="font-bold text-red-900 mb-1">Sanctions en cas de non-respect</h4>
                      <p class="text-sm text-red-800">
                        Tout manquement à ces obligations peut entraîner la suspension temporaire ou définitive de
                        votre compte, sans préavis ni remboursement, et pourra donner lieu à des poursuites judiciaires
                        si nécessaire.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Section 4: Données sur les véhicules -->
          <section id="donnees-vehicules" class="mb-12 scroll-mt-24" data-aos="fade-up">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-cyan-100 rounded-lg flex items-center justify-center">
                  <i class="bi bi-car-front text-cyan-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">4. Données sur les véhicules</h2>
              </div>
              <div class="prose max-w-none text-gray-700">
                <p class="leading-relaxed mb-6">
                  Les utilisateurs peuvent enregistrer et partager les données de leurs véhicules sur la plateforme
                  <strong class="text-gray-900">VEHIX</strong> pour bénéficier d'un suivi personnalisé et d'un
                  accompagnement optimal dans la gestion de leur parc automobile.
                </p>

                <div class="grid md:grid-cols-2 gap-4 mb-6">
                  <div class="bg-gradient-to-br from-blue-50 to-cyan-50 p-6 rounded-lg border border-blue-200">
                    <div class="flex items-center gap-3 mb-3">
                      <i class="bi bi-database-fill-check text-blue-600 text-2xl"></i>
                      <h4 class="font-bold text-gray-900">Types de données collectées</h4>
                    </div>
                    <ul class="text-sm space-y-2">
                      <li class="flex items-start gap-2">
                        <i class="bi bi-dot text-blue-600 text-xl"></i>
                        <span>Informations techniques du véhicule (marque, modèle, année)</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-dot text-blue-600 text-xl"></i>
                        <span>Kilométrage et historique d'utilisation</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-dot text-blue-600 text-xl"></i>
                        <span>Historique des entretiens et réparations</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-dot text-blue-600 text-xl"></i>
                        <span>Consommation de carburant</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-dot text-blue-600 text-xl"></i>
                        <span>Documents associés (assurance, contrôle technique)</span>
                      </li>
                    </ul>
                  </div>

                  <div class="bg-gradient-to-br from-green-50 to-emerald-50 p-6 rounded-lg border border-green-200">
                    <div class="flex items-center gap-3 mb-3">
                      <i class="bi bi-shield-check text-green-600 text-2xl"></i>
                      <h4 class="font-bold text-gray-900">Utilisation des données</h4>
                    </div>
                    <ul class="text-sm space-y-2">
                      <li class="flex items-start gap-2">
                        <i class="bi bi-dot text-green-600 text-xl"></i>
                        <span>Suivi personnalisé de vos véhicules</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-dot text-green-600 text-xl"></i>
                        <span>Rappels automatiques d'entretien</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-dot text-green-600 text-xl"></i>
                        <span>Analyses et statistiques de performance</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-dot text-green-600 text-xl"></i>
                        <span>Recommandations personnalisées</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-dot text-green-600 text-xl"></i>
                        <span>Amélioration continue du service</span>
                      </li>
                    </ul>
                  </div>
                </div>

                <div class="bg-amber-50 border border-amber-200 p-6 rounded-lg">
                  <div class="flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-amber-600 text-2xl mt-0.5"></i>
                    <div>
                      <h4 class="font-bold text-gray-900 mb-2">Services partenaires</h4>
                      <p class="text-sm mb-3">
                        VEHIX collabore avec des partenaires de confiance (garages, assureurs, fournisseurs de pièces)
                        pour vous offrir des services complémentaires. Les utilisateurs sont invités à vérifier les
                        conditions de chaque service partenaire avant de s'inscrire.
                      </p>
                      <p class="text-sm text-amber-800 font-semibold">
                        <i class="bi bi-hand-index-thumb mr-2"></i>
                        Vous gardez le contrôle total sur le partage de vos données avec nos partenaires.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Section 5: Données personnelles -->
          <section id="donnees-personnelles" class="mb-12 scroll-mt-24" data-aos="fade-up">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center">
                  <i class="bi bi-shield-lock text-indigo-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">5. Données personnelles</h2>
              </div>
              <div class="prose max-w-none text-gray-700">
                <p class="leading-relaxed mb-6">
                  VEHIX accorde une importance primordiale à la protection de vos données personnelles et s'engage à
                  respecter les réglementations en vigueur, notamment le Règlement Général sur la Protection des
                  Données (RGPD) et les lois malgaches applicables.
                </p>

                <div class="bg-gradient-to-r from-indigo-50 to-purple-50 border-2 border-indigo-200 rounded-lg p-6 mb-6">
                  <div class="flex items-start gap-3 mb-4">
                    <i class="bi bi-database-lock text-indigo-600 text-3xl"></i>
                    <div>
                      <h4 class="font-bold text-gray-900 text-lg mb-2">Traitement des données</h4>
                      <p class="text-sm mb-3">
                        Les données personnelles collectées font l'objet d'un traitement informatique destiné à :
                      </p>
                      <ul class="text-sm space-y-2">
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check2-circle text-indigo-600 mt-0.5"></i>
                          <span>La gestion de votre compte utilisateur</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check2-circle text-indigo-600 mt-0.5"></i>
                          <span>La fourniture et l'amélioration de nos services</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check2-circle text-indigo-600 mt-0.5"></i>
                          <span>La communication d'informations relatives à votre compte</span>
                        </li>
                        <li class="flex items-start gap-2">
                          <i class="bi bi-check2-circle text-indigo-600 mt-0.5"></i>
                          <span>Le respect de nos obligations légales</span>
                        </li>
                      </ul>
                    </div>
                  </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-6">
                  <!-- Vos droits -->
                  <div class="bg-white border-2 border-green-200 rounded-lg p-6">
                    <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                      <i class="bi bi-person-check-fill text-green-600 text-xl"></i>
                      Vos droits RGPD
                    </h4>
                    <ul class="space-y-3 text-sm">
                      <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                          <i class="bi bi-eye text-green-600 text-xs"></i>
                        </div>
                        <div>
                          <strong>Droit d'accès :</strong> Consulter vos données personnelles
                        </div>
                      </li>
                      <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                          <i class="bi bi-pencil text-blue-600 text-xs"></i>
                        </div>
                        <div>
                          <strong>Droit de rectification :</strong> Corriger vos informations
                        </div>
                      </li>
                      <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-red-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                          <i class="bi bi-trash text-red-600 text-xs"></i>
                        </div>
                        <div>
                          <strong>Droit à l'effacement :</strong> Supprimer vos données
                        </div>
                      </li>
                      <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-amber-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                          <i class="bi bi-hand-index text-amber-600 text-xs"></i>
                        </div>
                        <div>
                          <strong>Droit d'opposition :</strong> Vous opposer au traitement
                        </div>
                      </li>
                      <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                          <i class="bi bi-download text-purple-600 text-xs"></i>
                        </div>
                        <div>
                          <strong>Droit à la portabilité :</strong> Récupérer vos données
                        </div>
                      </li>
                      <li class="flex items-start gap-3">
                        <div class="w-6 h-6 bg-indigo-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                          <i class="bi bi-pause-circle text-indigo-600 text-xs"></i>
                        </div>
                        <div>
                          <strong>Droit à la limitation :</strong> Limiter le traitement
                        </div>
                      </li>
                    </ul>
                  </div>

                  <!-- Comment exercer vos droits -->
                  <div class="bg-white border-2 border-blue-200 rounded-lg p-6">
                    <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                      <i class="bi bi-envelope-paper-fill text-blue-600 text-xl"></i>
                      Exercer vos droits
                    </h4>
                    <p class="text-sm mb-4">
                      Pour exercer l'un de ces droits, vous pouvez nous contacter par :
                    </p>
                    <div class="space-y-3">
                      <div class="flex items-start gap-3 text-sm bg-blue-50 p-3 rounded-lg">
                        <i class="bi bi-envelope text-blue-600 mt-0.5"></i>
                        <div>
                          <strong class="block text-gray-900">Email :</strong>
                          <a href="mailto:contact@hasnreziga.mg" class="text-blue-600 hover:underline">
                            contact@hasnreziga.mg
                          </a>
                        </div>
                      </div>
                      <div class="flex items-start gap-3 text-sm bg-blue-50 p-3 rounded-lg">
                        <i class="bi bi-geo-alt text-blue-600 mt-0.5"></i>
                        <div>
                          <strong class="block text-gray-900">Adresse postale :</strong>
                          HASNREZIGA Informatique<br>
                          Ambohipo LOT VT 31 C Bis<br>
                          101 Antananarivo - Madagascar
                        </div>
                      </div>
                    </div>
                    <div class="mt-4 p-3 bg-amber-50 border border-amber-200 rounded-lg">
                      <p class="text-xs text-amber-800">
                        <i class="bi bi-clock-history mr-1"></i>
                        Nous nous engageons à répondre à vos demandes dans un délai maximum de 30 jours.
                      </p>
                    </div>
                  </div>
                </div>

                <div class="bg-blue-50 border-l-4 border-blue-600 p-6 rounded-r-lg">
                  <div class="flex items-start gap-3">
                    <i class="bi bi-shield-fill-check text-blue-600 text-2xl mt-0.5"></i>
                    <div>
                      <h4 class="font-bold text-gray-900 mb-2">Sécurité et conservation</h4>
                      <p class="text-sm text-gray-700 mb-2">
                        Vos données sont stockées de manière sécurisée et ne sont conservées que pendant la durée
                        nécessaire aux finalités pour lesquelles elles ont été collectées, ou conformément aux
                        obligations légales.
                      </p>
                      <p class="text-sm text-gray-700">
                        Nous mettons en œuvre des mesures techniques et organisationnelles appropriées pour garantir
                        la sécurité de vos données contre tout accès, modification, divulgation ou destruction non
                        autorisés.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Section 6: Responsabilité -->
          <section id="responsabilite" class="mb-12 scroll-mt-24" data-aos="fade-up">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center">
                  <i class="bi bi-exclamation-triangle text-orange-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">6. Responsabilité</h2>
              </div>
              <div class="prose max-w-none text-gray-700">
                <p class="leading-relaxed mb-6">
                  L'utilisation de VEHIX se fait sous la responsabilité exclusive de l'utilisateur. Les limitations de
                  responsabilité suivantes s'appliquent :
                </p>

                <div class="space-y-4 mb-6">
                  <!-- Limitation 1 -->
                  <div class="border-l-4 border-orange-500 bg-orange-50 p-5 rounded-r-lg">
                    <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                      <i class="bi bi-laptop text-orange-600"></i>
                      Dommages au matériel
                    </h4>
                    <p class="text-sm">
                      <strong class="text-gray-900">VEHIX</strong> ne saurait être tenu responsable des dommages
                      directs ou indirects causés au matériel de l'utilisateur lors de l'accès au site ou à
                      l'application, résultant notamment de virus, d'erreurs, d'omissions ou de dysfonctionnements.
                    </p>
                  </div>

                  <!-- Limitation 2 -->
                  <div class="border-l-4 border-blue-500 bg-blue-50 p-5 rounded-r-lg">
                    <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                      <i class="bi bi-wifi-off text-blue-600"></i>
                      Disponibilité du service
                    </h4>
                    <p class="text-sm">
                      Bien que nous nous efforcions d'assurer une disponibilité maximale, VEHIX ne garantit pas que le
                      service sera disponible de manière ininterrompue ou sans erreur. Des interruptions peuvent
                      survenir pour des raisons de maintenance, de mises à jour ou de circonstances indépendantes de
                      notre volonté.
                    </p>
                  </div>

                  <!-- Limitation 3 -->
                  <div class="border-l-4 border-purple-500 bg-purple-50 p-5 rounded-r-lg">
                    <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                      <i class="bi bi-database-x text-purple-600"></i>
                      Exactitude des informations
                    </h4>
                    <p class="text-sm mb-2">
                      VEHIX s'efforce de fournir des informations et des rappels exacts, mais ne peut garantir
                      l'exhaustivité ou la précision absolue des données. L'utilisateur reste responsable de :
                    </p>
                    <ul class="text-sm space-y-1 ml-6">
                      <li class="flex items-start gap-2">
                        <i class="bi bi-arrow-right-short text-purple-600 mt-0.5"></i>
                        <span>Vérifier les informations fournies par l'application</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-arrow-right-short text-purple-600 mt-0.5"></i>
                        <span>Maintenir ses véhicules en bon état</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-arrow-right-short text-purple-600 mt-0.5"></i>
                        <span>Respecter les recommandations du constructeur</span>
                      </li>
                    </ul>
                  </div>

                  <!-- Limitation 4 -->
                  <div class="border-l-4 border-green-500 bg-green-50 p-5 rounded-r-lg">
                    <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                      <i class="bi bi-people text-green-600"></i>
                      Services partenaires
                    </h4>
                    <p class="text-sm">
                      VEHIX agit uniquement comme plateforme de mise en relation avec des prestataires partenaires
                      (garages, assureurs, etc.). Nous ne sommes pas responsables de la qualité des prestations
                      fournies par ces partenaires. Tout litige concernant une prestation doit être résolu directement
                      avec le prestataire concerné.
                    </p>
                  </div>

                  <!-- Limitation 5 -->
                  <div class="border-l-4 border-red-500 bg-red-50 p-5 rounded-r-lg">
                    <h4 class="font-bold text-gray-900 mb-2 flex items-center gap-2">
                      <i class="bi bi-person-x text-red-600"></i>
                      Utilisation inappropriée
                    </h4>
                    <p class="text-sm">
                      VEHIX ne peut être tenu responsable de l'utilisation inappropriée du service par l'utilisateur,
                      notamment en cas de non-respect des présentes CGU, de fourniture de données incorrectes ou de
                      négligence dans l'entretien des véhicules.
                    </p>
                  </div>
                </div>

                <div class="bg-gradient-to-r from-gray-100 to-gray-50 border border-gray-300 p-6 rounded-lg">
                  <div class="flex items-start gap-3">
                    <i class="bi bi-info-circle-fill text-gray-600 text-2xl mt-0.5"></i>
                    <div>
                      <h4 class="font-bold text-gray-900 mb-2">Force majeure</h4>
                      <p class="text-sm text-gray-700">
                        VEHIX ne pourra être tenu responsable de tout retard ou inexécution de ses obligations résultant
                        de cas de force majeure tels que définis par la jurisprudence malgache, incluant notamment les
                        catastrophes naturelles, les pannes de réseau, les actes gouvernementaux ou toute autre
                        circonstance échappant à notre contrôle raisonnable.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Section 7: Modification des CGU -->
          <section id="modification" class="mb-12 scroll-mt-24" data-aos="fade-up">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-teal-100 rounded-lg flex items-center justify-center">
                  <i class="bi bi-pencil-square text-teal-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">7. Modification des CGU</h2>
              </div>
              <div class="prose max-w-none text-gray-700">
                <p class="leading-relaxed mb-6">
                  <strong class="text-gray-900">VEHIX</strong> se réserve le droit de modifier les présentes
                  Conditions Générales d'Utilisation à tout moment afin de les adapter aux évolutions du service, aux
                  nouvelles fonctionnalités ou aux changements réglementaires.
                </p>

                <div class="grid md:grid-cols-2 gap-6 mb-6">
                  <!-- Comment nous informons -->
                  <div class="bg-gradient-to-br from-blue-50 to-cyan-50 border border-blue-200 rounded-lg p-6">
                    <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                      <i class="bi bi-bell text-blue-600 text-xl"></i>
                      Notification des modifications
                    </h4>
                    <p class="text-sm mb-4">
                      En cas de modification substantielle, nous vous informerons par :
                    </p>
                    <ul class="space-y-2 text-sm">
                      <li class="flex items-start gap-2">
                        <i class="bi bi-envelope-check text-blue-600 mt-0.5"></i>
                        <span>Email à l'adresse associée à votre compte</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-app-indicator text-blue-600 mt-0.5"></i>
                        <span>Notification dans l'application</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-megaphone text-blue-600 mt-0.5"></i>
                        <span>Bannière d'information sur le site web</span>
                      </li>
                    </ul>
                  </div>

                  <!-- Acceptation des modifications -->
                  <div class="bg-gradient-to-br from-purple-50 to-pink-50 border border-purple-200 rounded-lg p-6">
                    <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                      <i class="bi bi-check-circle text-purple-600 text-xl"></i>
                      Acceptation des modifications
                    </h4>
                    <p class="text-sm mb-4">
                      Les modifications prennent effet :
                    </p>
                    <ul class="space-y-2 text-sm">
                      <li class="flex items-start gap-2">
                        <i class="bi bi-calendar-check text-purple-600 mt-0.5"></i>
                        <span>Dès leur publication sur le site pour les modifications mineures</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-hourglass-split text-purple-600 mt-0.5"></i>
                        <span>30 jours après notification pour les modifications substantielles</span>
                      </li>
                      <li class="flex items-start gap-2">
                        <i class="bi bi-person-check text-purple-600 mt-0.5"></i>
                        <span>Votre utilisation continue vaut acceptation</span>
                      </li>
                    </ul>
                  </div>
                </div>

                <div class="bg-amber-50 border-l-4 border-amber-500 p-6 rounded-r-lg mb-6">
                  <div class="flex items-start gap-3">
                    <i class="bi bi-exclamation-diamond-fill text-amber-600 text-2xl mt-0.5"></i>
                    <div>
                      <h4 class="font-bold text-gray-900 mb-2">Droit de refus</h4>
                      <p class="text-sm text-gray-700 mb-2">
                        Si vous n'acceptez pas les nouvelles conditions, vous disposez du droit de cesser d'utiliser
                        le service et de clôturer votre compte avant l'entrée en vigueur des modifications.
                      </p>
                      <p class="text-sm text-gray-700">
                        Pour clôturer votre compte, rendez-vous dans les paramètres de votre profil ou contactez notre
                        support à <a href="mailto:contact@hasnreziga.mg" class="text-blue-600 hover:underline font-semibold">contact@hasnreziga.mg</a>
                      </p>
                    </div>
                  </div>
                </div>

                <div class="bg-green-50 border border-green-200 p-5 rounded-lg">
                  <div class="flex items-start gap-3">
                    <i class="bi bi-file-earmark-diff text-green-600 text-2xl mt-0.5"></i>
                    <div>
                      <h4 class="font-bold text-gray-900 mb-2">Historique des versions</h4>
                      <p class="text-sm text-gray-700">
                        Nous conservons un historique des versions précédentes des CGU. Vous pouvez consulter les
                        versions antérieures en nous contactant ou en consultant la section "Historique" disponible au
                        bas de cette page.
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </section>

          <!-- Section 8: Droit applicable -->
          <section id="droit-applicable" class="mb-12 scroll-mt-24" data-aos="fade-up">
            <div class="bg-white rounded-lg shadow-lg p-8">
              <div class="flex items-center gap-3 mb-6">
                <div class="w-12 h-12 bg-rose-100 rounded-lg flex items-center justify-center">
                  <i class="bi bi-scales text-rose-600 text-2xl"></i>
                </div>
                <h2 class="text-2xl font-bold text-gray-900">8. Droit applicable</h2>
              </div>
              <div class="prose max-w-none text-gray-700">
                <div class="mb-6">
                  <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="bi bi-geo-alt text-rose-600"></i>
                    Loi applicable
                  </h4>
                  <p class="leading-relaxed">
                    Les présentes Conditions Générales d'Utilisation sont régies par et interprétées conformément au
                    <strong class="text-gray-900">droit malgache</strong>. Elles sont soumises aux dispositions du
                    Code civil malgache, du Code du commerce et de toutes autres réglementations en vigueur à
                    Madagascar.
                  </p>
                </div>

                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg p-6 mb-6">
                  <h4 class="font-bold text-gray-900 mb-4 flex items-center gap-2">
                    <i class="bi bi-building text-blue-600 text-xl"></i>
                    Juridiction compétente
                  </h4>
                  <p class="text-sm mb-4">
                    En cas de litige relatif à l'interprétation, l'exécution ou la validité des présentes CGU, et à
                    défaut de résolution amiable, les tribunaux suivants seront seuls compétents :
                  </p>
                  <div class="bg-white rounded-lg p-4 border border-blue-300">
                    <p class="text-sm font-semibold text-gray-900 mb-2">
                      <i class="bi bi-bank2 text-blue-600 mr-2"></i>
                      Tribunaux de la ville d'Antananarivo, Madagascar
                    </p>
                    <p class="text-xs text-gray-600">
                      Cette clause attributive de juridiction s'applique même en cas de pluralité de défendeurs ou
                      d'appel en garantie.
                    </p>
                  </div>
                </div>

                <div class="grid md:grid-cols-2 gap-4 mb-6">
                  <!-- Résolution amiable -->
                  <div class="bg-green-50 border border-green-200 rounded-lg p-5">
                    <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                      <i class="bi bi-chat-dots text-green-600"></i>
                      Résolution amiable
                    </h4>
                    <p class="text-sm mb-3">
                      Avant tout recours judiciaire, nous vous encourageons à tenter une résolution amiable du litige.
                    </p>
                    <div class="space-y-2">
                      <div class="flex items-start gap-2 text-sm">
                        <i class="bi bi-1-circle-fill text-green-600 mt-0.5"></i>
                        <span>Contactez notre service client</span>
                      </div>
                      <div class="flex items-start gap-2 text-sm">
                        <i class="bi bi-2-circle-fill text-green-600 mt-0.5"></i>
                        <span>Exposez clairement le problème</span>
                      </div>
                      <div class="flex items-start gap-2 text-sm">
                        <i class="bi bi-3-circle-fill text-green-600 mt-0.5"></i>
                        <span>Nous étudierons votre cas sous 15 jours</span>
                      </div>
                    </div>
                  </div>

                  <!-- Médiation -->
                  <div class="bg-purple-50 border border-purple-200 rounded-lg p-5">
                    <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                      <i class="bi bi-people text-purple-600"></i>
                      Médiation
                    </h4>
                    <p class="text-sm mb-3">
                      Si la résolution amiable échoue, vous pouvez faire appel à un médiateur.
                    </p>
                    <div class="bg-white rounded p-3 border border-purple-300 text-sm">
                      <p class="font-semibold text-gray-900 mb-1">
                        Chambre de Commerce et d'Industrie d'Antananarivo
                      </p>
                      <p class="text-xs text-gray-600">
                        Service de médiation et d'arbitrage commercial
                      </p>
                    </div>
                  </div>
                </div>

                <div class="bg-gray-50 border border-gray-300 rounded-lg p-6">
                  <h4 class="font-bold text-gray-900 mb-3 flex items-center gap-2">
                    <i class="bi bi-globe text-gray-600"></i>
                    Utilisateurs internationaux
                  </h4>
                  <p class="text-sm text-gray-700">
                    Si vous accédez à VEHIX depuis l'étranger, vous acceptez que le droit malgache s'applique et que
                    les tribunaux malgaches soient compétents. Cependant, cette clause ne porte pas atteinte aux droits
                    impératifs dont vous pourriez bénéficier en tant que consommateur dans votre pays de résidence.
                  </p>
                </div>
              </div>
            </div>
          </section>

          <!-- Section Contact et Questions -->
          <section class="mb-12" data-aos="fade-up">
            <div class="bg-[#273628] rounded-lg shadow-2xl p-8 text-white">
              <div class="text-center mb-6">
                <i class="bi bi-question-circle text-6xl mb-4 opacity-90"></i>
                <h2 class="text-3xl font-bold mb-3">Des questions sur nos CGU ?</h2>
                <p class="text-blue-100">
                  Notre équipe est là pour vous aider et répondre à toutes vos questions
                </p>
              </div>

              <div class="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                <!-- Email -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 text-center hover:bg-white/20 transition">
                  <i class="bi bi-envelope text-4xl mb-3"></i>
                  <h4 class="font-bold mb-2">Email</h4>
                  <a href="mailto:contact@hasnreziga.mg" class="text-[#eb912b] hover:text-white text-sm">
                    contact@hasnreziga.mg
                  </a>
                </div>

                <!-- Téléphone -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 text-center hover:bg-white/20 transition">
                  <i class="bi bi-telephone text-4xl mb-3"></i>
                  <h4 class="font-bold mb-2">Téléphone</h4>
                  <a href="tel:+261344099435" class="text-[#eb912b] hover:text-white text-sm">
                    +261 34 40 994 35
                  </a>
                </div>

                <!-- Adresse -->
                <div class="bg-white/10 backdrop-blur-sm rounded-lg p-6 text-center hover:bg-white/20 transition">
                  <i class="bi bi-geo-alt text-4xl mb-3"></i>
                  <h4 class="font-bold mb-2">Adresse</h4>
                  <p class="text-[#eb912b] hover:text-white text-sm">
                    Ambohipo LOT VT 31 C Bis<br>
                    101 Antananarivo
                  </p>
                </div>
              </div>

              <div class="text-center mt-8">
                <Link
                  href="/#contact"
                  class="inline-flex items-center gap-2 bg-white text-[#eb912b] px-8 py-3 rounded-lg font-semibold hover:bg-blue-50 transition shadow-lg"
                >
                  <i class="bi bi-chat-left-text"></i>
                  <span>Contactez-nous</span>
                </Link>
              </div>
            </div>
          </section>

          <!-- Footer de la page CGU -->
          <div class="border-t border-gray-200 pt-8" data-aos="fade-up">
            <div class="text-center text-gray-600 text-sm">
              <p class="mb-2">
                <strong class="text-gray-900">VEHIX</strong> - Gestionnaire de véhicules digital
              </p>
              <p class="mb-4">
                Un produit <a href="https://www.hasnreziga.mg/" target="_blank" class="text-blue-600 hover:underline font-semibold">HASNREZIGA Informatique</a>
              </p>
              <div class="flex items-center justify-center gap-4 text-xs">
                <span>Version 1.0</span>
                <span>•</span>
                <span>Dernière mise à jour : {{ lastUpdated }}</span>
                <span>•</span>
                <Link href="/" class="text-blue-600 hover:underline">Retour à l'accueil</Link>
              </div>
            </div>
          </div>
        </main>
      </div>
    </div>

    <!-- Scroll to Top Button -->
    <button
      @click="scrollToSection('objet')"
      class="fixed bottom-8 right-8 w-12 h-12 bg-[#4f5c50] hover:bg-[#eb912b] text-white rounded-full flex items-center justify-center shadow-lg transition z-40"
    >
      <i class="bi bi-arrow-up-short text-2xl"></i>
    </button>
  </div>
</template>

<style scoped>
/* Smooth scroll behavior */
html {
  scroll-behavior: smooth;
}

/* Custom scrollbar for webkit browsers */
::-webkit-scrollbar {
  width: 10px;
}

::-webkit-scrollbar-track {
  background: #f1f1f1;
}

::-webkit-scrollbar-thumb {
  background: #888;
  border-radius: 5px;
}

::-webkit-scrollbar-thumb:hover {
  background: #555;
}

/* Prose styles for better text formatting */
.prose p {
  margin-bottom: 1rem;
}

.prose strong {
  font-weight: 600;
}
</style>
