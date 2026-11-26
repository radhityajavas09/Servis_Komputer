import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';

window.Alpine = Alpine;
window.Chart = Chart;

/**
 * Komponen Live Search & Filter Instan tanpa reload halaman
 * Menjaga kursor tetap aktif di kolom input saat pengguna mengetik.
 */
window.liveTableSearch = function (endpoint, containerId, initialQuery = '') {
    return {
        endpoint: endpoint,
        containerId: containerId,
        searchQuery: initialQuery,
        loading: false,

        init() {
            // Tangkap klik pagination agar pergantian halaman juga mulus tanpa reload
            const container = document.getElementById(this.containerId);
            if (container) {
                container.addEventListener('click', (e) => {
                    const link = e.target.closest('a');
                    if (link && link.href && link.href.includes('page=') && container.contains(link)) {
                        e.preventDefault();
                        this.fetchUrl(link.href);
                    }
                });
            }
        },

        applyFilter() {
            const form = this.$refs.filterForm;
            if (!form) return;

            const formData = new FormData(form);
            const params = new URLSearchParams(formData);

            for (const [key, val] of Array.from(params.entries())) {
                if (!val || typeof val !== 'string' || val.trim() === '') {
                    params.delete(key);
                }
            }

            const queryString = params.toString();
            const url = this.endpoint + (queryString ? '?' + queryString : '');
            this.fetchUrl(url);
        },

        fetchUrl(url) {
            this.loading = true;
            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(res => {
                if (!res.ok) throw new Error('Network error');
                return res.text();
            })
            .then(html => {
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');
                const newContainer = doc.getElementById(this.containerId);
                const currentContainer = document.getElementById(this.containerId);

                if (newContainer && currentContainer) {
                    currentContainer.innerHTML = newContainer.innerHTML;
                    if (window.Alpine && typeof window.Alpine.initTree === 'function') {
                        window.Alpine.initTree(currentContainer);
                    }
                }

                window.history.replaceState({}, '', url);
            })
            .catch(err => {
                console.error('Live search error:', err);
            })
            .finally(() => {
                this.loading = false;
            });
        },

        clearSearch() {
            this.searchQuery = '';
            this.$nextTick(() => {
                if (this.$refs.searchInput) {
                    this.$refs.searchInput.focus();
                }
                this.applyFilter();
            });
        }
    };
};

Alpine.data('liveTableSearch', (endpoint, containerId, initialQuery = '') =>
    window.liveTableSearch(endpoint, containerId, initialQuery)
);

Alpine.start();
