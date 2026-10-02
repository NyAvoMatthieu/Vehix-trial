<script setup>
import { ref, watch } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    id: { type: String, default: undefined },
    required: { type: Boolean, default: false },
    defaultCountry: { type: String, default: 'MG' },
});

const emit = defineEmits(['update:modelValue']);

const countries = [
    { iso: 'MG', flag: '🇲🇬', name: 'Madagascar', dial: '+261' },
    { iso: 'FR', flag: '🇫🇷', name: 'France', dial: '+33' },
    { iso: 'RE', flag: '🇷🇪', name: 'La Réunion', dial: '+262' },
    { iso: 'MU', flag: '🇲🇺', name: 'Maurice', dial: '+230' },
    { iso: 'KM', flag: '🇰🇲', name: 'Comores', dial: '+269' },
    { iso: 'SC', flag: '🇸🇨', name: 'Seychelles', dial: '+248' },
    { iso: 'MZ', flag: '🇲🇿', name: 'Mozambique', dial: '+258' },
    { iso: 'ZA', flag: '🇿🇦', name: 'Afrique du Sud', dial: '+27' },
    { iso: 'KE', flag: '🇰🇪', name: 'Kenya', dial: '+254' },
    { iso: 'TZ', flag: '🇹🇿', name: 'Tanzanie', dial: '+255' },
    { iso: 'MA', flag: '🇲🇦', name: 'Maroc', dial: '+212' },
    { iso: 'DZ', flag: '🇩🇿', name: 'Algérie', dial: '+213' },
    { iso: 'TN', flag: '🇹🇳', name: 'Tunisie', dial: '+216' },
    { iso: 'SN', flag: '🇸🇳', name: 'Sénégal', dial: '+221' },
    { iso: 'CI', flag: '🇨🇮', name: "Côte d'Ivoire", dial: '+225' },
    { iso: 'CM', flag: '🇨🇲', name: 'Cameroun', dial: '+237' },
    { iso: 'EG', flag: '🇪🇬', name: 'Égypte', dial: '+20' },
    { iso: 'BE', flag: '🇧🇪', name: 'Belgique', dial: '+32' },
    { iso: 'CH', flag: '🇨🇭', name: 'Suisse', dial: '+41' },
    { iso: 'DE', flag: '🇩🇪', name: 'Allemagne', dial: '+49' },
    { iso: 'GB', flag: '🇬🇧', name: 'Royaume-Uni', dial: '+44' },
    { iso: 'ES', flag: '🇪🇸', name: 'Espagne', dial: '+34' },
    { iso: 'IT', flag: '🇮🇹', name: 'Italie', dial: '+39' },
    { iso: 'US', flag: '🇺🇸', name: 'États-Unis / Canada', dial: '+1' },
    { iso: 'CN', flag: '🇨🇳', name: 'Chine', dial: '+86' },
    { iso: 'IN', flag: '🇮🇳', name: 'Inde', dial: '+91' },
    { iso: 'JP', flag: '🇯🇵', name: 'Japon', dial: '+81' },
];

// Décompose une valeur existante ("+261 34 12 345 67") en pays + numéro
const parse = (value) => {
    const v = (value || '').trim();
    if (v.startsWith('+')) {
        const match = [...countries]
            .sort((a, b) => b.dial.length - a.dial.length)
            .find((c) => v.startsWith(c.dial));
        if (match) {
            return { iso: match.iso, number: v.slice(match.dial.length).trim() };
        }
    }
    return { iso: props.defaultCountry, number: v };
};

const initial = parse(props.modelValue);
const iso = ref(initial.iso);
const number = ref(initial.number);

const dialOf = (code) => countries.find((c) => c.iso === code)?.dial ?? '';

const onNumberInput = (e) => {
    number.value = e.target.value.replace(/[^\d\s]/g, '');
};

watch([iso, number], () => {
    const n = number.value.trim();
    emit('update:modelValue', n ? `${dialOf(iso.value)} ${n}` : '');
});
</script>

<template>
    <div class="mt-1 flex rounded-md shadow-sm">
        <select
            v-model="iso"
            aria-label="Indicatif du pays"
            class="rounded-l-md border-gray-300 bg-gray-50 text-sm focus:border-indigo-500 focus:ring-indigo-500 w-18 shrink-0"
        >
            <option v-for="c in countries" :key="c.iso" :value="c.iso" :title="c.name">
                {{ c.flag }} {{ c.name }}
            </option>
        </select>
        <input
            :id="id"
            :value="number"
            @input="onNumberInput"
            type="tel"
            inputmode="tel"
            :required="required"
            placeholder=" 34 12 345 67"
            class="block w-full rounded-r-md border-l-0 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
        />
    </div>
</template>