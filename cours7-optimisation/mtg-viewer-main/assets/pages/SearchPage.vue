<script setup>
import {
    onMounted,
    onUnmounted,
    ref,
    watch,
} from 'vue';
import { fetchArtists, fetchSetCodes, searchCards } from '../services/cardService';

const cards = ref([]);
const loadingCards = ref(false);
const setCodes = ref([]);
const artists = ref([]);
const query = ref('');
const filters = ref({
    setCode: '',
    artistId: '',
});
let debounceTimeout = null;

async function loadFilters() {
    const [setCodeResponse, artistResponse] = await Promise.all([
        fetchSetCodes(),
        fetchArtists(),
    ]);
    setCodes.value = setCodeResponse.items;
    artists.value = artistResponse.items;
}

async function runSearch() {
    if (query.value.trim().length < 3) {
        cards.value = [];
        loadingCards.value = false;
        return;
    }

    loadingCards.value = true;

    try {
        const response = await searchCards({
            q: query.value.trim(),
            setCode: filters.value.setCode,
            artistId: filters.value.artistId,
        });
        cards.value = response.items;
    } finally {
        loadingCards.value = false;
    }
}

function debounceSearch() {
    clearTimeout(debounceTimeout);
    debounceTimeout = setTimeout(() => {
        runSearch();
    }, 300);
}

onMounted(() => {
    loadFilters();
});

watch([query, filters], () => {
    debounceSearch();
}, { deep: true });

onUnmounted(() => {
    clearTimeout(debounceTimeout);
});
</script>

<template>
    <div>
        <h1>Rechercher une Carte</h1>
    </div>
    <div class="filters">
        <label class="search-input">
            Nom
            <input v-model="query" type="search" placeholder="Saisir au moins 3 caractères" />
        </label>
        <label>
            Extension
            <select v-model="filters.setCode">
                <option value="">Toutes</option>
                <option v-for="setCode in setCodes" :key="setCode" :value="setCode">
                    {{ setCode }}
                </option>
            </select>
        </label>
        <label>
            Artiste
            <select v-model="filters.artistId">
                <option value="">Tous</option>
                <option v-for="artist in artists" :key="artist.id" :value="artist.id">
                    {{ artist.name }}
                </option>
            </select>
        </label>
    </div>
    <div class="card-list">
        <div v-if="query.length < 3">La recherche démarre automatiquement à partir de 3 caractères.</div>
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="results-summary" v-if="query.length >= 3">
                {{ cards.length }} résultat(s) affiché(s), limité(s) à 20.
            </div>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} - {{ card.setCode }}
                </router-link>
                <div v-if="card.artist" class="card-result-meta">
                    {{ card.artist.name }}
                </div>
            </div>
            <div v-if="query.length >= 3 && cards.length === 0">Aucune carte trouvée.</div>
        </div>
    </div>
</template>
