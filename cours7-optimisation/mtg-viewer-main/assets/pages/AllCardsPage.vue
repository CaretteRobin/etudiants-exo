<script setup>
import { onMounted, ref, watch } from 'vue';
import { fetchArtists, fetchCards, fetchSetCodes } from '../services/cardService';

const cards = ref([]);
const loadingCards = ref(true);
const setCodes = ref([]);
const artists = ref([]);
const pagination = ref({
    page: 1,
    totalPages: 1,
    totalItems: 0,
    pageSize: 100,
});
const filters = ref({
    setCode: '',
    artistId: '',
});

async function loadCards() {
    loadingCards.value = true;

    try {
        const response = await fetchCards({
            page: pagination.value.page,
            setCode: filters.value.setCode,
            artistId: filters.value.artistId,
        });

        cards.value = response.items;
        pagination.value = response.pagination;
    } finally {
        loadingCards.value = false;
    }
}

async function loadFilters() {
    const [setCodeResponse, artistResponse] = await Promise.all([
        fetchSetCodes(),
        fetchArtists(),
    ]);
    setCodes.value = setCodeResponse.items;
    artists.value = artistResponse.items;
}

function updatePage(page) {
    if (page < 1 || page > pagination.value.totalPages || page === pagination.value.page) {
        return;
    }

    pagination.value.page = page;
}

onMounted(() => {
    loadFilters();
    loadCards();
});

watch(filters, () => {
    if (pagination.value.page !== 1) {
        pagination.value.page = 1;
        return;
    }

    loadCards();
}, { deep: true });

watch(() => pagination.value.page, () => {
    loadCards();
});

</script>

<template>
    <div>
        <h1>Toutes les cartes</h1>
    </div>
    <div class="filters">
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
        <div v-if="loadingCards">Loading...</div>
        <div v-else>
            <div class="results-summary">
                {{ pagination.totalItems }} cartes, page {{ pagination.page }} / {{ pagination.totalPages }}
            </div>
            <div class="card-result" v-for="card in cards" :key="card.id">
                <router-link :to="{ name: 'get-card', params: { uuid: card.uuid } }">
                    {{ card.name }} <span>({{ card.setCode }})</span>
                </router-link>
                <div v-if="card.artist" class="card-result-meta">
                    {{ card.artist.name }}
                </div>
            </div>
            <div v-if="cards.length === 0">Aucune carte trouvée.</div>
            <div class="pagination">
                <button type="button" :disabled="pagination.page <= 1" @click="updatePage(pagination.page - 1)">
                    Précédent
                </button>
                <button type="button" :disabled="pagination.page >= pagination.totalPages" @click="updatePage(pagination.page + 1)">
                    Suivant
                </button>
            </div>
        </div>
    </div>
</template>
