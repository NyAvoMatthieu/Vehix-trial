<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
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

const mobileMenuOpen = ref(false);
const typedElement = ref(null);
const activeSection = ref('hero');
const currentPartnerIndex = ref(0);
const currentReviewIndex = ref(0);
const activePartnerCategory = ref('tous');

const partners = [
    {
        name: 'Hasnreziga Informatique',
        image: 'assets/img/portfolio/hasnreziga.png',
        description: 'Entreprise spécialisée en Informatique : Maintenance et Administration réseaux/systèmes, Développement d\'Applications, ERP, Analyse des données',
        features: [
            'Solutions informatiques complètes',
            'Développement sur mesure',
            'Support technique dédié'
        ],
        link: 'https://hasnreziga.mg',
        color: 'from-[#72c8d3] to-[#3a494f]',
        category: 'platinium'
    },
    {
        name: 'Garage Premium',
        image: 'assets/img/portfolio/hasnreziga.png',
        description: 'Spécialiste en maintenance automobile et réparation de tous types de véhicules. Service rapide et de qualité avec des pièces d\'origine',
        features: [
            'Maintenance préventive',
            'Diagnostic électronique',
            'Réparations garanties'
        ],
        link: '#',
        color: 'from-[#cf98da] to-[#4e4052]',
        category: 'gold'
    },
    {
        name: 'Station Service Total',
        image: 'assets/img/portfolio/hasnreziga.png',
        description: 'Réseau de stations-service offrant carburants de qualité, services de lavage et boutique. Partenaire de confiance pour vos déplacements',
        features: [
            'Carburants premium',
            'Programme de fidélité',
            'Services 24/7'
        ],
        link: '#',
        color: 'from-[#e4de88] to-[#5e5e47]',
        category: 'silver'
    },
    {
        name: 'Pièces Auto',
        image: 'assets/img/portfolio/hasnreziga.png',
        description: 'Distributeur de pièces détachées automobiles neuves et d\'origine. Stock important et livraison rapide partout à Madagascar',
        features: [
            'Large catalogue',
            'Prix compétitifs',
            'Livraison rapide'
        ],
        link: '#',
        color: 'from-[#7af77e] to-[#4d6a4e]',
        category: 'bronze'
    },
    {
        name: 'Auto Excellence',
        image: 'assets/img/portfolio/hasnreziga.png',
        description: 'Centre automobile de référence offrant une gamme complète de services premium pour votre véhicule',
        features: [
            'Service VIP',
            'Garantie étendue',
            'Expertise certifiée'
        ],
        link: '#',
        color: 'from-[#72c8d3] to-[#3a494f]',
        category: 'platinium'
    },
    {
        name: 'Meca Pro',
        image: 'assets/img/portfolio/hasnreziga.png',
        description: 'Atelier mécanique spécialisé dans la réparation et l\'entretien de véhicules toutes marques',
        features: [
            'Équipe qualifiée',
            'Devis gratuit',
            'Pièces d\'origine'
        ],
        link: '#',
        color: 'from-[#cf98da] to-[#4e4052]',
        category: 'gold'
    }
];

const reviews = [
    {
        name: 'RAKOTOARISOA Hasina Patrick',
        role: 'CEO & Founder HASNREZIGA',
        avatar: 'assets/img/testimonials/hasina.png',
        rating: 5,
        comment: 'Tsy voatery "Harena mandany harena" ny fiara na ny moto anananao.</br>Cet outil va diminuer les gaspillages en termes de gestion de votre véhicule.',
        date: 'Il y a 2 jours'
    },
    {
        name: 'Marie Rasolofo',
        role: 'Entreprise de transport',
        avatar: 'https://ui-avatars.com/api/?name=Marie+Rasolofo&background=cf98da&color=fff&size=100',
        rating: 5,
        comment: 'Interface intuitive et fonctionnalités complètes. Le rapport coût/bénéfice est excellent. Je recommande vivement à tous les gestionnaires de véhicules.',
        date: 'Il y a 5 jours'
    },
    {
        name: 'Paul Andria',
        role: 'Chauffeur indépendant',
        avatar: 'https://ui-avatars.com/api/?name=Paul+Andria&background=e4de88&color=fff&size=100',
        rating: 5,
        comment: 'Grâce à VEHIX, je garde un historique complet de mes dépenses et de mes trajets. C\'est devenu indispensable pour mon activité !',
        date: 'Il y a 1 semaine'
    },
    {
        name: 'Sophie Rabe',
        role: 'Responsable logistique',
        avatar: 'https://ui-avatars.com/api/?name=Sophie+Rabe&background=7af77e&color=fff&size=100',
        rating: 5,
        comment: 'L\'application m\'aide à optimiser les coûts de carburant et à planifier les maintenances. Support client très réactif !',
        date: 'Il y a 1 semaine'
    },
    {
        name: 'David Raharison',
        role: 'Entrepreneur',
        avatar: 'https://ui-avatars.com/api/?name=David+Raharison&background=72c8d3&color=fff&size=100',
        rating: 5,
        comment: 'Excellente solution pour gérer mes 3 véhicules professionnels. Les rapports détaillés m\'aident dans ma comptabilité.',
        date: 'Il y a 2 semaines'
    },
    {
        name: 'Lina Razafindra',
        role: 'Conductrice VTC',
        avatar: 'https://ui-avatars.com/api/?name=Lina+Razafindra&background=cf98da&color=fff&size=100',
        rating: 5,
        comment: 'Application facile à utiliser même en conduisant. La fonctionnalité de suivi des trajets est parfaite pour mon activité VTC.',
        date: 'Il y a 2 semaines'
    },
    {
        name: 'Thomas Randria',
        role: 'Gérant de taxi',
        avatar: 'https://ui-avatars.com/api/?name=Thomas+Randria&background=e4de88&color=fff&size=100',
        rating: 4,
        comment: 'Très bon outil de gestion. J\'apprécie particulièrement le calendrier de maintenance qui m\'évite les oublis coûteux.',
        date: 'Il y a 3 semaines'
    },
    {
        name: 'Nadia Heriniaina',
        role: 'Responsable parc auto',
        avatar: 'https://ui-avatars.com/api/?name=Nadia+Heriniaina&background=7af77e&color=fff&size=100',
        rating: 5,
        comment: 'VEHIX nous permet de gérer efficacement notre parc de 50 véhicules. Gain de temps considérable au quotidien !',
        date: 'Il y a 3 semaines'
    }
];


const filteredPartners = () => {
    if (activePartnerCategory.value === 'tous') {
        return partners;
    }
    return partners.filter(partner => partner.category === activePartnerCategory.value);
};

const setPartnerCategory = (category) => {
    activePartnerCategory.value = category;
    currentPartnerIndex.value = 0;
};

const getCategoryBadgeColor = (category) => {
    const colors = {
        platinium: 'from-slate-400 to-slate-600',
        gold: 'from-yellow-400 to-yellow-600',
        silver: 'from-slate-300 to-slate-400',
        bronze: 'from-orange-400 to-orange-600'
    };
    return colors[category] || 'from-slate-400 to-slate-600';
};

const getCategoryIcon = (category) => {
    const icons = {
        platinium: 'bi-star-fill',
        gold: 'bi-award-fill',
        silver: 'bi-gem',
        bronze: 'bi-trophy-fill'
    };
    return icons[category] || 'bi-circle-fill';
};

// Modifiez aussi vos fonctions nextPartner et prevPartner existantes :
const nextPartner = () => {
    const filtered = filteredPartners();
    currentPartnerIndex.value = (currentPartnerIndex.value + 1) % filtered.length;
};

const prevPartner = () => {
    const filtered = filteredPartners();
    currentPartnerIndex.value = (currentPartnerIndex.value - 1 + filtered.length) % filtered.length;
};


const goToPartner = (index) => {
    currentPartnerIndex.value = index;
};

const nextReview = () => {
    currentReviewIndex.value = (currentReviewIndex.value + 1) % reviews.length;
};

const prevReview = () => {
    currentReviewIndex.value = (currentReviewIndex.value - 1 + reviews.length) % reviews.length;
};

const getVisibleReviews = () => {
    const visible = [];
    for (let i = 0; i < 3; i++) {
        visible.push(reviews[(currentReviewIndex.value + i) % reviews.length]);
    }
    return visible;
};


//  variables réactives du formulaire
const contactForm = ref({
    name: '',
    email: '',
    subject: '',
    message: ''
});

const contactFormLoading = ref(false);
const contactFormMessage = ref({ type: '', text: '' });

// Fonction pour soumettre le formulaire
const submitContactForm = (e) => {
    e.preventDefault();

    contactFormMessage.value = { type: '', text: '' };

    if (!contactForm.value.name || !contactForm.value.email || !contactForm.value.subject || !contactForm.value.message) {
        contactFormMessage.value = {
            type: 'error',
            text: 'Veuillez remplir tous les champs'
        };
        return;
    }

    contactFormLoading.value = true;

    router.post(route('contact.store'), contactForm.value, {
        preserveScroll: true,
        onSuccess: (response) => {
            contactFormMessage.value = {
                type: 'success',
                text: 'Votre message a été envoyé avec succès ! Nous vous répondrons dans les plus brefs délais.'
            };

            contactForm.value = {
                name: '',
                email: '',
                subject: '',
                message: ''
            };
        },
        onError: (errors) => {
            const firstError = Object.values(errors)[0];
            contactFormMessage.value = {
                type: 'error',
                text: firstError || 'Une erreur est survenue'
            };
        },
        onFinish: () => {
            contactFormLoading.value = false;
        }
    });
};
onMounted(() => {
    // ========== GESTION DU SCROLL POUR LE SIDEBAR ==========
    const handleScroll = () => {
        const scrollPosition = window.scrollY + 150; // Offset pour une meilleure détection

        const sectionIds = ['hero', 'about', 'services', 'portfolio', 'reviews', 'contact'];
        
        // Parcourir les sections de la dernière à la première
        for (let i = sectionIds.length - 1; i >= 0; i--) {
            const section = document.querySelector(`#${sectionIds[i]}`);
            if (section) {
                const sectionTop = section.offsetTop;
                const sectionHeight = section.offsetHeight;
                const sectionBottom = sectionTop + sectionHeight;
                
                // Vérifier si on est dans la section
                if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
                    activeSection.value = sectionIds[i];
                    break;
                }
            }
        }
    };

    // Ajouter l'écouteur de scroll
    window.addEventListener('scroll', handleScroll);
    handleScroll(); // Appel initial pour définir la section active au chargement

     // ========== TYPED.JS  ==========
    if (typedElement.value && window.Typed) {
        new window.Typed(typedElement.value, {
            strings: ["d'état de votre véhicule", 'de Maintenance', 'de dépenses', 'de circulabilité'],
            typeSpeed: 100,
            backSpeed: 50,
            loop: true,
        });
    }

    // Smooth scroll with active section tracking
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
                mobileMenuOpen.value = false;
            }
        });
    });

    /* Intersection Observer for active section
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                activeSection.value = entry.target.id;
            }
        });
    }, { threshold: 0.5 });

    document.querySelectorAll('section[id]').forEach(section => {
        observer.observe(section);
    });*/

    // ========== ANIMATE ON SCROLL ==========
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    document.querySelectorAll('.animate-on-scroll').forEach(el => {
        observer.observe(el);
    });

    // Auto-slide reviews every 5 seconds
    setInterval(() => {
        nextReview();
    }, 5000);
});

const toggleMobileMenu = () => {
    mobileMenuOpen.value = !mobileMenuOpen.value;
};

const isActive = (section) => activeSection.value === section;
</script>

<template>
    <Head title="VEHIX - Vehicle Manager Digital">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
        
    </Head>

    <div class="bg-gradient-to-br from-slate-50 to-slate-100 text-gray-900 min-h-screen">
        <!-- Desktop Sidebar -->
        <aside class="hidden xl:flex fixed left-0 top-0 bottom-0 w-80 bg-gradient-to-r from-[#273628] to-[#3a4f3d] text-white z-50 flex-col shadow-2xl">
            <!-- Logo Section -->
            <div class="p-8 flex justify-center items-center border-b border-slate-700/50">
                <div class="relative">
                    <div class="absolute inset-0 bg-[#72c8d3]/20 blur-xl rounded-full"></div>
                    <img src="assets/img/logovehix.png" alt="VEHIX Logo" class="w-32 h-32 object-contain relative z-10 drop-shadow-2xl">
                </div>
            </div>

            <!-- Social Links -->
            <div class="flex justify-center gap-3 py-6 border-b border-slate-700/50">
                <a href="#" class="group relative w-11 h-11 rounded-xl bg-slate-800/50 hover:bg-gradient-to-br hover:from-[#72c8d3] hover:to-[#3a494f] flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-[#72c8d3]/50">
                    <i class="bi bi-twitter-x text-lg group-hover:scale-110 transition-transform"></i>
                </a>
                <a href="https://web.facebook.com/people/VEHIX/61583880714830/" class="group relative w-11 h-11 rounded-xl bg-slate-800/50 hover:bg-gradient-to-br hover:from-[#72c8d3] hover:to-[#3a494f] flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-[#72c8d3]/50">
                    <i class="bi bi-facebook text-lg group-hover:scale-110 transition-transform"></i>
                </a>
                <a href="#" class="group relative w-11 h-11 rounded-xl bg-slate-800/50 hover:bg-gradient-to-br hover:from-[#cf98da] hover:to-[#4e4052] flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-[#cf98da]/50">
                    <i class="bi bi-instagram text-lg group-hover:scale-110 transition-transform"></i>
                </a>
                <a href="#" class="group relative w-11 h-11 rounded-xl bg-slate-800/50 hover:bg-gradient-to-br hover:from-[#72c8d3] hover:to-[#3a494f] flex items-center justify-center transition-all duration-300 hover:scale-110 hover:shadow-lg hover:shadow-[#72c8d3]/50">
                    <i class="bi bi-linkedin text-lg group-hover:scale-110 transition-transform"></i>
                </a>
            </div>

            <!-- Navigation -->
            <nav class="flex-1 px-4 py-6 overflow-y-auto custom-scrollbar">
                <ul class="space-y-2">
                    <li>
                        <a href="#hero" :class="isActive('hero') ? 'bg-gradient-to-r from-[#72c8d3] to-[#3a494f] shadow-lg shadow-[#72c8d3]/30 translate-x-1' : 'hover:bg-slate-800/50 hover:translate-x-1'" class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#72c8d3]/0 via-[#72c8d3]/10 to-[#72c8d3]/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                            <div :class="isActive('hero') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-[#72c8d3]/30'" class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300 relative z-10">
                                <i class="bi bi-house-door text-lg group-hover:scale-110 transition-transform"></i>
                            </div>
                            <span class="font-medium relative z-10">Accueil</span>
                            <div v-if="isActive('hero')" class="ml-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#about" :class="isActive('about') ? 'bg-gradient-to-r from-[#72c8d3] to-[#3a494f] shadow-lg shadow-[#72c8d3]/30 translate-x-1' : 'hover:bg-slate-800/50 hover:translate-x-1'" class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#72c8d3]/0 via-[#72c8d3]/10 to-[#72c8d3]/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                            <div :class="isActive('about') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-[#72c8d3]/30'" class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300 relative z-10">
                                <i class="bi bi-info-circle text-lg group-hover:scale-110 transition-transform"></i>
                            </div>
                            <span class="font-medium relative z-10 text-sm">Qu'est-ce que VEHIX?</span>
                            <div v-if="isActive('about')" class="ml-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#services" :class="isActive('services') ? 'bg-gradient-to-r from-[#72c8d3] to-[#3a494f] shadow-lg shadow-[#72c8d3]/30 translate-x-1' : 'hover:bg-slate-800/50 hover:translate-x-1'" class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#72c8d3]/0 via-[#72c8d3]/10 to-[#72c8d3]/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                            <div :class="isActive('services') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-[#72c8d3]/30'" class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300 relative z-10">
                                <i class="bi bi-grid-3x3-gap text-lg group-hover:scale-110 transition-transform"></i>
                            </div>
                            <span class="font-medium relative z-10">Services</span>
                            <div v-if="isActive('services')" class="ml-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#portfolio" :class="isActive('portfolio') ? 'bg-gradient-to-r from-[#72c8d3] to-[#3a494f] shadow-lg shadow-[#72c8d3]/30 translate-x-1' : 'hover:bg-slate-800/50 hover:translate-x-1'" class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#72c8d3]/0 via-[#72c8d3]/10 to-[#72c8d3]/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                            <div :class="isActive('portfolio') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-[#72c8d3]/30'" class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300 relative z-10">
                                <i class="bi bi-people text-lg group-hover:scale-110 transition-transform"></i>
                            </div>
                            <span class="font-medium relative z-10">Partenaires</span>
                            <div v-if="isActive('portfolio')" class="ml-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#reviews" :class="isActive('reviews') ? 'bg-gradient-to-r from-[#72c8d3] to-[#3a494f] shadow-lg shadow-[#72c8d3]/30 translate-x-1' : 'hover:bg-slate-800/50 hover:translate-x-1'" class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#72c8d3]/0 via-[#72c8d3]/10 to-[#72c8d3]/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                            <div :class="isActive('reviews') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-[#72c8d3]/30'" class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300 relative z-10">
                                <i class="bi bi-star-fill text-lg group-hover:scale-110 transition-transform"></i>
                            </div>
                            <span class="font-medium relative z-10">Témoignages</span>
                            <div v-if="isActive('reviews')" class="ml-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                        </a>
                    </li>
                    <li>
                        <a href="#contact" :class="isActive('contact') ? 'bg-gradient-to-r from-[#72c8d3] to-[#3a494f] shadow-lg shadow-[#72c8d3]/30 translate-x-1' : 'hover:bg-slate-800/50 hover:translate-x-1'" class="group relative flex items-center gap-3 px-4 py-3.5 rounded-xl transition-all duration-300 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-r from-[#72c8d3]/0 via-[#72c8d3]/10 to-[#72c8d3]/0 translate-x-[-100%] group-hover:translate-x-[100%] transition-transform duration-700"></div>
                            <div :class="isActive('contact') ? 'bg-white/20' : 'bg-slate-700/50 group-hover:bg-[#72c8d3]/30'" class="w-10 h-10 rounded-lg flex items-center justify-center transition-all duration-300 relative z-10">
                                <i class="bi bi-envelope text-lg group-hover:scale-110 transition-transform"></i>
                            </div>
                            <span class="font-medium relative z-10">Contact</span>
                            <div v-if="isActive('contact')" class="ml-auto w-1.5 h-1.5 rounded-full bg-white animate-pulse"></div>
                        </a>
                    </li>
                </ul>
            </nav>

            <!-- Footer Logo -->
            <div class="p-6 border-t border-slate-700/50 flex justify-center">
                <img src="assets/img/hasnreziga.png" alt="HASNREZIGA" class="w-20 h-20 rounded-2xl object-cover shadow-xl ring-4 ring-slate-700/50 hover:ring-cyan-500/50 transition-all duration-300">
            </div>
        </aside>

        <!-- Mobile Header -->
        <div class="xl:hidden fixed top-0 left-0 right-0 bg-[#273628]/95 backdrop-blur-lg text-white z-50 px-4 py-3 flex items-center justify-between shadow-xl">
            <img src="assets/img/logovehix.png" alt="VEHIX" class="h-12">
            <button @click="toggleMobileMenu" class="text-3xl p-2 hover:bg-slate-800/50 rounded-lg transition-colors">
                <i :class="mobileMenuOpen ? 'bi bi-x-lg' : 'bi bi-list'"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <transition
            enter-active-class="transition-all duration-300"
            enter-from-class="opacity-0 -translate-y-4"
            enter-to-class="opacity-100 translate-y-0"
            leave-active-class="transition-all duration-200"
            leave-from-class="opacity-100 translate-y-0"
            leave-to-class="opacity-0 -translate-y-4"
        >
            <div v-if="mobileMenuOpen" class="xl:hidden fixed inset-0 bg-[#273628]/98 backdrop-blur-xl text-black z-40 pt-20 overflow-y-auto">
                <nav class="p-6">
                    <ul class="space-y-3">
                        <li><a href="#hero" class="block px-6 py-4 hover:bg-slate-800/50 rounded-xl transition-all font-medium text-lg"><i class="bi bi-house-door mr-3"></i>Accueil</a></li>
                        <li><a href="#about" class="block px-6 py-4 hover:bg-slate-800/50 rounded-xl transition-all font-medium text-lg"><i class="bi bi-info-circle mr-3"></i>Qu'est-ce que VEHIX?</a></li>
                        <li><a href="#services" class="block px-6 py-4 hover:bg-slate-800/50 rounded-xl transition-all font-medium text-lg"><i class="bi bi-grid-3x3-gap mr-3"></i>Services</a></li>
                        <li><a href="#portfolio" class="block px-6 py-4 hover:bg-slate-800/50 rounded-xl transition-all font-medium text-lg"><i class="bi bi-people mr-3"></i>Partenaires</a></li>
                        <li><a href="#reviews" class="block px-6 py-4 hover:bg-slate-800/50 rounded-xl transition-all font-medium text-lg"><i class="bi bi-star-fill mr-3"></i>Témoignages</a></li>
                        <li><a href="#contact" class="block px-6 py-4 hover:bg-slate-800/50 rounded-xl transition-all font-medium text-lg"><i class="bi bi-envelope mr-3"></i>Contact</a></li>
                    </ul>
                </nav>
            </div>
        </transition>

        <!-- Main Content -->
        <main class="xl:ml-80">
            <!-- Hero Section -->
            <section id="hero" class="relative min-h-screen flex items-center justify-center bg-gradient-to-br from-[#273628] via-[#3a494f] to-slate-900 text-white overflow-hidden">
                <!-- Background Effects -->
                <div class="absolute inset-0 bg-[url('assets/img/collage.png')] bg-cover bg-center opacity-35"></div>
                <div class="absolute inset-0 bg-gradient-to-b from-transparent via-[#273628]/50 to-[#273628]"></div>

                <!-- Animated Background Shapes -->
                <div class="absolute top-20 left-20 w-72 h-72 bg-[#72c8d3]/10 rounded-full blur-3xl animate-pulse"></div>
                <div class="absolute bottom-20 right-20 w-96 h-96 bg-[#cf98da]/10 rounded-full blur-3xl animate-pulse delay-1000"></div>

                <div class="container mx-auto px-6 z-10 text-center py-20">
                    <div class="max-w-5xl mx-auto space-y-8">
                        <!-- Badge
                        <div class="inline-flex items-center gap-2 px-4 py-2 bg-[#72c8d3]/10 backdrop-blur-sm border border-[#72c8d3]/20 rounded-full text-[#72c8d3] text-sm font-medium">
                            <span class="relative flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#72c8d3] opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#72c8d3]"></span>
                            </span>
                            Nouvelle génération de gestion automobile
                        </div> -->

                        <h1 class="text-5xl md:text-6xl lg:text-7xl font-bold mb-6 tracking-tight leading-tight">
                            <span class="bg-gradient-to-r from-[#72c8d3] via-[#cf98da] to-[#e4de88] bg-clip-text text-transparent">VEHIX</span>
                            <br>
                            <span class="text-3xl md:text-4xl lg:text-5xl font-semibold text-slate-300">Vehicle Manager Digital</span>
                        </h1>

                        <p class="text-xl md:text-2xl text-slate-300 font-light">
                            Carnet de bord numérique de votre véhicule
                        </p>

                        <div class="flex items-center justify-center gap-2 text-lg md:text-xl text-[#72c8d3]">
                            <span class="text-slate-400">Application de Suivi</span>
                            <span ref="typedElement" class="font-semibold text-[#72c8d3]"></span>
                        </div>

                        <div v-if="canLogin" class="flex gap-4 justify-center flex-wrap pt-6">
                            <Link
                                v-if="$page.props.auth.user"
                                :href="route('dashboard')"
                                class="group relative px-8 py-4 bg-gradient-to-r from-[#72d3b3] to-[#3a494f] rounded-xl font-semibold transition-all duration-300 hover:shadow-2xl hover:shadow-[#72c8d3]/50 hover:scale-105"
                            >
                                <span class="relative z-10 flex items-center gap-2">
                                    <i class="bi bi-speedometer2"></i>
                                    Tableau de bord
                                </span>
                            </Link>

                            <template v-else>
                                <Link
                                    :href="route('login')"
                                    class="group relative px-8 py-4 bg-gradient-to-r from-[#72d3b3] to-[#3a494f] rounded-xl font-semibold transition-all duration-300 hover:shadow-2xl hover:shadow-[#72c8d3]/50 hover:scale-105"
                                >
                                    <span class="relative z-10 flex items-center gap-2">
                                        <i class="bi bi-box-arrow-in-right"></i>
                                        Accéder à l'Application
                                    </span>
                                </Link>

                                <Link
                                    v-if="canRegister"
                                    :href="route('register')"
                                    class="group relative px-8 py-4 bg-slate-800/50 backdrop-blur-sm border-2 border-slate-700 hover:border-[#72c8d3] rounded-xl font-semibold transition-all duration-300 hover:bg-slate-800 hover:scale-105"
                                >
                                    <span class="relative z-10 flex items-center gap-2">
                                        <i class="bi bi-person-plus"></i>
                                        S'inscrire gratuitement
                                    </span>
                                </Link>
                            </template>
                        </div>

                        <!-- Stats Preview -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 pt-12 max-w-4xl mx-auto">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-[#72c8d3]">232+</div>
                                <div class="text-sm text-slate-400 mt-1">Véhicules</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-[#72c8d3]">10</div>
                                <div class="text-sm text-slate-400 mt-1">Garage spécialisés</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-[#72c8d3]">50</div>
                                <div class="text-sm text-slate-400 mt-1">Marques</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-[#72c8d3]">202</div>
                                <div class="text-sm text-slate-400 mt-1">Utilisateurs</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Scroll Indicator -->
                <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 animate-bounce">
                    <i class="bi bi-chevron-down text-3xl text-[#72c8d3]"></i>
                </div>
            </section>

            <!-- About Section -->
            <section id="about" class="py-24 bg-white">
                <div class="container mx-auto px-6 animate-on-scroll">
                    <div class="text-center mb-16">
                        <span class="inline-block px-4 py-2 bg-[#72c8d3]/20 text-[#3a494f] rounded-full text-sm font-semibold mb-4">À PROPOS</span>
                        <h2 class="text-4xl md:text-5xl font-bold mb-6 text-slate-900">Qu'est-ce que Vehix?</h2>
                        <div class="max-w-3xl mx-auto text-lg text-slate-600 space-y-4">
                            <p class="leading-relaxed">
                                Découvrez VEHIX, l'application innovante qui révolutionne la gestion de votre véhicule
                                (voitures, motos, camions,...). Accessible sur le web et les mobiles, VEHIX est l'outil
                                idéal pour les usagers de la route souhaitant suivre et gérer leur bien en temps réel.
                            </p>
                        </div>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-16 items-center mt-16">
                        <!-- Image Side -->
                        <div class="relative">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#72c8d3] to-[#3a494f] rounded-3xl transform rotate-3"></div>
                            <div class="relative bg-white p-8 rounded-3xl shadow-2xl">
                                <img src="assets/img/logovehix.png" alt="VEHIX" class="w-full">
                            </div>
                        </div>

                        <!-- Content Side -->
                        <div class="space-y-8">
                            <div>
                                <h3 class="text-3xl font-bold mb-6 text-slate-900">Fonctionnalités et avantages</h3>
                                <p class="text-lg text-slate-600 italic mb-6">
                                    Simplifiez votre gestion automobile en quelques clics :
                                </p>
                            </div>

                            <!-- Features List -->
                            <div class="space-y-4">
                                <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#72c8d3] to-[#3a494f] flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-download text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-slate-900 mb-1">Installation Simple</h4>
                                        <p class="text-slate-600">Téléchargez l'application sur votre ordinateur ou smartphone</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#72c8d3] to-[#3a494f] flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-shield-check text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-slate-900 mb-1">Sécurité Maximale</h4>
                                        <p class="text-slate-600">Inscrivez-vous en ligne en toute sécurité</p>
                                    </div>
                                </div>

                                <div class="flex items-start gap-4 p-4 rounded-xl bg-slate-50 hover:bg-slate-100 transition-colors">
                                    <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-[#72c8d3] to-[#3a494f] flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-car-front text-white"></i>
                                    </div>
                                    <div>
                                        <h4 class="font-semibold text-slate-900 mb-1">Gestion Complète</h4>
                                        <p class="text-slate-600">Ajoutez vos véhicules et propriétaires en quelques étapes</p>
                                    </div>
                                </div>
                            </div>

                            <div class="p-6 bg-gradient-to-r from-[#72c8d3]/10 to-[#3a494f]/10 rounded-2xl border border-[#72c8d3]/30">
                                <p class="text-xl font-bold text-[#3a494f] text-center">
                                    <i class="bi bi-check-circle-fill mr-2 text-[#72c8d3]"></i>
                                    C'est gratuit, simple et efficace!
                                </p>
                            </div>

                            <!-- Benefits Grid -->
                            <div class="grid md:grid-cols-2 gap-4 pt-4">
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-[#72c8d3]"></i>
                                        <span class="font-medium text-slate-700">Maîtriser les dépenses</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-[#72c8d3]"></i>
                                        <span class="font-medium text-slate-700">Programmer les maintenances</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-[#72c8d3]"></i>
                                        <span class="font-medium text-slate-700">Contrôler les performances</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-[#72c8d3]"></i>
                                        <span class="font-medium text-slate-700">Économiser efficacement</span>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-[#72c8d3]"></i>
                                        <span class="font-medium text-slate-700">Trouver des garages</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-[#72c8d3]"></i>
                                        <span class="font-medium text-slate-700">Rechercher des vendeurs</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-[#72c8d3]"></i>
                                        <span class="font-medium text-slate-700">Éviter les pannes</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <i class="bi bi-check-circle-fill text-[#72c8d3]"></i>
                                        <span class="font-medium text-slate-700">Comparer les stations</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Services Section -->
            <section id="services" class="py-24 bg-gradient-to-br from-slate-50 to-slate-100">
                <div class="container mx-auto px-6">
                    <div class="text-center mb-16">
                        <span class="inline-block px-4 py-2 bg-[#72c8d3]/20 text-[#3a494f] rounded-full text-sm font-semibold mb-4">NOS SERVICES</span>
                        <h2 class="text-4xl md:text-5xl font-bold mb-6 text-slate-900">Services VEHIX</h2>
                        <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                            Plusieurs services sont disponibles pour optimiser la gestion de votre véhicule
                        </p>
                    </div>

                    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                        <!-- Service Card 1 - Voitures -->
                        <div class="group relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#72c8d3]/5 to-[#3a494f]/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#72c8d3] to-[#3a494f] flex items-center justify-center text-3xl text-white mb-6 group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                                    </svg>
                                </div>
                                <h4 class="text-xl font-bold mb-3 text-slate-900">Tableau de bord et Papiers</h4>
                                <p class="text-slate-600 leading-relaxed">
                                    Visualisez le tableau de bord de votre véhicule en ligne ou sur mobile. Ajoutez toutes les informations importantes.
                                </p>
                            </div>
                        </div>

                        <!-- Service Card 2 - Documentation -->
                        <div class="group relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#cf98da]/5 to-[#4e4052]/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#cf98da] to-[#4e4052] flex items-center justify-center text-3xl text-white mb-6 group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.5 4.5c-1.95 0-4.05.4-5.5 1.5-1.45-1.1-3.55-1.5-5.5-1.5-1.45 0-2.99.22-4.28.79C1.49 5.62 1 6.33 1 7.14v11.28c0 1.3 1.22 2.26 2.48 1.94.98-.25 2.02-.36 3.02-.36 1.56 0 3.22.26 4.56.92.6.3 1.28.3 1.88 0 1.34-.67 3-.92 4.56-.92 1 0 2.04.11 3.02.36 1.26.33 2.48-.63 2.48-1.94V7.14c0-.81-.49-1.52-1.22-1.85-1.29-.57-2.83-.79-4.28-.79zM21 17.23c0 .63-.58 1.09-1.2.98-.75-.14-1.53-.2-2.3-.2-1.7 0-4.15.65-5.5 1.5V8c1.35-.85 3.8-1.5 5.5-1.5.92 0 1.83.09 2.7.28.46.1.8.51.8.98v9.47z"/>
                                    </svg>
                                </div>
                                <h4 class="text-xl font-bold mb-3 text-slate-900">Trajets</h4>
                                <p class="text-slate-600 leading-relaxed">
                                    Enregistrez vos déplacements et l'application historisera automatiquement vos trajets et l'état de votre véhicule.
                                </p>
                            </div>
                        </div>

                        <!-- Service Card 3 - Logistique -->
                        <div class="group relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#e4de88]/5 to-[#5e5e47]/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#e4de88] to-[#5e5e47] flex items-center justify-center text-3xl text-white mb-6 group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M20 8h-3V4H3c-1.1 0-2 .9-2 2v11h2c0 1.66 1.34 3 3 3s3-1.34 3-3h6c0 1.66 1.34 3 3 3s3-1.34 3-3h2v-5l-3-4zM6 18.5c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zm13.5-9l1.96 2.5H17V9.5h2.5zm-1.5 9c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5z"/>
                                    </svg>
                                </div>
                                <h4 class="text-xl font-bold mb-3 text-slate-900">Ravitaillements</h4>
                                <p class="text-slate-600 leading-relaxed">
                                    Gérez votre ravitaillement en carburant, suivez les coûts et analysez votre consommation en détail.
                                </p>
                            </div>
                        </div>

                        <!-- Service Card 4 - Maintenance -->
                        <div class="group relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#7af77e]/5 to-[#4d6a4e]/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#7af77e] to-[#4d6a4e] flex items-center justify-center text-3xl text-white mb-6 group-hover:scale-110 transition-transform">
                                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2L4 5v6.09c0 5.05 3.41 9.76 8 10.91 4.59-1.15 8-5.86 8-10.91V5l-8-3zm6 9.09c0 4-2.55 7.7-6 8.83-3.45-1.13-6-4.82-6-8.83V6.31l6-2.12 6 2.12v4.78z"/>
                                        <path d="M10.23 14.83L7.4 12l-1.41 1.41L10.23 18 18 10.23 16.59 8.82z"/>
                                    </svg>
                                </div>
                                <h4 class="text-xl font-bold mb-3 text-slate-900">Entretiens</h4>
                                <p class="text-slate-600 leading-relaxed">
                                    Gérez votre plan d'entretien comme les vidanges, changement de courroie. Trouvez des garages spécialisés.
                                </p>
                            </div>
                        </div>

                        <!-- Service Card 5 - Pièces -->
                        <div class="group relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#72c8d3]/5 to-[#3a494f]/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#72c8d3] to-[#3a494f] flex items-center justify-center text-3xl text-white mb-6 group-hover:scale-110 transition-transform">
                                    <i class="bi bi-gear-fill"></i>
                                </div>
                                <h4 class="text-xl font-bold mb-3 text-slate-900">Pièces</h4>
                                <p class="text-slate-600 leading-relaxed">
                                    Comparez les marques, les prix, les revendeurs. Déterminez l'état de chaque pièce de votre véhicule.
                                </p>
                            </div>
                        </div>

                        <!-- Service Card 6 - Véhicule -->
                        <div class="group relative bg-white p-8 rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 hover:-translate-y-2 border border-slate-200">
                            <div class="absolute inset-0 bg-gradient-to-br from-[#cf98da]/5 to-[#4e4052]/5 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity"></div>
                            <div class="relative">
                                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-[#cf98da] to-[#4e4052] flex items-center justify-center text-3xl text-white mb-6 group-hover:scale-110 transition-transform">
                                    <i class="bi bi-card-list"></i>
                                </div>
                                <h4 class="text-xl font-bold mb-3 text-slate-900">Véhicule et Propriétaire</h4>
                                <p class="text-slate-600 leading-relaxed">
                                    Stockez les informations essentielles de votre véhicule. Données sécurisées en cas de perte ou vol.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            
            <!-- Portfolio/Partners Section -->
            <section id="portfolio" class="py-24 bg-white">
                <div class="container mx-auto px-6">
                    <div class="text-center mb-16">
                        <span class="inline-block px-4 py-2 bg-[#72c8d3]/20 text-[#3a494f] rounded-full text-sm font-semibold mb-4">PARTENAIRES</span>
                        <h2 class="text-4xl md:text-5xl font-bold mb-6 text-slate-900">Nos Partenaires</h2>
                        <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                            Découvrez les différents partenaires qui soutiennent cette application
                        </p>
                    </div>

                    <!-- Onglets de catégories -->
                    <div class="flex justify-center mb-12">
                        <div class="inline-flex bg-slate-100 rounded-2xl p-2 gap-2 flex-wrap">
                            <button
                                @click="setPartnerCategory('tous')"
                                :class="activePartnerCategory === 'tous'
                                    ? 'bg-white shadow-lg text-slate-900'
                                    : 'text-slate-600 hover:text-slate-900'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2"
                            >
                                <i class="bi bi-grid-3x3-gap"></i>
                                <span>Tous</span>
                                <span class="px-2 py-0.5 bg-slate-200 rounded-full text-xs">{{ partners.length }}</span>
                            </button>

                            <button
                                @click="setPartnerCategory('platinium')"
                                :class="activePartnerCategory === 'platinium'
                                    ? 'bg-gradient-to-r from-slate-400 to-slate-600 shadow-lg text-white'
                                    : 'text-slate-600 hover:text-slate-900'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2"
                            >
                                <i class="bi bi-star-fill"></i>
                                <span>Platinium</span>
                                <span class="px-2 py-0.5 bg-slate-200 rounded-full text-xs text-slate-900">{{ partners.filter(p => p.category === 'platinium').length }}</span>
                            </button>

                            <button
                                @click="setPartnerCategory('gold')"
                                :class="activePartnerCategory === 'gold'
                                    ? 'bg-gradient-to-r from-yellow-400 to-yellow-600 shadow-lg text-white'
                                    : 'text-slate-600 hover:text-slate-900'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2"
                            >
                                <i class="bi bi-award-fill"></i>
                                <span>Gold</span>
                                <span class="px-2 py-0.5 bg-yellow-100 rounded-full text-xs text-yellow-900">{{ partners.filter(p => p.category === 'gold').length }}</span>
                            </button>

                            <button
                                @click="setPartnerCategory('silver')"
                                :class="activePartnerCategory === 'silver'
                                    ? 'bg-gradient-to-r from-slate-300 to-slate-400 shadow-lg text-white'
                                    : 'text-slate-600 hover:text-slate-900'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2"
                            >
                                <i class="bi bi-gem"></i>
                                <span>Silver</span>
                                <span class="px-2 py-0.5 bg-slate-200 rounded-full text-xs text-slate-900">{{ partners.filter(p => p.category === 'silver').length }}</span>
                            </button>

                            <button
                                @click="setPartnerCategory('bronze')"
                                :class="activePartnerCategory === 'bronze'
                                    ? 'bg-gradient-to-r from-orange-400 to-orange-600 shadow-lg text-white'
                                    : 'text-slate-600 hover:text-slate-900'"
                                class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 flex items-center gap-2"
                            >
                                <i class="bi bi-trophy-fill"></i>
                                <span>Bronze</span>
                                <span class="px-2 py-0.5 bg-orange-100 rounded-full text-xs text-orange-900">{{ partners.filter(p => p.category === 'bronze').length }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="max-w-6xl mx-auto relative">
                        <!-- Carrousel Container -->
                        <div class="relative overflow-hidden">
                            <!-- Partner Cards -->
                            <div class="transition-transform duration-500 ease-in-out" :style="{ transform: `translateX(-${currentPartnerIndex * 100}%)` }">
                                <div class="flex">
                                    <div v-for="(partner, index) in filteredPartners()" :key="index" class="w-full flex-shrink-0 px-4">
                                        <div class="group relative bg-gradient-to-br from-slate-50 to-slate-100 rounded-3xl shadow-xl overflow-hidden hover:shadow-2xl transition-all duration-300 border border-slate-200">
                                            <div class="absolute inset-0 bg-gradient-to-br opacity-0 group-hover:opacity-100 transition-opacity" :class="`${partner.color}/5`"></div>

                                            <!-- Badge de catégorie -->
                                            <div class="absolute top-6 right-6 z-20">
                                                <div class="px-4 py-2 bg-gradient-to-r rounded-full text-white font-bold text-sm shadow-lg flex items-center gap-2" :class="getCategoryBadgeColor(partner.category)">
                                                    <i :class="getCategoryIcon(partner.category)"></i>
                                                    <span class="uppercase">{{ partner.category }}</span>
                                                </div>
                                            </div>

                                            <div class="grid md:grid-cols-2 gap-8 p-8">
                                                <!-- Image -->
                                                <div class="flex items-center justify-center">
                                                    <div class="relative">
                                                        <div class="absolute inset-0 bg-gradient-to-br rounded-2xl blur-xl opacity-20" :class="partner.color"></div>
                                                        <img :src="partner.image" :alt="partner.name" class="relative w-full max-w-sm rounded-2xl shadow-lg">
                                                    </div>
                                                </div>

                                                <!-- Content -->
                                                <div class="flex flex-col justify-center">
                                                    <h3 class="text-3xl font-bold mb-4 text-slate-900">{{ partner.name }}</h3>
                                                    <p class="text-slate-600 mb-6 leading-relaxed">
                                                        {{ partner.description }}
                                                    </p>

                                                    <div class="space-y-3 mb-6">
                                                        <div v-for="(feature, fIndex) in partner.features" :key="fIndex" class="flex items-center gap-2 text-slate-700">
                                                            <svg class="w-5 h-5 text-[#72c8d3]" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                            <span>{{ feature }}</span>
                                                        </div>
                                                    </div>

                                                    <a :href="partner.link" target="_blank" class="inline-flex items-center gap-2 text-[#72c8d3] hover:text-[#3a494f] font-semibold group/link">
                                                        En savoir plus
                                                        <svg class="w-5 h-5 group-hover/link:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                                                        </svg>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <button
                            v-if="filteredPartners().length > 1"
                            @click="prevPartner"
                            class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-12 h-12 bg-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center text-slate-900 hover:bg-gradient-to-r hover:from-[#72c8d3] hover:to-[#3a494f] hover:text-white group z-10"
                        >
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <button
                            v-if="filteredPartners().length > 1"
                            @click="nextPartner"
                            class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-12 h-12 bg-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center text-slate-900 hover:bg-gradient-to-r hover:from-[#72c8d3] hover:to-[#3a494f] hover:text-white group z-10"
                        >
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <!-- Dots Indicators -->
                        <div v-if="filteredPartners().length > 1" class="flex justify-center gap-3 mt-8">
                            <button
                                v-for="(partner, index) in filteredPartners()"
                                :key="index"
                                @click="goToPartner(index)"
                                class="transition-all duration-300"
                                :class="currentPartnerIndex === index
                                    ? 'w-12 h-3 bg-gradient-to-r from-[#72c8d3] to-[#3a494f] rounded-full'
                                    : 'w-3 h-3 bg-slate-300 hover:bg-slate-400 rounded-full'"
                            ></button>
                        </div>

                        <!-- Partner Counter -->
                        <div v-if="filteredPartners().length > 0" class="text-center mt-6">
                            <p class="text-sm text-slate-600 font-medium">
                                {{ currentPartnerIndex + 1 }} / {{ filteredPartners().length }}
                            </p>
                        </div>

                        <!-- Message si aucun partenaire dans la catégorie -->
                        <div v-if="filteredPartners().length === 0" class="text-center py-16">
                            <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-100 rounded-full mb-4">
                                <i class="bi bi-inbox text-4xl text-slate-400"></i>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 mb-2">Aucun partenaire dans cette catégorie</h3>
                            <p class="text-slate-600">Revenez bientôt pour découvrir de nouveaux partenaires</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Reviews Section - Remplacez votre section #reviews par ce code -->
            <section id="reviews" class="py-24 bg-gradient-to-br from-slate-50 to-slate-100">
                <div class="container mx-auto px-6">
                    <div class="text-center mb-16">
                        <span class="inline-block px-4 py-2 bg-[#72c8d3]/20 text-[#3a494f] rounded-full text-sm font-semibold mb-4">TÉMOIGNAGES</span>
                        <h2 class="text-4xl md:text-5xl font-bold mb-6 text-slate-900">Ce que disent nos utilisateurs</h2>
                        <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                            Découvrez les retours d'expérience de ceux qui utilisent VEHIX au quotidien
                        </p>
                    </div>

                    <div class="max-w-7xl mx-auto relative">
                        <!-- Reviews Container -->
                        <div class="overflow-hidden">
                            <div class="grid md:grid-cols-3 gap-6">
                                <div
                                    v-for="(review, index) in getVisibleReviews()"
                                    :key="index"
                                    class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-slate-200 overflow-hidden"
                                >
                                    <!-- Header avec badge vérifié -->
                                    <div class="p-6 bg-gradient-to-r from-[#72c8d3]/10 to-[#3a494f]/10">
                                        <!-- Badge vérifié -->
                                        <!--<div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center gap-2 px-3 py-1.5 bg-green-100 rounded-full">
                                                <svg class="w-4 h-4 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs font-semibold text-green-700">Vérifié</span>
                                            </div>
                                                <span class="text-xs text-slate-400">{{ review.date }}</span>
                                        </div>-->

                                        <!-- Commentaire -->
                                        <i class="bi bi-quote text-[#FFA500] text-2xl"></i>
                                            <p class="text-slate-700 leading-relaxed" v-html="review.comment"></p>
                                        <i class="bi bi-quote text-[#FFA500] text-2xl rotate-180 inline-block ml-1"></i>
                                    </div>

                                    <!-- User Info -->
                                    <div class="p-6 flex items-center gap-4">
                                        <img
                                            :src="review.avatar"
                                            :alt="review.name"
                                            class="w-16 h-16 rounded-full object-cover ring-4 ring-slate-100"
                                        >
                                        <div class="flex-1">
                                            <h4 class="font-bold text-slate-900">{{ review.name }}</h4>
                                            <p class="text-sm text-slate-600">{{ review.role }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Navigation Buttons -->
                        <button
                            @click="prevReview"
                            class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 w-12 h-12 bg-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center text-slate-900 hover:bg-gradient-to-r hover:from-[#72c8d3] hover:to-[#3a494f] hover:text-white group z-10"
                        >
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>

                        <button
                            @click="nextReview"
                            class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 w-12 h-12 bg-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 flex items-center justify-center text-slate-900 hover:bg-gradient-to-r hover:from-[#72c8d3] hover:to-[#3a494f] hover:text-white group z-10"
                        >
                            <svg class="w-6 h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                            </svg>
                        </button>

                        <!-- Review Counter -->
                        <div class="text-center mt-8">
                            <p class="text-sm text-slate-600 font-medium">
                                Affichage de {{ currentReviewIndex + 1 }}-{{ Math.min(currentReviewIndex + 3, reviews.length) }} sur {{ reviews.length }} avis
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Contact Section -->
            <section id="contact" class="py-24 bg-gradient-to-br from-slate-50 to-slate-100">
                <div class="container mx-auto px-6">
                    <div class="text-center mb-16">
                        <span class="inline-block px-4 py-2 bg-[#72c8d3]/20 text-[#3a494f] rounded-full text-sm font-semibold mb-4">CONTACTEZ-NOUS</span>
                        <h2 class="text-4xl md:text-5xl font-bold mb-6 text-slate-900">Restons en Contact</h2>
                        <p class="text-lg text-slate-600 max-w-2xl mx-auto">
                            VEHIX est un produit conçu et développé par HASNREZIGA Informatique.
                            N'hésitez pas à nous contacter en cas de besoin!
                        </p>
                    </div>

                    <div class="grid lg:grid-cols-2 gap-12 max-w-6xl mx-auto">
                        <!-- Contact Info -->
                        <div class="space-y-6">
                            <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow border border-slate-200">
                                <div class="flex items-start gap-4">
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#72c8d3] to-[#3a494f] flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-envelope-fill text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg mb-2 text-slate-900">Email</h3>
                                        <p class="text-slate-600">hasnreziga@gmail.com</p>
                                    </div>
                                </div>
                            </div>

                            <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-shadow border border-slate-200">
                                <div class="flex items-start gap-4">
                                    <div class="w-14 h-14 rounded-xl bg-gradient-to-br from-[#72c8d3] to-[#3a494f] flex items-center justify-center flex-shrink-0">
                                        <i class="bi bi-telephone-fill text-2xl text-white"></i>
                                    </div>
                                    <div>
                                        <h3 class="font-bold text-lg mb-2 text-slate-900">Téléphone</h3>
                                        <p class="text-slate-600">+261 34 40 994 35</p>
                                    </div>
                                </div>
                            </div>


                            <!-- Map -->
                            <div class="bg-white rounded-2xl shadow-lg border border-slate-200 hover:shadow-xl transition-shadow overflow-hidden">
                                <div class="p-6">
                                    <h3 class="font-bold text-lg mb-4 text-slate-900 flex items-center gap-2">
                                        <i class="bi bi-pin-map-fill text-[#72c8d3]"></i>
                                        Notre Localisation
                                    </h3>
                                    <p class="text-sm text-slate-600">Ambohipo LOT VT 31 C Bis Antananarivo, Madagascar</p>
                                </div>

                                <!-- Google Maps Embed -->
                                <div class="relative h-80">
                                    <iframe
                                        src="https://www.google.com/maps?q=-18.930912982244024,47.561584074383836&hl=fr&z=15&output=embed"
                                        width="100%"
                                        height="100%"
                                        style="border:0;"
                                        allowfullscreen=""
                                        loading="lazy"
                                        referrerpolicy="no-referrer-when-downgrade"
                                        class="w-full h-full"
                                    ></iframe>
                                </div>

                                <!-- Action Buttons -->
                                <div class="p-4 bg-slate-50 flex flex-wrap gap-3 justify-center">
                                    <a
                                        href="https://www.google.com/maps/search/?api=1&query=-18.931014633483336, 47.56195546453688"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="group inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-[#72c8d3] to-[#3a494f] text-white rounded-lg hover:shadow-lg hover:shadow-[#72c8d3]/30 transition-all duration-300 hover:scale-105 text-sm font-semibold"
                                    >
                                        <i class="bi bi-map"></i>
                                        <span>Google Maps</span>
                                        <i class="bi bi-box-arrow-up-right text-xs group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"></i>
                                    </a>

                                    <a
                                        href="https://waze.com/ul?ll=-18.93101273063659, 47.56195010012092&navigate=yes"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        class="group inline-flex items-center gap-2 px-4 py-2 bg-white border-2 border-slate-200 text-slate-700 rounded-lg hover:border-[#72c8d3] hover:text-[#72c8d3] transition-all duration-300 hover:scale-105 text-sm font-semibold"
                                    >
                                        <i class="bi bi-signpost-2"></i>
                                        <span>Waze</span>
                                    </a>
                                </div>
                            </div>

                        </div>

                        <!-- Contact Form -->
                        <div class="bg-white p-8 rounded-2xl shadow-lg border border-slate-200">
                            <form @submit="submitContactForm" class="space-y-6">
                                <!-- Message de succès/erreur -->
                                <div v-if="contactFormMessage.text"
                                    :class="contactFormMessage.type === 'success' ? 'bg-green-50 border-green-500 text-green-800' : 'bg-red-50 border-red-500 text-red-800'"
                                    class="p-4 rounded-xl border-2 flex items-start gap-3">
                                    <i :class="contactFormMessage.type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill'"
                                    class="bi text-xl"></i>
                                    <span>{{ contactFormMessage.text }}</span>
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2 text-slate-900">Votre Nom</label>
                                    <input
                                        v-model="contactForm.name"
                                        type="text"
                                        required
                                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#72c8d3] focus:border-transparent transition-all bg-slate-50 focus:bg-white"
                                        placeholder=""
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2 text-slate-900">Votre Email</label>
                                    <input
                                        v-model="contactForm.email"
                                        type="email"
                                        required
                                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#72c8d3] focus:border-transparent transition-all bg-slate-50 focus:bg-white"
                                        placeholder=""
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2 text-slate-900">Sujet</label>
                                    <input
                                        v-model="contactForm.subject"
                                        type="text"
                                        required
                                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#72c8d3] focus:border-transparent transition-all bg-slate-50 focus:bg-white"
                                        placeholder="Demande d'information"
                                    >
                                </div>

                                <div>
                                    <label class="block text-sm font-semibold mb-2 text-slate-900">Message</label>
                                    <textarea
                                        v-model="contactForm.message"
                                        rows="6"
                                        required
                                        class="w-full px-4 py-3 border border-slate-300 rounded-xl focus:ring-2 focus:ring-[#72c8d3] focus:border-transparent transition-all bg-slate-50 focus:bg-white resize-none"
                                        placeholder="Votre message ici..."
                                    ></textarea>
                                </div>

                                <button
                                    type="submit"
                                    :disabled="contactFormLoading"
                                    class="w-full bg-gradient-to-r from-[#72c8d3] to-[#3a494f] hover:from-[#72c8d3] hover:to-[#273628] text-white px-8 py-4 rounded-xl font-semibold transition-all duration-300 hover:shadow-xl hover:shadow-[#72c8d3]/50 flex items-center justify-center gap-2 group disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="!contactFormLoading">Envoyer le Message</span>
                                    <span v-else>Envoi en cours...</span>
                                    <i v-if="!contactFormLoading" class="bi bi-send group-hover:translate-x-1 transition-transform"></i>
                                    <svg v-else class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Footer -->
            <footer class="py-12 bg-[#273628] text-white">
                <div class="container mx-auto px-6">
                    <div class="text-center space-y-4">
                        <div class="flex items-center justify-center gap-2 text-slate-400">
                            <span>©</span>
                            <span class="text-white font-semibold">VEHIX</span>
                            <span>{{ new Date().getFullYear() }}</span>
                            <span>- Tous droits réservés</span>
                        </div>
                        <p class="text-sm text-slate-400">
                            Conçu par
                            <a href="https://www.hasnreziga.mg/" target="_blank" class="text-[#72c8d3] hover:text-[#cf98da] font-semibold transition-colors">
                                HASNREZIGA Informatique
                            </a>
                        </p>
                    </div>
                </div>
            </footer>
        </main>
    </div>
</template>

<style scoped>
/* Smooth animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Animate on scroll */
.animate-on-scroll {
    opacity: 0;
    transform: translateY(30px);
    transition: opacity 0.6s ease-out, transform 0.6s ease-out;
}

.animate-on-scroll.animate-in {
    opacity: 1;
    transform: translateY(0);
}

/* Custom scrollbar for sidebar */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(114, 200, 211, 0.5);
    border-radius: 10px;
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(114, 200, 211, 0.8);
}

/* Gradient text animation */
@keyframes gradient-shift {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

.gradient-text-animated {
    background-size: 200% 200%;
    animation: gradient-shift 3s ease infinite;
}

/* Card hover effects */
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-8px) scale(1.02);
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
}

/* Pulse animation for badges */
@keyframes pulse-glow {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(114, 200, 211, 0.7);
    }
    50% {
        box-shadow: 0 0 20px 10px rgba(114, 200, 211, 0);
    }
}

.pulse-badge {
    animation: pulse-glow 2s infinite;
}

/* Stagger animation delays */
.stagger-1 { animation-delay: 0.1s; }
.stagger-2 { animation-delay: 0.2s; }
.stagger-3 { animation-delay: 0.3s; }
.stagger-4 { animation-delay: 0.4s; }
.stagger-5 { animation-delay: 0.5s; }
.stagger-6 { animation-delay: 0.6s; }
</style>