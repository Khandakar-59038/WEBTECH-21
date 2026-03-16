// js/filter.js --- Filter programmes by level and live search
// CTEC2712N --- Prodip

document.addEventListener('DOMContentLoaded', function () {

    const filterBtns = document.querySelectorAll('.filter-btn');
    const cards = document.querySelectorAll('.programme-card');
    const searchInput = document.getElementById('search-input');
    const grid = document.getElementById('programme-grid');

    // ── FILTER BY LEVEL ──────────────────────────────────────────
    filterBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {

            // Mark this button as active, deactivate all others
            filterBtns.forEach(function (b) { b.classList.remove('active'); });
            btn.classList.add('active');

            const selected = btn.getAttribute('data-filter');

            cards.forEach(function (card) {
                // data-level on each card is set by PHP from the database
                if (selected === 'all' || card.getAttribute('data-level') === selected) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });

            showEmptyMessage();
        });
    });

    // ── LIVE SEARCH ──────────────────────────────────────────────
    searchInput.addEventListener('input', function () {
        const query = searchInput.value.toLowerCase().trim();

        cards.forEach(function (card) {
            const title = card.querySelector('h2').textContent.toLowerCase();
            const desc  = card.querySelector('p').textContent.toLowerCase();

            if (title.includes(query) || desc.includes(query)) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });

        showEmptyMessage();
    });

    // ── SHOW "NO RESULTS" MESSAGE IF ALL CARDS ARE HIDDEN ────────
    function showEmptyMessage() {
        // Remove any existing message first
        const existing = grid.querySelector('.no-results');
        if (existing) existing.remove();

        const visible = Array.from(cards).filter(function (c) {
            return !c.classList.contains('hidden');
        });

        if (visible.length === 0) {
            const msg = document.createElement('p');
            msg.className = 'no-results';
            msg.textContent = 'No programmes match your search. Try a different term.';
            grid.appendChild(msg);
        }
    }

});