<script setup>
import { ref, computed, onMounted } from 'vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import Banner from '@/Components/Banner.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import PwaInstallPrompt from '@/Components/PwaInstallPrompt.vue';

import {
    HomeIcon,
    TruckIcon,
    MapIcon,
    FireIcon,
    WrenchScrewdriverIcon,
    ShieldCheckIcon,
    ClipboardDocumentCheckIcon,
    UserGroupIcon,
    Bars3Icon,
    XMarkIcon,
    RectangleStackIcon,
    CurrencyDollarIcon,
    ChevronDownIcon,
    ChartBarIcon,

} from '@heroicons/vue/24/outline';

const page = usePage();

const props = defineProps({
    title: String,
    userRole: {
        type: String,
        default: 'client'
    }
});

const showingNavigationDropdown = ref(false);
const sidebarOpen = ref(true);
const isOnline = ref(navigator.onLine);
const showSyncNotification = ref(false);
const expandedGroups = ref([]);

// Toggle group expansion
const toggleGroup = (groupName) => {
    const index = expandedGroups.value.indexOf(groupName);
    if (index > -1) {
        expandedGroups.value.splice(index, 1);
    } else {
        expandedGroups.value.push(groupName);
    }
};

const isGroupExpanded = (groupName) => {
    return expandedGroups.value.includes(groupName);
};

// Navigation links dynamiques selon le rôle
const navigationLinks = computed(() => {
    const role = page.props.auth?.user?.role || 'client';
    const links = [
        {
            name: 'Tableau de bord',
            href: 'dashboard',
            icon: HomeIcon,
            current: 'dashboard'
        }
    ];

    if (role === 'client') {
        links.push(
            {
                name: 'Sélection Véhicule',
                href: 'vehicules.selection',
                icon: RectangleStackIcon,
                current: 'vehicules.selection'
            },
            {
                name: 'Trajets',
                href: 'trajets.index',
                icon: MapIcon,
                current: 'trajets.*'
            },
            {
                name: 'Carburants',
                href: 'ravitaillements.index',
                icon: FireIcon,
                current: 'ravitaillements.*'
            },
            {
                name: 'Maintenance',
                href: 'maintenances.index',
                icon: WrenchScrewdriverIcon,
                current: 'maintenances.*'
            },
            {
                name: 'Assurances',
                href: 'assurances.index',
                icon: ShieldCheckIcon,
                current: 'assurances.*'
            },
            {
                name: 'Visite Technique',
                href: 'visite-techniques.index',
                icon: ClipboardDocumentCheckIcon,
                current: 'visite-techniques.*'
            },
            {
                name: 'Propriétaires',
                href: 'proprietaires.index',
                icon: UserGroupIcon,
                current: 'proprietaires.*'
            },
            {
                name: 'Rapports',
                href: 'rapports.index',
                icon: ChartBarIcon,
                current: 'rapports.*'
            }
        );
    }
    else if (role === 'validator' || role === 'administrateur') {
        links.push(
            {
                name: 'Gestion Utilisateurs',
                href: 'administrateur.users.index',
                icon: UserGroupIcon,
                current: 'administrateur.users.*'
            },
            {
                name: 'Validation Véhicules',
                href: 'vehicules.index',
                icon: ClipboardDocumentCheckIcon,
                current: 'vehicules.index,vehicules.show,vehicules.edit'
            },
            
            {
                name: 'Prix de carburant',
                href: 'admin.fuel-prices.index',
                icon: CurrencyDollarIcon,
                current: 'admin.fuel-prices.index,admin.fuel-prices.create,admin.fuel-prices.edit'
            },
            {
                name: 'Gestion Ressources',
                icon: TruckIcon,
                isGroup: true,
                groupCurrent: 'trajets.*,ravitaillements.*,maintenances.*,assurances.*,visite-techniques.*,proprietaires.*',
                children: [
                    {
                        name: 'Trajets',
                        href: 'trajets.index',
                        icon: MapIcon,
                        current: 'trajets.*'
                    },
                    {
                        name: 'Carburants',
                        href: 'ravitaillements.index',
                        icon: FireIcon,
                        current: 'ravitaillements.*'
                    },
                    {
                        name: 'Maintenances',
                        href: 'maintenances.index',
                        icon: WrenchScrewdriverIcon,
                        current: 'maintenances.*'
                    },
                    {
                        name: 'Assurances',
                        href: 'assurances.index',
                        icon: ShieldCheckIcon,
                        current: 'assurances.*'
                    },
                    {
                        name: 'Visites Techniques',
                        href: 'visite-techniques.index',
                        icon: ClipboardDocumentCheckIcon,
                        current: 'visite-techniques.*'
                    },
                    {
                        name: 'Propriétaires',
                        href: 'proprietaires.index',
                        icon: UserGroupIcon,
                        current: 'proprietaires.*'
                    }
                ]
            }
        );
    }

    return links;
});

// Check if any child is active
const hasActiveChild = (link) => {
    if (!link.children) return false;

    const childActive = link.children.some(child => route().current(child.current));

    if (link.groupCurrent) {
        const patterns = link.groupCurrent.split(',');
        const groupActive = patterns.some(pattern => route().current(pattern.trim()));
        return childActive || groupActive;
    }

    return childActive;
};

onMounted(() => {
    window.addEventListener('online', handleOnline);
    window.addEventListener('offline', handleOffline);

    // Auto-expand groups with active children
    navigationLinks.value.forEach(link => {
        if (link.isGroup && hasActiveChild(link)) {
            expandedGroups.value.push(link.name);
        }
    });
});

const handleOnline = () => {
    isOnline.value = true;
    showSyncNotification.value = true;

    if ('serviceWorker' in navigator && navigator.serviceWorker.controller) {
        navigator.serviceWorker.controller.postMessage({
            type: 'SYNC_NOW'
        });
    }

    setTimeout(() => {
        showSyncNotification.value = false;
    }, 5000);
};

const handleOffline = () => {
    isOnline.value = false;
};

const switchToTeam = (team) => {
    router.put(route('current-team.update'), {
        team_id: team.id,
    }, {
        preserveState: false,
    });
};

const logout = () => {
    router.post(route('logout'));
};

const toggleSidebar = () => {
    sidebarOpen.value = !sidebarOpen.value;
};
</script>

<template>
    <div>
        <Head :title="title" />

        <Banner />

        <PwaInstallPrompt />

        <!-- Notification de statut en ligne/hors ligne -->
        <Transition name="slide-down">
            <div
                v-if="!isOnline"
                class="fixed top-0 left-0 right-0 bg-yellow-500 text-white text-center py-2 px-4 z-50 shadow-lg"
            >
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Mode hors ligne - Vos modifications seront synchronisées automatiquement</span>
                </div>
            </div>
        </Transition>

        <!-- Notification de synchronisation -->
        <Transition name="slide-down">
            <div
                v-if="showSyncNotification"
                class="fixed top-0 left-0 right-0 bg-green-500 text-white text-center py-2 px-4 z-50 shadow-lg"
            >
                <div class="flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Connexion rétablie - Synchronisation en cours...</span>
                </div>
            </div>
        </Transition>

        <div class="min-h-screen bg-gray-100">
            <!-- Sidebar pour desktop -->
            <div
                :class="[
                    'fixed inset-y-0 left-0 z-40 w-64 bg-white shadow-lg transform transition-transform duration-300 ease-in-out',
                    sidebarOpen ? 'translate-x-0' : '-translate-x-full',
                    'hidden lg:block'
                ]"
            >
                <!-- Logo Desktop - TOUJOURS VISIBLE -->
                <div class="flex items-center justify-center h-16 px-6 border-b border-gray-200 bg-white">
                    <Link :href="route('dashboard')" class="flex items-center">
                        <ApplicationMark class="block h-9 w-auto" />
                    </Link>
                </div>

                <!-- Navigation Links -->
                <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto max-h-[calc(100vh-4rem)]">
                    <template v-for="link in navigationLinks" :key="link.name">
                        <!-- Group with children (Admin/Validator) -->
                        <div v-if="link.isGroup" class="space-y-1">
                            <button
                                @click="toggleGroup(link.name)"
                                :class="[
                                    hasActiveChild(link)
                                        ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
                                        : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                    'group w-full flex items-center justify-between px-3 py-2 text-sm font-medium border-l-4 transition-colors duration-150'
                                ]"
                            >
                                <div class="flex items-center">
                                    <component
                                        :is="link.icon"
                                        :class="[
                                            hasActiveChild(link)
                                                ? 'text-indigo-500'
                                                : 'text-gray-400 group-hover:text-gray-500',
                                            'mr-3 flex-shrink-0 h-6 w-6 transition-colors duration-150'
                                        ]"
                                    />
                                    {{ link.name }}
                                </div>
                                <ChevronDownIcon
                                    :class="[
                                        'h-5 w-5 transition-transform duration-200',
                                        isGroupExpanded(link.name) ? 'transform rotate-180' : '',
                                        hasActiveChild(link) ? 'text-indigo-500' : 'text-gray-400'
                                    ]"
                                />
                            </button>

                            <!-- Children -->
                            <Transition
                                enter-active-class="transition ease-out duration-200"
                                enter-from-class="opacity-0 -translate-y-1"
                                enter-to-class="opacity-100 translate-y-0"
                                leave-active-class="transition ease-in duration-150"
                                leave-from-class="opacity-100 translate-y-0"
                                leave-to-class="opacity-0 -translate-y-1"
                            >
                                <div v-show="isGroupExpanded(link.name)" class="ml-4 space-y-1">
                                    <Link
                                        v-for="child in link.children"
                                        :key="child.href"
                                        :href="route(child.href)"
                                        :class="[
                                            route().current(child.current)
                                                ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
                                                : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                            'group flex items-center px-3 py-2 text-sm font-medium border-l-4 transition-colors duration-150'
                                        ]"
                                    >
                                        <component
                                            v-if="child.icon"
                                            :is="child.icon"
                                            :class="[
                                                route().current(child.current)
                                                    ? 'text-indigo-500'
                                                    : 'text-gray-400 group-hover:text-gray-500',
                                                'mr-3 flex-shrink-0 h-5 w-5 transition-colors duration-150'
                                            ]"
                                        />
                                        <span v-else class="w-5 mr-3"></span>
                                        {{ child.name }}
                                    </Link>
                                </div>
                            </Transition>
                        </div>

                        <!-- Regular link (Client) -->
                        <Link
                            v-else
                            :href="route(link.href)"
                            :class="[
                                route().current(link.current)
                                    ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
                                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                'group flex items-center px-3 py-2 text-sm font-medium border-l-4 transition-colors duration-150'
                            ]"
                        >
                            <component
                                :is="link.icon"
                                :class="[
                                    route().current(link.current)
                                        ? 'text-indigo-500'
                                        : 'text-gray-400 group-hover:text-gray-500',
                                    'mr-3 flex-shrink-0 h-6 w-6 transition-colors duration-150'
                                ]"
                            />
                            {{ link.name }}
                        </Link>
                    </template>
                </nav>
            </div>

            <!-- Sidebar mobile -->
            <Transition
                enter-active-class="transition-opacity duration-300"
                enter-from-class="opacity-0"
                enter-to-class="opacity-100"
                leave-active-class="transition-opacity duration-300"
                leave-from-class="opacity-100"
                leave-to-class="opacity-0"
            >
                <div
                    v-if="showingNavigationDropdown"
                    class="fixed inset-0 z-40 bg-gray-600 bg-opacity-75 lg:hidden"
                    @click="showingNavigationDropdown = false"
                />
            </Transition>

            <Transition
                enter-active-class="transition-transform duration-300"
                enter-from-class="-translate-x-full"
                enter-to-class="translate-x-0"
                leave-active-class="transition-transform duration-300"
                leave-from-class="translate-x-0"
                leave-to-class="-translate-x-full"
            >
                <div
                    v-if="showingNavigationDropdown"
                    class="fixed inset-y-0 left-0 z-50 w-64 bg-white shadow-lg lg:hidden overflow-y-auto"
                >
                    <!-- Logo et bouton fermer Mobile - TOUJOURS VISIBLE -->
                    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 bg-white sticky top-0 z-10">
                        <Link :href="route('dashboard')" class="flex items-center" @click="showingNavigationDropdown = false">
                            <ApplicationMark class="block h-9 w-auto" />
                        </Link>
                        <button
                            @click="showingNavigationDropdown = false"
                            class="text-gray-500 hover:text-gray-700"
                        >
                            <XMarkIcon class="h-6 w-6" />
                        </button>
                    </div>

                    <!-- Navigation Links Mobile -->
                    <nav class="px-4 py-6 space-y-1">
                        <template v-for="link in navigationLinks" :key="link.name">
                            <!-- Group with children (Admin/Validator) -->
                            <div v-if="link.isGroup" class="space-y-1">
                                <button
                                    @click="toggleGroup(link.name)"
                                    :class="[
                                        hasActiveChild(link)
                                            ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
                                            : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                        'group w-full flex items-center justify-between px-3 py-2 text-sm font-medium border-l-4 transition-colors duration-150'
                                    ]"
                                >
                                    <div class="flex items-center">
                                        <component
                                            :is="link.icon"
                                            :class="[
                                                hasActiveChild(link)
                                                    ? 'text-indigo-500'
                                                    : 'text-gray-400 group-hover:text-gray-500',
                                                'mr-3 flex-shrink-0 h-6 w-6 transition-colors duration-150'
                                            ]"
                                        />
                                        {{ link.name }}
                                    </div>
                                    <ChevronDownIcon
                                        :class="[
                                            'h-5 w-5 transition-transform duration-200',
                                            isGroupExpanded(link.name) ? 'transform rotate-180' : '',
                                            hasActiveChild(link) ? 'text-indigo-500' : 'text-gray-400'
                                        ]"
                                    />
                                </button>

                                <!-- Children -->
                                <Transition
                                    enter-active-class="transition ease-out duration-200"
                                    enter-from-class="opacity-0 -translate-y-1"
                                    enter-to-class="opacity-100 translate-y-0"
                                    leave-active-class="transition ease-in duration-150"
                                    leave-from-class="opacity-100 translate-y-0"
                                    leave-to-class="opacity-0 -translate-y-1"
                                >
                                    <div v-show="isGroupExpanded(link.name)" class="ml-4 space-y-1">
                                        <Link
                                            v-for="child in link.children"
                                            :key="child.href"
                                            :href="route(child.href)"
                                            :class="[
                                                route().current(child.current)
                                                    ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
                                                    : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                                'group flex items-center px-3 py-2 text-sm font-medium border-l-4 transition-colors duration-150'
                                            ]"
                                            @click="showingNavigationDropdown = false"
                                        >
                                            <component
                                                v-if="child.icon"
                                                :is="child.icon"
                                                :class="[
                                                    route().current(child.current)
                                                        ? 'text-indigo-500'
                                                        : 'text-gray-400 group-hover:text-gray-500',
                                                    'mr-3 flex-shrink-0 h-5 w-5 transition-colors duration-150'
                                                ]"
                                            />
                                            <span v-else class="w-5 mr-3"></span>
                                            {{ child.name }}
                                        </Link>
                                    </div>
                                </Transition>
                            </div>

                            <!-- Regular link (Client) -->
                            <Link
                                v-else
                                :href="route(link.href)"
                                :class="[
                                    route().current(link.current)
                                        ? 'bg-indigo-50 border-indigo-500 text-indigo-700'
                                        : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900',
                                    'group flex items-center px-3 py-2 text-sm font-medium border-l-4 transition-colors duration-150'
                                ]"
                                @click="showingNavigationDropdown = false"
                            >
                                <component
                                    :is="link.icon"
                                    :class="[
                                        route().current(link.current)
                                            ? 'text-indigo-500'
                                            : 'text-gray-400 group-hover:text-gray-500',
                                        'mr-3 flex-shrink-0 h-6 w-6 transition-colors duration-150'
                                    ]"
                                />
                                {{ link.name }}
                            </Link>
                        </template>
                    </nav>

                    <!-- User info mobile -->
                    <div class="border-t border-gray-200 pt-4 pb-3 px-4">
                        <div class="flex items-center px-3">
                            <div v-if="$page.props.jetstream.managesProfilePhotos" class="shrink-0 me-3">
                                <img class="size-10 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                            </div>
                            <div>
                                <div class="font-medium text-base text-gray-800">
                                    {{ $page.props.auth.user.name }}
                                </div>
                                <div class="font-medium text-sm text-gray-500">
                                    {{ $page.props.auth.user.email }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.show')" :active="route().current('profile.show')">
                                Profile
                            </ResponsiveNavLink>

                            <form method="POST" @submit.prevent="logout">
                                <ResponsiveNavLink as="button">
                                    Log Out
                                </ResponsiveNavLink>
                            </form>
                        </div>
                    </div>
                </div>
            </Transition>

            <!-- Main content -->
            <div :class="['transition-all duration-300', sidebarOpen ? 'lg:pl-64' : 'lg:pl-0']">
                <!-- Top bar -->
                <nav class="bg-white border-b border-gray-100 sticky top-0 z-30">
                    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div class="flex justify-between h-16">
                            <div class="flex items-center">
                                <!-- Toggle sidebar button (desktop) -->
                                <button
                                    @click="toggleSidebar"
                                    class="hidden lg:block p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500 mr-4"
                                >
                                    <Bars3Icon class="h-6 w-6" />
                                </button>

                                <!-- Mobile menu button -->
                                <button
                                    @click="showingNavigationDropdown = !showingNavigationDropdown"
                                    class="lg:hidden p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                >
                                    <Bars3Icon class="h-6 w-6" />
                                </button>

                                <!-- Logo mobile - TOUJOURS VISIBLE dans la topbar -->
                                <Link :href="route('dashboard')" class="lg:hidden ml-2 flex items-center">
                                    <ApplicationMark class="block h-8 w-auto" />
                                </Link>
                            </div>

                            <div class="flex items-center gap-4">
                                <!-- Indicateur de connexion -->
                                <div
                                    :class="[
                                        'flex items-center gap-2 px-3 py-1 rounded-full text-xs font-medium',
                                        isOnline ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700'
                                    ]"
                                >
                                    <span
                                        :class="[
                                            'w-2 h-2 rounded-full',
                                            isOnline ? 'bg-green-500' : 'bg-yellow-500 animate-pulse'
                                        ]"
                                    />
                                    {{ isOnline ? 'En ligne' : 'Hors ligne' }}
                                </div>

                                <!-- Teams Dropdown -->
                                <Dropdown v-if="$page.props.jetstream.hasTeamFeatures" align="right" width="60">
                                    <template #trigger>
                                        <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none focus:bg-gray-50 transition">
                                            {{ $page.props.auth.user.current_team.name }}
                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 15L12 18.75 15.75 15m-7.5-6L12 5.25 15.75 9" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="w-60">
                                            <div class="block px-4 py-2 text-xs text-gray-400">
                                                Manage Team
                                            </div>
                                            <DropdownLink :href="route('teams.show', $page.props.auth.user.current_team)">
                                                Team Settings
                                            </DropdownLink>
                                            <DropdownLink v-if="$page.props.jetstream.canCreateTeams" :href="route('teams.create')">
                                                Create New Team
                                            </DropdownLink>

                                            <template v-if="$page.props.auth.user.all_teams.length > 1">
                                                <div class="border-t border-gray-200" />
                                                <div class="block px-4 py-2 text-xs text-gray-400">
                                                    Switch Teams
                                                </div>
                                                <template v-for="team in $page.props.auth.user.all_teams" :key="team.id">
                                                    <form @submit.prevent="switchToTeam(team)">
                                                        <DropdownLink as="button">
                                                            <div class="flex items-center">
                                                                <svg v-if="team.id == $page.props.auth.user.current_team_id" class="me-2 size-5 text-green-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                </svg>
                                                                <div>{{ team.name }}</div>
                                                            </div>
                                                        </DropdownLink>
                                                    </form>
                                                </template>
                                            </template>
                                        </div>
                                    </template>
                                </Dropdown>

                                <!-- User Dropdown -->
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button v-if="$page.props.jetstream.managesProfilePhotos" class="flex text-sm border-2 border-transparent rounded-full focus:outline-none focus:border-gray-300 transition">
                                            <img class="size-8 rounded-full object-cover" :src="$page.props.auth.user.profile_photo_url" :alt="$page.props.auth.user.name">
                                        </button>

                                        <button v-else type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition">
                                            {{ $page.props.auth.user.name }}
                                            <svg class="ms-2 -me-0.5 size-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                            </svg>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="block px-4 py-2 text-xs text-gray-400">
                                            Manage Account
                                        </div>
                                        <DropdownLink :href="route('profile.show')">
                                            Profile
                                        </DropdownLink>
                                        <DropdownLink v-if="$page.props.jetstream.hasApiFeatures" :href="route('api-tokens.index')">
                                            API Tokens
                                        </DropdownLink>
                                        <div class="border-t border-gray-200" />
                                        <form @submit.prevent="logout">
                                            <DropdownLink as="button">
                                                Log Out
                                            </DropdownLink>
                                        </form>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>
                    </div>
                </nav>

                <!-- Page Heading -->
                <header v-if="$slots.header" class="bg-white shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        <slot name="header" />
                    </div>
                </header>

                <!-- Page Content -->
                <main>
                    <slot />
                </main>
            </div>
        </div>
    </div>
</template>

<style scoped>
.slide-down-enter-active,
.slide-down-leave-active {
    transition: all 0.3s ease;
}

.slide-down-enter-from {
    transform: translateY(-100%);
    opacity: 0;
}

.slide-down-leave-to {
    transform: translateY(-100%);
    opacity: 0;
}
</style>
