<script setup>
import { ref, computed, onMounted } from 'vue';
import { AsYouType, parsePhoneNumberFromString } from 'libphonenumber-js/min';

const props = defineProps({
    modelValue: { type: String, default: '' },
    id: { type: String, default: undefined },
    required: { type: Boolean, default: false },
    defaultCountry: { type: String, default: 'MG' },
});

const emit = defineEmits(['update:modelValue']);

const inputEl = ref(null);
const display = ref('');
const country = ref(null);

// ISO "MG" -> 🇲🇬
const flag = computed(() =>
    country.value
        ? String.fromCodePoint(...[...country.value].map((c) => 127397 + c.charCodeAt(0)))
        : '🌐'
);

const parsed = (value) => parsePhoneNumberFromString(value || '', props.defaultCountry);

const isInvalid = computed(() => {
    const p = parsed(display.value);
    return display.value.trim() !== '' && (!p || !p.isValid());
});

const sync = () => {
    const p = parsed(display.value);
    emit('update:modelValue', p && p.isValid() ? p.formatInternational() : display.value.trim());
    inputEl.value?.setCustomValidity(isInvalid.value ? 'Numéro invalide' : '');
};

const onInput = (e) => {
    const raw = e.target.value.replace(/[^\d+\s]/g, '');
    // "+..." : indicatif libre, pays détecté ; sinon : numéro local du pays par défaut
    const formatter = raw.startsWith('+') ? new AsYouType() : new AsYouType(props.defaultCountry);
    display.value = formatter.input(raw);
    country.value = raw.trim() === '' ? null : (formatter.getCountry() ?? null);
    sync();
};

// À la sortie du champ : normalise en format international si valide
const onBlur = () => {
    const p = parsed(display.value);
    if (p && p.isValid()) {
        display.value = p.formatInternational();
        country.value = p.country ?? null;
        sync();
    }
};

onMounted(() => {
    const p = parsed(props.modelValue);
    display.value = p && p.isValid() ? p.formatInternational() : (props.modelValue || '');
    country.value = p?.country ?? null;
    sync();
});
</script>

<template>
    <div>
        <div class="mt-1 relative">
            <span class="absolute inset-y-0 left-3 flex items-center text-lg pointer-events-none" aria-hidden="true">
                {{ flag }}
            </span>
            <input
                :id="id"
                ref="inputEl"
                :value="display"
                @input="onInput"
                @blur="onBlur"
                type="tel"
                inputmode="tel"
                autocomplete="tel"
                :required="required"
                placeholder="034 12 345 67 ou +33 6 12 34 56 78"
                class="block w-full pl-11 rounded-md shadow-sm border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                :class="{ 'border-red-400': isInvalid }"
            />
        </div>
        <p v-if="isInvalid" class="mt-1 text-xs text-red-600">
            Numéro invalide. Tape le numéro local ou ajoute l'indicatif (ex. +33).
        </p>
    </div>
</template>