<script setup>
import { ref, onMounted, computed } from 'vue';
import VehiculeTrackingTitle from '@/Components/VehiculeTrackingTitle.vue'
import PartnerCard from '@/Components/PartnerCard.vue'
import PartnerModal from '@/Components/PartnerModal.vue'
import { Head, Link } from '@inertiajs/vue3';


// Catégories de filtres
const categories = ref([
  { key: 'all', label: 'Tous' },
  { key: 'premium', label: 'Premium' },
  { key: 'gold', label: 'Or' },
  { key: 'silver', label: 'Argent' },
  { key: 'bronze', label: 'Bronze' }
])

// Catégorie sélectionnée
const selectedCategory = ref('all')

// Données des partenaires avec catégories et détails
const partners = ref([
  {
    logo: 'assets/img/hasnreziga.png',
    name: 'HASNREZIGA Informatique',
    badge: 'Premium',
    link: 'https://www.hasnreziga.com',
    key: 'hasnreziga',
    category: 'premium',
    details: 'Spécialiste en développement web et IA'
  },
  {
    logo: 'assets/img/hasnreziga.png',
    name: 'Hasnreziga1',
    badge: 'Or',
    link: '#',
    key: 'Test1',
    category: 'gold',
    details: 'Partenaire de premier plan'
  },
  {
    logo: 'assets/img/hasnreziga.png',
    name: 'Hasnreziga2',
    badge: 'Argent',
    link: '#',
    key: 'Test2',
    category: 'silver',
    details: 'Service de qualité assuré'
  },
  {
    logo: 'assets/img/hasnreziga.png',
    name: 'Hasnreziga3',
    badge: 'Bronze',
    link: '#',
    key: 'Test3',
    category: 'bronze',
    details: 'Fiable et professionnel'
  },
  {
    logo: 'assets/img/hasnreziga.png',
    name: 'Hasnreziga4',
    badge: 'Or',
    link: '#',
    key: 'Test4',
    category: 'gold',
    details: 'Partenaire de premier plan'
  },
  {
    logo: 'assets/img/hasnreziga.png',
    name: 'Hasnreziga5',
    badge: 'Argent',
    link: '#',
    key: 'Test5',
    category: 'silver',
    details: 'Service de qualité assuré'
  },
  {
    logo: 'assets/img/hasnreziga.png',
    name: 'Hasnreziga6',
    badge: 'Bronze',
    link: '#',
    key: 'Test6',
    category: 'bronze',
    details: 'Fiable et professionnel'
  },
  {
    logo: 'assets/img/hasnreziga.png',
    name: 'Hasnreziga7',
    badge: 'Standard',
    link: '#',
    key: 'Test7',
    category: 'other',
    details: 'Partenaire de confiance'
  }
])

// Filtrer les partenaires
const filteredPartners = computed(() => {
  if (selectedCategory.value === 'all') {
    return partners.value
  }

  return partners.value.filter(partner =>
    partner.category === selectedCategory.value
  )
})


const partnerModal = ref(null)

const handleOpenModal = (partnerKey) => {
  partnerModal.value.openModal(partnerKey)
}



// État pour le menu mobile
const isMobileMenuOpen = ref(false);

// État pour les témoignages (carousel)
const currentTestimonialStart = ref(0); // Index du premier témoignage visible
const testimonialsToShow = 3; // Nombre de témoignages visibles à l'écran
const testimonials = ref([
  {
    name: 'Saul Goodman',
    role: 'Directeur',
    text: 'Proin iaculis purus consequat sem cure digni ssim donec porttitora entum suscipit rhoncus. Accusantium quam, ultricies eget id, aliquam eget nibh et. Maecen aliquam, risus at semper.',
    image: 'assets/img/testimonials/hasnreziga.png'
  },
  {
    name: 'Sara Wilsson',
    role: 'Commercante',
    text: 'Export tempor illum tamen malis malis eram quae irure esse labore quem cillum quid cillum eram malis quorum velit fore eram velit sunt aliqua noster fugiat irure amet legam anim culpa.',
    image: 'assets/img/testimonials/hasnreziga.png'
  },
  {
    name: 'RANDRIA Jean Karlis',
    role: 'Transporteur',
    text: 'Enim nisi quem export duis labore cillum quae magna enim sint quorum nulla quem veniam duis minim tempor labore quem eram duis noster aute amet eram fore quis sint minim.',
    image: 'assets/img/testimonials/hasnreziga.png'
  },
  {
    name: 'Matt Brandon',
    role: 'Entrepreneur',
    text: 'Fugiat enim eram quae cillum dolore dolor amet nulla culpa multos export minim fugiat dolor enim duis veniam ipsum anim magna sunt elit fore quem dolore labore illum veniam.',
    image: 'assets/img/testimonials/hasnreziga.png'
  },
  {
    name: 'John Larson',
    role: 'Entrepreneur',
    text: 'Quis quorum aliqua sint quem legam fore sunt eram irure aliqua veniam tempor noster veniam sunt culpa nulla illum cillum fugiat legam esse veniam culpa fore nisi cillum quid.',
    image: 'assets/img/testimonials/hasnreziga.png'
  },
  {
    name: 'Marie Dubois',
    role: 'Ingénieur',
    text: 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.',
    image: 'assets/img/testimonials/hasnreziga.png'
  }
]);

// Calculer le nombre maximum de positions possibles
const maxPosition = computed(() => {
  if (!testimonials.value || testimonials.value.length <= testimonialsToShow) {
    return 0;
  }
  return testimonials.value.length - testimonialsToShow;
});

// Navigation carousel témoignages
const nextTestimonial = () => {
  if (currentTestimonialStart.value < maxPosition.value) {
    currentTestimonialStart.value++;
  }
};

const prevTestimonial = () => {
  if (currentTestimonialStart.value > 0) {
    currentTestimonialStart.value--;
  }
};

// Auto-rotation des témoignages
let testimonialInterval;
onMounted(() => {
  testimonialInterval = setInterval(() => {
    if (currentTestimonialStart.value < maxPosition.value) {
      currentTestimonialStart.value++;
    } else {
      currentTestimonialStart.value = 0; // Boucler
    }
  }, 5000); // Change toutes les 5 secondes
});

// Calculer le nombre de groupes (pages) possibles
const testimonialGroups = computed(() => {
  if (!testimonials.value || testimonials.value.length <= testimonialsToShow) {
    return 1;
  }
  return testimonials.value.length - testimonialsToShow + 1;
});

// Fonction pour aller à une page spécifique
const goToTestimonial = (index) => {
  if (index >= 0 && index <= maxPosition.value) {
    currentTestimonialStart.value = index;
  }
};

// Cleanup
onUnmounted(() => {
  if (testimonialInterval) {
    clearInterval(testimonialInterval);
  }
});

// Formulaire de contact
const contactForm = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
});

const isSubmitting = ref(false);
const submitMessage = ref('');

// Toggle menu mobile
const toggleMobileMenu = () => {
  isMobileMenuOpen.value = !isMobileMenuOpen.value;
};

// Soumission du formulaire
const submitContact = async () => {
  isSubmitting.value = true;
  submitMessage.value = '';

  try {
    // Ici vous pouvez faire un appel Inertia.post vers votre route Laravel
    // await Inertia.post('/contact', contactForm.value);

    // Simulation pour l'instant
    await new Promise(resolve => setTimeout(resolve, 1000));

    submitMessage.value = 'Votre message est envoyé. Nous vous remercions!';
    contactForm.value = { name: '', email: '', subject: '', message: '' };
  } catch (error) {
    submitMessage.value = 'Une erreur est survenue. Veuillez réessayer.';
  } finally {
    isSubmitting.value = false;
  }
};

// Smooth scroll
const scrollToSection = (sectionId) => {
  const element = document.querySelector(sectionId);
  if (element) {
    element.scrollIntoView({ behavior: 'smooth' });
    isMobileMenuOpen.value = false;
  }
};

// Cleanup
import { onUnmounted } from 'vue';
onUnmounted(() => {
  if (testimonialInterval) {
    clearInterval(testimonialInterval);
  }
});
</script>

<template>
  <div class="min-h-screen bg-gray-50">
    <Head title="Vehix - Vehicle Manager Digital" />

 <!-- Header / Sidebar -->
<header class="fixed left-0 top-0  w-80 bg-[#273628] text-white z-50 hidden xl:flex flex-col origin-top-left" style="transform: scale(0.8); transform-origin: top left;">
  <!-- Mobile Toggle Button -->
  <button
    @click="toggleMobileMenu"
    class="xl:hidden absolute top-4 right-4 text-2xl"
  >
    <i class="bi bi-list"></i>
  </button>

  <!-- Logo Profile -->
  <div class="flex flex-col items-center py-8">
    <div class="w-32 h-32 mb-4">
      <img src="assets/img/logovehix.png" alt="Vehix" class="w-full h-full object-contain">
    </div>
  </div>

  <!-- Social Links -->
  <div class="flex justify-center gap-4 mb-8">
    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-[#4f5c50] hover:bg-[#87CEEB] transition">
      <i class="bi bi-twitter-x"></i>
    </a>
    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-[#4f5c50] hover:bg-[#87CEEB] transition">
      <i class="bi bi-facebook"></i>
    </a>
    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-[#4f5c50] hover:bg-[#87CEEB] transition">
      <i class="bi bi-instagram"></i>
    </a>
    <a href="#" class="w-10 h-10 flex items-center justify-center rounded-full bg-[#4f5c50] hover:bg-[#87CEEB] transition">
      <i class="bi bi-linkedin"></i>
    </a>
  </div>

  <!-- Navigation -->
  <nav class="flex-1 px-6">
    <ul class="space-y-2">
      <li>
        <a @click.prevent="scrollToSection('#hero')" href="#hero"
           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition cursor-pointer">
          <i class="bi bi-house"></i>
          <span>Accueil</span>
        </a>
      </li>
      <li>
        <a @click.prevent="scrollToSection('#about')" href="#about"
           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition cursor-pointer">
          <i class="bi bi-person"></i>
          <span>Qu'est-ce que VEHIX?</span>
        </a>
      </li>
      <li>
        <a @click.prevent="scrollToSection('#services')" href="#services"
           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition cursor-pointer">
          <i class="bi bi-hdd-stack"></i>
          <span>Services</span>
        </a>
      </li>
      <li>
        <a @click.prevent="scrollToSection('#portfolio')" href="#portfolio"
           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition cursor-pointer">
          <i class="bi bi-images"></i>
          <span>Partenaires</span>
        </a>
      </li>
      <li>
        <a @click.prevent="scrollToSection('#contact')" href="#contact"
           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition cursor-pointer">
          <i class="bi bi-envelope"></i>
          <span>Contact</span>
        </a>
      </li>
      <li>
        <a href="/cgu" target="_blank"
           class="flex items-center gap-3 px-4 py-3 rounded-lg hover:bg-gray-800 transition">
          <i class="bi bi-file-text"></i>
          <span>Conditions d'utilisation</span>
        </a>
      </li>
    </ul>
  </nav>

  <!-- Bottom Profile Image -->
  <div class="p-6 flex justify-center">
    <div class="w-24 h-24 rounded-full overflow-hidden">
      <img src="assets/img/hasnreziga.png" alt="Hasnreziga" class="w-full h-full object-cover">
    </div>
  </div>
</header>

<!-- Mobile Header -->
<div class="xl:hidden fixed top-0 left-0 right-0 bg-gray-900 text-white z-50 p-4 flex justify-between items-center">
  <img src="assets/img/logovehix.png" alt="Vehix" class="h-12">
  <button @click="toggleMobileMenu" class="text-2xl">
    <i class="bi bi-list"></i>
  </button>
</div>

<!-- Mobile Menu -->
<div
  v-if="isMobileMenuOpen"
  class="xl:hidden fixed inset-0 bg-gray-900 text-white z-40 p-8 pt-20"
>
  <nav>
    <ul class="space-y-4">
      <li><a @click="scrollToSection('#hero')" href="#hero" class="block py-2">Accueil</a></li>
      <li><a @click="scrollToSection('#about')" href="#about" class="block py-2">Qu'est-ce que VEHIX?</a></li>
      <li><a @click="scrollToSection('#services')" href="#services" class="block py-2">Services</a></li>
      <li><a @click="scrollToSection('#portfolio')" href="#portfolio" class="block py-2">Partenaires</a></li>
      <li><a @click="scrollToSection('#contact')" href="#contact" class="block py-2">Contact</a></li>
      <li><a href="/cgu" target="_blank" class="block py-2">Conditions d'utilisation</a></li>
    </ul>
  </nav>
</div>

    <!-- Main Content -->
    <main class="xl:ml-64 pt-16 xl:pt-0">

      <!-- Hero Section -->
      <section id="hero" class="relative min-h-screen flex items-center justify-center bg-[#000000ce] text-white overflow-hidden">
        <img
          src="assets/img/collage.png"
          alt="Vehix Background"
          class="absolute inset-0 w-full h-full object-cover opacity-70"
        >

        <div class="relative z-10 container mx-auto px-6 ">
          <h1 class="text-4xl md:text-6xl font-bold mb-6 tracking-wider" style="font-family: 'Bitcount Grid Single', monospace;">
            VEHIX - VEHICLE MANAGER DIGITAL
          </h1>
          <p class="text-xl md:text-2xl mb-4">
            CARNET DE BORD NUMERIQUE de votre véhicule
          </p>
          <VehiculeTrackingTitle />

          <div class="flex flex-col sm:flex-row gap-4">
                <Link href="/register"
                        class="px-7 py-2 bg-white hover:bg-[#87CEEB] text-black rounded-lg font-semibold transition duration-300">
                    S'inscrire
                </Link>
                <Link href="/login"
                        class="px-7 py-2 bg-white hover:bg-[#87CEEB] text-black rounded-lg font-semibold transition duration-300">
                    Accéder à l'Application
                </Link>
            </div>


        </div>
      </section>

      <!-- About Section -->
      <section id="about" class="py-20 bg-white">
        <div class="container mx-auto px-6">
          <!-- Section Title -->
          <div class="mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 text-center mb-6">
              Qu'est-ce que Vehix?
            </h2>
            <div class="max-w-4xl mx-auto text-gray-700 space-y-4">
              <p>
                Découvrez VEHIX, l'application innovante qui révolutionne la gestion de votre véhicule (voitures, motos, camions,...).
              </p>
              <p>
                Accessible sur le web et les mobiles, VEHIX est l'outil idéal pour les usagers de la route souhaitant suivre et gérer leur bien en temps réel. Conçu pour les conducteurs exigeants, VEHIX offre une solution complète et intuitive pour une gestion sécurisée et efficace de votre véhicule.
              </p>

              <div class="text-left max-w-2xl mx-auto mt-6">
                <p class="font-semibold mb-3">Avec Véhix, vous pouvez:</p>
                <ul class="space-y-2 ml-6">
                  <li class="flex items-start">
                    <span class="text-blue-600 mr-2">▪</span>
                    <span>suivre l'état de votre véhicule en temps réel</span>
                  </li>
                  <li class="flex items-start">
                    <span class="text-blue-600 mr-2">▪</span>
                    <span>gérer vos dépenses et vos rendez-vous</span>
                  </li>
                  <li class="flex items-start">
                    <span class="text-blue-600 mr-2">▪</span>
                    <span>accéder à des informations personnalisées sur votre véhicule</span>
                  </li>
                </ul>
              </div>

              <p class="mt-6">
                Voyez comment Véhix peut vous aider à optimiser votre expérience de conduite
              </p>
              <p class="font-semibold">
                Utilisez l'application dès aujourd'hui et prenez le contrôle de votre véhicule.
              </p>
            </div>
          </div>

          <!-- Content Grid -->
          <div class="grid lg:grid-cols-2 gap-12 items-center">
            <div class="flex justify-center">
              <img src="assets/img/logovehix.png" alt="VEHIX" class="max-w-sm w-full">
            </div>

            <div>
              <h3 class="text-2xl font-bold text-gray-900 mb-4">
                Fonctionnalités et avantages
              </h3>
              <p class="text-gray-700 italic mb-6">
                Simplifiez votre gestion automobile en quelques clics :
              </p>

              <ul class="space-y-2 mb-6 ml-6">
                <li class="flex items-start">
                  <span class="text-blue-600 mr-2">▪</span>
                  <span>Téléchargez l'application "VEHIX" sur votre ordinateur ou smartphone.</span>
                </li>
                <li class="flex items-start">
                  <span class="text-blue-600 mr-2">▪</span>
                  <span>Inscrivez-vous en ligne en toute sécurité.</span>
                </li>
                <li class="flex items-start">
                  <span class="text-blue-600 mr-2">▪</span>
                  <span>Ajoutez vos véhicules et propriétaires en quelques étapes.</span>
                </li>
              </ul>

              <p class="text-lg font-semibold text-blue-600 mb-6">
                C'est gratuit, simple et efficace!
              </p>

              <div class="grid md:grid-cols-2 gap-6">
                <ul class="space-y-3">
                  <li class="flex items-start">
                    <i class="bi bi-chevron-right text-blue-600 mr-2 mt-1"></i>
                    <span class="font-semibold">Maîtriser les dépenses</span>
                  </li>
                  <li class="flex items-start">
                    <i class="bi bi-chevron-right text-blue-600 mr-2 mt-1"></i>
                    <span class="font-semibold">Programmer les maintenances</span>
                  </li>
                  <li class="flex items-start">
                    <i class="bi bi-chevron-right text-blue-600 mr-2 mt-1"></i>
                    <span class="font-semibold">Contrôler les performances</span>
                  </li>
                  <li class="flex items-start">
                    <i class="bi bi-chevron-right text-blue-600 mr-2 mt-1"></i>
                    <span class="font-semibold">Diminuer les gaspillages - Economiser</span>
                  </li>
                </ul>

                <ul class="space-y-3">
                  <li class="flex items-start">
                    <i class="bi bi-chevron-right text-blue-600 mr-2 mt-1"></i>
                    <span class="font-semibold">Retrouver les garages spécialisés</span>
                  </li>
                  <li class="flex items-start">
                    <i class="bi bi-chevron-right text-blue-600 mr-2 mt-1"></i>
                    <span class="font-semibold">Rechercher des vendeurs</span>
                  </li>
                  <li class="flex items-start">
                    <i class="bi bi-chevron-right text-blue-600 mr-2 mt-1"></i>
                    <span class="font-semibold">Eviter les pannes</span>
                  </li>
                  <li class="flex items-start">
                    <i class="bi bi-chevron-right text-blue-600 mr-2 mt-1"></i>
                    <span class="font-semibold">Comparer les stations services</span>
                  </li>
                </ul>
              </div>

              <p class="mt-6 text-gray-700">
                Par ailleurs, le concepteur,
                <a href="https://hasnreziga.mg" target="_blank" class="text-blue-600 hover:underline">
                  HASNREZIGA Informatique
                </a>,
                est une entreprise experte en informatique et dans le domaine de l'intelligence artificielle, garantissant ainsi une solution de gestion automobile de haute qualité.
              </p>
            </div>
          </div>
        </div>
      </section>

        <!-- Services Section -->
        <section id="services" class="py-20 bg-gray-50">
            <div class="container mx-auto px-6">
                <div class="text-center mb-16">
                    <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Services</h2>
                    <p class="text-gray-600">
                        Nous vous offrons des services de qualité pour vous aider à gérer votre véhicule
                    </p>
                </div>

                <!-- Services 1 à 6 en 3 colonnes -->
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8 mb-8">
                    <!-- Service 1 -->
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        <div class="flex items-center mb-6">
                            <i class="bi bi-speedometer2 text-3xl text-blue-600 mr-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-0">Tableau de bord et Papiers</h3>
                        </div>
                        <div class="text-gray-600 space-y-2">
                            <div>Vous pouvez voir le Tableau de bord de votre véhicule en ligne ou sur Téléphone.</div>
                            <div>Ajouter les informations de votre véhicule, de son propriétaire,
                                de sa dernière visite technique et de sa dernière assurance.
                            </div>
                        </div>
                    </div>

                    <!-- Service 2 -->
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        <div class="flex items-center mb-6">
                            <i class="bi bi-signpost-split text-3xl text-green-600 mr-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-0">Trajets</h3>
                        </div>
                        <div class="text-gray-600 space-y-2">
                            <div>Mettre votre déplacement dans Vehix et l'application se chargera d'historiser
                                votre déplacement et vous indique l'état de votre véhicule.
                            </div>
                        </div>
                    </div>

                    <!-- Service 3 -->
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        <div class="flex items-center mb-6">
                            <i class="bi bi-fuel-pump text-3xl text-purple-600 mr-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-0">Ravitaillements</h3>
                        </div>
                        <div class="text-gray-600 space-y-2">
                            <div>Vehix permet de gérer votre ravitaillement en carburant,
                                de suivre les coûts, de voir les consommations.
                            </div>
                        </div>
                    </div>

                    <!-- Service 4 -->
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        <div class="flex items-center mb-6">
                            <i class="bi bi-tools text-3xl text-red-600 mr-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-0">Entretiens</h3>
                        </div>
                        <div class="text-gray-600 space-y-2">
                            <div>Vous pouvez gerer ici votre plan d'entretien comme les vidanges, changement de courroie, ...</div>
                            <div>Vous pouvez trouver aussi les garages spécialisés dans la réalisation des diverses entretiens.</div>
                        </div>
                    </div>

                    <!-- Service 5 -->
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        <div class="flex items-center mb-6">
                            <i class="bi bi-gear text-3xl text-yellow-600 mr-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-0">Pièces</h3>
                        </div>
                        <div class="text-gray-600 space-y-2">
                            <div>La gestion des pièces est un atout majeur dans l'utilisation de Vehix.</div>
                            <div>Vous pouvez comparer les marques, les prix, les revendeurs, ...</div>
                            <div>En plus, l'application permet de determiner l'état de chaque pièce dans votre véhicule.</div>
                        </div>
                    </div>

                    <!-- Service 6 -->
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        <div class="flex items-center mb-6">
                            <i class="bi bi-person-badge text-3xl text-indigo-600 mr-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-0">Vehicule et Propriétaire</h3>
                        </div>
                        <div class="text-gray-600 space-y-2">
                            <div>Vous pouvez stocker ici les informations essentielles de votre véhicule.</div>
                            <div>En cas de perte, ces informations serviront de base de recherche.</div>
                        </div>
                    </div>
                </div>

                <!-- Services 7 à 10 en 4 colonnes -->
                <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Service 7 -->
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        <div class="flex items-center mb-6">
                            <i class="bi bi-car-front text-3xl text-blue-600 mr-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-0">232+</h3>
                        </div>
                        <div class="text-gray-600 space-y-2">
                            <div>Véhicules et moto enregistrés Merci pour votre confiance</div>
                        </div>
                    </div>

                    <!-- Service 8 -->
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        <div class="flex items-center mb-6">
                            <i class="bi bi-folder text-3xl text-green-600 mr-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-0">521</h3>
                        </div>
                        <div class="text-gray-600 space-y-2">
                            <div>Projects adipisci atque cum quia aut</div>
                        </div>
                    </div>

                    <!-- Service 9 -->
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        <div class="flex items-center mb-6">
                            <i class="bi bi-headset text-3xl text-purple-600 mr-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-0">1 453</h3>
                        </div>
                        <div class="text-gray-600 space-y-2">
                            <div>Hours Of Support aut commodi quaerat</div>
                        </div>
                    </div>

                    <!-- Service 10 -->
                    <div class="bg-white p-8 rounded-lg shadow-md hover:shadow-xl transition">
                        <div class="flex items-center mb-6">
                            <i class="bi bi-person-workspace text-3xl text-red-600 mr-4"></i>
                            <h3 class="text-xl font-bold text-gray-900 mb-0">32</h3>
                        </div>
                        <div class="text-gray-600 space-y-2">
                            <div>Hard Workers rerum asperiores dolor</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

      <!-- Portfolio/Partners Section -->
      <section id="portfolio" class="py-20 bg-white">
        <div class="container mx-auto px-6">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Nos Partenaires</h2>
            <p class="text-gray-600">
              Ils nous font confiance pour la gestion de leurs véhicules
            </p>
          </div>

            <!-- Filtres -->
                <div class="flex flex-wrap justify-center gap-2 mb-8">
                    <button
                    v-for="category in categories"
                    :key="category.key"
                    @click="selectedCategory = category.key"
                    :class="[
                        'px-4 py-2 rounded-full transition-colors',
                        selectedCategory === category.key
                        ? 'bg-blue-600 text-white'
                        : 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                    ]"
                    >
                    {{ category.label }}
                    </button>
                </div>

            <!-- Cartes des partenaires -->
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            <PartnerCard
                v-for="(partner, index) in filteredPartners"
                :key="index"
                :logo-src="partner.logo"
                :name="partner.name"
                :badge="partner.badge"
                :link="partner.link"
                :partner-key="partner.key"
                :details="partner.details"
                @open-modal="handleOpenModal"
            />
            </div>


        </div>

        <!-- Modal -->
        <PartnerModal ref="partnerModal" />
      </section>

      <!-- Testimonials Section -->
      <section id="testimonials" class="py-20 bg-gray-50">
        <div class="container mx-auto px-6">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Avis</h2>
            <p class="text-gray-600">Ce que disent nos utilisateurs</p>
          </div>

          <div class="max-w-7xl mx-auto">
            <!-- Conteneur du carrousel -->
            <div class="relative overflow-hidden">
              <!-- Conteneur extérieur pour limiter la hauteur -->
              <div class="h-[400px] relative">
                <!-- Conteneur pour tous les témoignages -->
                <div 
                  class="flex transition-transform duration-500 ease-in-out h-full"
                  :style="{ transform: `translateX(-${currentTestimonialStart * (100 / testimonialsToShow)}%)` }"
                >
                  <div 
                    v-for="(testimonial, index) in testimonials" 
                    :key="index"
                    class="min-w-[calc(100%/3)] px-4"
                  >
                    <div class="bg-white p-6 rounded-lg shadow-lg relative h-full flex flex-col">
                      <!-- Quote Icon -->
                      <div class="text-4xl text-blue-200 mb-4">
                        <i class="bi bi-quote"></i>
                      </div>

                      <!-- Testimonial Content -->
                      <div class="text-center flex-grow">
                        <p class="text-gray-700 text-sm mb-6 italic">
                          {{ testimonial.text }}
                        </p>

                        <div class="flex flex-col items-center">
                          <img
                            :src="testimonial.image"
                            :alt="testimonial.name"
                            class="w-16 h-16 rounded-full object-cover mb-3"
                          >
                          <h4 class="font-bold text-gray-900 text-base">
                            {{ testimonial.name }}
                          </h4>
                          <p class="text-gray-600 text-sm">
                            {{ testimonial.role }}
                          </p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Boutons de navigation sur les côtés -->
                <button
                  @click="prevTestimonial"
                  :disabled="currentTestimonialStart === 0"
                  class="absolute left-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition disabled:opacity-50 z-10"
                >
                  <i class="bi bi-chevron-left"></i>
                </button>
                
                <button
                  @click="nextTestimonial"
                  :disabled="currentTestimonialStart >= maxPosition"
                  class="absolute right-4 top-1/2 transform -translate-y-1/2 w-10 h-10 bg-blue-600 hover:bg-blue-700 text-white rounded-full flex items-center justify-center transition disabled:opacity-50 z-10"
                >
                  <i class="bi bi-chevron-right"></i>
                </button>
              </div>
            </div>

            <!-- Pagination Dots en bas au centre -->
            <div class="flex justify-center gap-2 mt-6">
              <div 
                v-for="(_, index) in testimonialGroups" 
                :key="index"
                @click="goToTestimonial(index)"
                :class="[
                  'w-3 h-3 rounded-full cursor-pointer transition',
                  currentTestimonialStart === index ? 'bg-blue-600' : 'bg-gray-300 hover:bg-gray-400'
                ]"
              ></div>
            </div>
          </div>
        </div>
      </section>

      <!-- Contact Section -->
      <section id="contact" class="py-20 bg-white">
        <div class="container mx-auto px-6">
          <div class="text-center mb-16">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Contact</h2>
            <p class="text-gray-600">
              VEHIX est un produit conçu et développé par HASNREZIGA Informatique. N'hésitez pas à nous contacter en cas de besoin!
            </p>
          </div>

          <div class="grid lg:grid-cols-2 gap-12">
            <!-- Contact Info -->
            <div class="space-y-8">
              <!-- Address -->
              <div class="flex gap-4">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                  <i class="bi bi-geo-alt text-blue-600 text-xl"></i>
                </div>
                <div>
                  <h3 class="font-bold text-gray-900 mb-2">Adresse</h3>
                  <p class="text-gray-600">Ambohipo LOT VT 31 C Bis, 101 Antananarivo - Madagascar</p>
                </div>
              </div>

              <!-- Phone -->
              <div class="flex gap-4">
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center flex-shrink-0">
                  <i class="bi bi-telephone text-green-600 text-xl"></i>
                </div>
                <div>
                  <h3 class="font-bold text-gray-900 mb-2">Téléphone</h3>
                  <p class="text-gray-600">+261 34 40 994 35</p>
                </div>
              </div>

              <!-- Email -->
              <div class="flex gap-4">
                <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center flex-shrink-0">
                  <i class="bi bi-envelope text-purple-600 text-xl"></i>
                </div>
                <div>
                  <h3 class="font-bold text-gray-900 mb-2">Email</h3>
                  <p class="text-gray-600">contact@hasnreziga.mg</p>
                </div>
              </div>

              <!-- Map -->
              <div class="rounded-lg overflow-hidden shadow-lg h-64">
                <iframe
                  src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d3774.0108703217525!2d47.559384074383836!3d-18.930912982244024!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2s!5e0!3m2!1sfr!2smg!4v1761679728125!5m2!1sfr!2smg"
                  width="100%"
                  height="100%"
                  style="border:0;"
                  allowfullscreen=""
                  loading="lazy"
                  referrerpolicy="no-referrer-when-downgrade"
                  class="w-full h-full"
                ></iframe>
              </div>
            </div>

            <!-- Contact Form -->
            <div class="bg-gray-50 p-8 rounded-lg">
              <form @submit.prevent="submitContact" class="space-y-6">
                <div class="grid md:grid-cols-2 gap-6">
                  <div>
                    <label for="name" class="block text-gray-700 font-semibold mb-2">
                      Votre Nom
                    </label>
                    <input
                      type="text"
                      id="name"
                      v-model="contactForm.name"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    >
                  </div>

                  <div>
                    <label for="email" class="block text-gray-700 font-semibold mb-2">
                      Votre Email
                    </label>
                    <input
                      type="email"
                      id="email"
                      v-model="contactForm.email"
                      required
                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                    >
                  </div>
                </div>

                <div>
                  <label for="subject" class="block text-gray-700 font-semibold mb-2">
                    Sujet
                  </label>
                  <input
                    type="text"
                    id="subject"
                    v-model="contactForm.subject"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition"
                  >
                </div>

                <div>
                  <label for="message" class="block text-gray-700 font-semibold mb-2">
                    Message
                  </label>
                  <textarea
                    id="message"
                    v-model="contactForm.message"
                    rows="6"
                    required
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent outline-none transition resize-none"
                  ></textarea>
                </div>

                <!-- Submit Messages -->
                <div v-if="isSubmitting" class="text-center text-gray-600">
                  Envoi en cours...
                </div>
                <div v-if="submitMessage" class="text-center text-green-600 font-semibold">
                  {{ submitMessage }}
                </div>

                <div class="text-center">
                  <button
                    type="submit"
                    :disabled="isSubmitting"
                    class="px-8 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-400 text-white rounded-lg font-semibold transition"
                  >
                    Envoyer Message
                  </button>
                </div>
              </form>
            </div>
          </div>
        </div>
      </section>
    </main>

    <!-- Footer -->
    <footer class="bg-gray-100 py-8 xl:ml-80">
      <div class="container mx-auto px-6 text-center">
        <p class="text-gray-700 mb-2">
          © <span class="font-semibold">Vehix</span> - All Rights Reserved
        </p>
        <p class="text-gray-600 text-sm">
          Designed by <a href="https://www.hasnreziga.mg/" target="_blank" class="text-blue-600 hover:underline">HASNREZIGA</a>
        </p>
      </div>
    </footer>

    <!-- Scroll to Top Button -->
    <button
      @click="scrollToSection('#hero')"
      class="fixed bottom-8 right-8 w-12 h-12 bg-[#4f5c50] hover:bg-[#eb912b] text-white rounded-full flex items-center justify-center shadow-lg transition z-40"
    >
      <i class="bi bi-arrow-up-short text-2xl"></i>
    </button>
  </div>
</template>

<style scoped>
/* Vous pouvez ajouter des styles personnalisés ici si nécessaire */
</style>
