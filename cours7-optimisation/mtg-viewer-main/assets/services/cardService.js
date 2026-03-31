function buildQueryString(params = {}) {
    const searchParams = new URLSearchParams();
    Object.entries(params).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
            searchParams.set(key, value);
        }
    });

    return searchParams.toString();
}

export async function fetchCards(params = {}) {
    const queryString = buildQueryString(params);
    const response = await fetch(`/api/card${queryString ? `?${queryString}` : ''}`);
    if (!response.ok) throw new Error('Failed to fetch cards');

    return response.json();
}

export async function fetchCard(uuid) {
    const response = await fetch(`/api/card/${uuid}`);
    if (response.status === 404) return null;
    if (!response.ok) throw new Error('Failed to fetch card');
    const card = await response.json();
    card.text = card.text?.replaceAll('\\n', '\n') ?? '';

    return card;
}

export async function searchCards(params = {}) {
    const queryString = buildQueryString(params);
    const response = await fetch(`/api/card/search?${queryString}`);
    if (!response.ok) throw new Error('Failed to search cards');

    return response.json();
}

export async function fetchSetCodes() {
    const response = await fetch('/api/card/set-codes');
    if (!response.ok) throw new Error('Failed to fetch set codes');

    return response.json();
}

export async function fetchArtists() {
    const response = await fetch('/api/card/artists');
    if (!response.ok) throw new Error('Failed to fetch artists');

    return response.json();
}
