// =============================================
// Karim Telecom — JavaScript principal
// =============================================

document.addEventListener('DOMContentLoaded', function () {

    // ── Menu hamburger ──
    var hamburger  = document.getElementById('hamburger');
    var mobileNav  = document.getElementById('mobile-nav');
    var searchMob  = document.getElementById('searchMobile');

    if (hamburger) {
        hamburger.addEventListener('click', function () {
            var open = mobileNav && mobileNav.classList.toggle('open');
            // Afficher/masquer la search mobile aussi
            if (searchMob) searchMob.style.display = open ? 'block' : 'none';
        });
    }

    // Fermer le menu si on clique en dehors
    document.addEventListener('click', function(e) {
        if (!e.target.closest('header')) {
            if (mobileNav) mobileNav.classList.remove('open');
            if (searchMob) searchMob.style.display = 'none';
        }
    });

    // ── Filtres catalogue ──
    var filterMarque  = document.getElementById('filter-marque');
    var filterBudget  = document.getElementById('filter-budget');
    var filterCat     = document.getElementById('filter-categorie');
    var filterEtat    = document.getElementById('filter-etat');
    var cards         = document.querySelectorAll('.product-card[data-marque]');

    function applyFilters() {
        var marque = filterMarque ? filterMarque.value.toLowerCase() : 'all';
        var budget = filterBudget ? filterBudget.value : 'all';
        var cat    = filterCat    ? filterCat.value.toLowerCase()    : 'all';
        var etat   = filterEtat   ? filterEtat.value : 'all'; // comparaison directe pour les accents
        var count  = 0;

        cards.forEach(function(card) {
            var cm = (card.getAttribute('data-marque')    || '').toLowerCase().trim();
            var cp = parseInt(card.getAttribute('data-prix') || '0', 10);
            var cc = (card.getAttribute('data-categorie') || '').toLowerCase().trim();
            var ce = (card.getAttribute('data-etat')      || '').trim(); // pas de toLowerCase pour les accents

            var show = true;

            if (marque !== 'all' && cm !== marque) show = false;
            if (cat    !== 'all' && cc !== cat)    show = false;
            if (etat   !== 'all' && ce !== etat)   show = false; // comparaison directe

            if (budget === 'moins200' && cp >= 200000)                     show = false;
            else if (budget === '200-500' && (cp < 200000 || cp >= 500000)) show = false;
            else if (budget === 'plus500' && cp < 500000)                  show = false;

            card.style.display = show ? '' : 'none';
            if (show) count++;
        });

        var counter = document.getElementById('products-count');
        if (counter) counter.textContent = count + ' produit' + (count > 1 ? 's' : '');
    }

    if (filterMarque) filterMarque.addEventListener('change', applyFilters);
    if (filterBudget) filterBudget.addEventListener('change', applyFilters);
    if (filterCat)    filterCat.addEventListener('change', applyFilters);
    if (filterEtat)   filterEtat.addEventListener('change', applyFilters);

    // ── Sticky header shadow ──
    var header = document.querySelector('header');
    if (header) {
        window.addEventListener('scroll', function () {
            header.style.boxShadow = window.scrollY > 10
                ? '0 4px 20px rgba(0,0,0,.12)'
                : '0 1px 3px rgba(0,0,0,.08)';
        });
    }
});
