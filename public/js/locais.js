document.addEventListener('DOMContentLoaded', () => {
    const CITIES = window.HAWB_CITIES || [];
    const UNITS_BY_CITY = window.HAWB_UNITS_BY_CITY || {};

    const input = document.getElementById('cityInput');
    const list = document.getElementById('citySuggestions');
    const cityPanel = document.getElementById('cityPanel');
    const typeChoice = document.getElementById('typeChoice');
    const noDataMsg = document.getElementById('noDataMsg');
    const unitsPanel = document.getElementById('unitsPanel');
    const unitsGrid = document.getElementById('unitsGrid');
    const unitsTitle = document.getElementById('unitsTitle');
    const detailPanel = document.getElementById('detailPanel');

    let selectedCity = '';
    let currentType = '';
    let userPos = null;
    let geoAsked = false;
    let highlightIndex = -1;

    const normalize = (s) => s.normalize('NFD').replace(/\p{M}/gu, '').toLowerCase();

    // Coordenadas das cidades (vêm do CNES junto com as unidades) — evita chamada externa
    const CITY_COORDS = {};
    Object.entries(window.HAWB_CITY_COORDS || {}).forEach(([name, coords]) => {
        CITY_COORDS[normalize(name)] = coords;
    });

    /* ================= Autocompletar da cidade ================= */

    function hideSuggestions() {
        list.hidden = true;
        list.innerHTML = '';
        highlightIndex = -1;
        input.setAttribute('aria-expanded', 'false');
    }

    function renderSuggestions(term) {
        const q = normalize(term);
        if (q.length < 2) { hideSuggestions(); return; }

        const matches = CITIES.filter(c => normalize(c).includes(q)).slice(0, 8);
        if (matches.length === 0) { hideSuggestions(); return; }

        list.innerHTML = '';
        matches.forEach((city, i) => {
            const li = document.createElement('li');
            li.setAttribute('role', 'option');
            li.dataset.city = city;
            li.id = 'city-opt-' + i;

            // Destaca o trecho digitado sem usar innerHTML (seguro)
            const pos = normalize(city).indexOf(q);
            const before = document.createElement('span');
            before.textContent = city.slice(0, pos);
            const mid = document.createElement('span');
            mid.className = 'match';
            mid.textContent = city.slice(pos, pos + term.length);
            const after = document.createElement('span');
            after.textContent = city.slice(pos + term.length);

            li.append(before, mid, after);
            li.addEventListener('click', () => selectCity(city));
            list.appendChild(li);
        });

        list.hidden = false;
        input.setAttribute('aria-expanded', 'true');
        highlightIndex = -1;
    }

    function moveHighlight(delta) {
        const options = [...list.querySelectorAll('li')];
        if (!options.length) return;
        highlightIndex = (highlightIndex + delta + options.length) % options.length;
        options.forEach((li, i) => li.classList.toggle('active', i === highlightIndex));
        input.setAttribute('aria-activedescendant', options[highlightIndex].id);
        options[highlightIndex].scrollIntoView({ block: 'nearest' });
    }

    input.addEventListener('input', () => renderSuggestions(input.value));

    input.addEventListener('keydown', (e) => {
        if (list.hidden) return;
        if (e.key === 'ArrowDown') { e.preventDefault(); moveHighlight(1); }
        else if (e.key === 'ArrowUp') { e.preventDefault(); moveHighlight(-1); }
        else if (e.key === 'Enter' && highlightIndex >= 0) {
            e.preventDefault();
            const li = list.querySelectorAll('li')[highlightIndex];
            if (li) selectCity(li.dataset.city);
        }
        else if (e.key === 'Escape') hideSuggestions();
    });

    document.addEventListener('click', (e) => {
        if (!e.target.closest('.search-box')) hideSuggestions();
    });

    /* ================= Distância ================= */

    function getUserPos() {
        if (geoAsked) return Promise.resolve(userPos);
        geoAsked = true;
        return new Promise((resolve) => {
            if (!navigator.geolocation) { resolve(null); return; }
            navigator.geolocation.getCurrentPosition(
                (p) => { userPos = [p.coords.latitude, p.coords.longitude]; resolve(userPos); },
                () => resolve(null),
                { enableHighAccuracy: false, timeout: 8000, maximumAge: 600000 }
            );
        });
    }

    async function getCityCoords(name) {
        const key = normalize(name);
        if (CITY_COORDS[key]) return CITY_COORDS[key];
        try {
            const url = 'https://nominatim.openstreetmap.org/search?format=json&limit=1&q='
                + encodeURIComponent(name + ', Bahia, Brasil');
            const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (data && data[0]) return [parseFloat(data[0].lat), parseFloat(data[0].lon)];
        } catch (e) { /* offline ou limite de requisições */ }
        return null;
    }

    function haversineKm(a, b) {
        const R = 6371;
        const rad = (d) => d * Math.PI / 180;
        const dLat = rad(b[0] - a[0]);
        const dLon = rad(b[1] - a[1]);
        const s = Math.sin(dLat / 2) ** 2
            + Math.cos(rad(a[0])) * Math.cos(rad(b[0])) * Math.sin(dLon / 2) ** 2;
        return 2 * R * Math.asin(Math.sqrt(s));
    }

    async function showDistance(cityName) {
        const el = document.getElementById('cityDistance');
        el.textContent = 'calculando distância…';
        const results = await Promise.all([getUserPos(), getCityCoords(cityName)]);
        const pos = results[0];
        const coords = results[1];

        if (!pos) { el.textContent = 'Ative a localização do navegador para ver a distância'; return; }
        if (!coords) { el.textContent = 'Distância indisponível no momento'; return; }

        const km = haversineKm(pos, coords);
        el.textContent = km < 1
            ? 'a menos de 1 km de você'
            : (km < 100 ? km.toFixed(1) : Math.round(km)) + ' km de você';
    }

    /* ================= Passo: cidade escolhida ================= */

    function unitsOf(cityName, type) {
        const key = normalize(cityName);
        let units = UNITS_BY_CITY[cityName] || [];
        if (!units.length) {
            const foundKey = Object.keys(UNITS_BY_CITY).find(k => normalize(k) === key);
            units = foundKey ? UNITS_BY_CITY[foundKey] : [];
        }
        return type ? units.filter(u => u.type === type) : units;
    }

    function selectCity(cityName) {
        selectedCity = cityName;
        currentType = '';
        input.value = cityName;
        hideSuggestions();

        document.getElementById('cityName').textContent = cityName;
        document.getElementById('cityState').textContent = 'Bahia — BA';

        const units = unitsOf(cityName);
        const upaCount = units.filter(u => u.type === 'upa').length;
        const ubsCount = units.filter(u => u.type === 'ubs').length;

        if (units.length > 0) {
            typeChoice.hidden = false;
            noDataMsg.hidden = true;
            document.getElementById('upaCount').textContent = '(' + upaCount + ')';
            document.getElementById('ubsCount').textContent = '(' + ubsCount + ')';
        } else {
            typeChoice.hidden = true;
            noDataMsg.hidden = false;
        }

        unitsPanel.hidden = true;
        detailPanel.hidden = true;
        cityPanel.hidden = false;
        showDistance(cityName);

        cityPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.getElementById('changeCity').addEventListener('click', () => {
        cityPanel.hidden = true;
        unitsPanel.hidden = true;
        detailPanel.hidden = true;
        input.value = '';
        input.focus();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });

    /* ================= Passo: lista de unidades ================= */

    document.getElementById('typeButtons').addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-filter');
        if (!btn) return;
        document.querySelectorAll('#typeButtons .btn-filter').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        showUnits(btn.dataset.type);
    });

    document.getElementById('backToTypes').addEventListener('click', () => {
        unitsPanel.hidden = true;
        detailPanel.hidden = true;
        document.querySelectorAll('#typeButtons .btn-filter').forEach(b => b.classList.remove('active'));
        cityPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });

    function showUnits(type) {
        currentType = type;
        const units = unitsOf(selectedCity, type);
        const label = type === 'upa' ? 'UPAs' : 'UBS';
        unitsTitle.textContent = label + ' em ' + selectedCity;
        unitsGrid.innerHTML = '';

        units.forEach((unit) => {
            const card = document.createElement('article');
            card.className = 'local-card';

            const tags = document.createElement('div');
            tags.className = 'card-header-tags';
            const badge = document.createElement('span');
            badge.className = 'tag-badge badge-' + unit.type;
            badge.textContent = unit.type.toUpperCase();
            const city = document.createElement('span');
            city.className = 'tag-city';
            city.textContent = unit.cityLabel;
            tags.append(badge, city);

            const h2 = document.createElement('h2');
            h2.textContent = unit.name;

            const addr = document.createElement('div');
            addr.className = 'info-line';
            const addrIcon = document.createElement('span');
            addrIcon.className = 'icon';
            addrIcon.setAttribute('aria-hidden', 'true');
            addrIcon.textContent = '📍';
            const addrP = document.createElement('p');
            addrP.textContent = unit.address;
            addr.append(addrIcon, addrP);

            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'btn-toggle-map';
            btn.innerHTML = '<span class="map-icon"></span><span class="btn-text">Ver no mapa</span>';
            btn.addEventListener('click', () => showDetail(unit));

            card.append(tags, h2, addr, btn);
            unitsGrid.appendChild(card);
        });

        unitsPanel.hidden = false;
        detailPanel.hidden = true;
        unitsPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /* ================= Passo: detalhe (mapa + informações) ================= */

    function showDetail(unit) {
        const badge = document.getElementById('detailBadge');
        badge.textContent = unit.type.toUpperCase();
        badge.className = 'tag-badge badge-' + unit.type;

        document.getElementById('detailCity').textContent = unit.cityLabel;
        document.getElementById('detailName').textContent = unit.name;
        document.getElementById('detailAddress').textContent = unit.address;
        document.getElementById('detailPhone').textContent = unit.phone;
        document.getElementById('detailHours').textContent = unit.hours;

        const services = document.getElementById('detailServices');
        services.innerHTML = '';
        unit.services.forEach((s) => {
            const span = document.createElement('span');
            span.textContent = s;
            services.appendChild(span);
        });

        // Com coordenadas do CNES o mapa centraliza exato na unidade; sem, busca por texto
        const hasCoords = typeof unit.lat === 'number' && typeof unit.lng === 'number';
        const query = unit.name + ', ' + unit.address + ', ' + unit.cityLabel + ', BA, Brasil';
        const mapQuery = hasCoords ? unit.lat + ',' + unit.lng : query;
        document.getElementById('mapFrame').src =
            'https://maps.google.com/maps?q=' + encodeURIComponent(mapQuery) + '&z=15&output=embed';
        document.getElementById('directionsLink').href =
            'https://www.google.com/maps/dir/?api=1&destination=' + encodeURIComponent(query);

        detailPanel.hidden = false;
        detailPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    document.getElementById('backToUnits').addEventListener('click', () => {
        detailPanel.hidden = true;
        unitsPanel.hidden = false;
        unitsPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
    });
});
