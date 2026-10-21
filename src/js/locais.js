document.addEventListener("DOMContentLoaded", () => {
    const typeButtons = document.querySelectorAll("#filter-type .btn-filter");
    const cityButtons = document.querySelectorAll("#filter-city .btn-filter");
    const cards = document.querySelectorAll(".local-card");

    let activeType = "todos";
    let activeCity = "todos";

    // Função Combinada de Filtragem (Tipo + Cidade simultaneamente)
    function applyCombinedFilters() {
        cards.forEach(card => {
            const cardType = card.getAttribute("data-type");
            const cardCity = card.getAttribute("data-city");

            const matchesType = (activeType === "todos" || cardType === activeType);
            const matchesCity = (activeCity === "todos" || cardCity === activeCity);

            if (matchesType && matchesCity) {
                card.style.display = "flex";
            } else {
                card.style.display = "none";
            }
        });
    }

    // Escuta eventos dos botões de Unidade
    typeButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            typeButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            activeType = btn.getAttribute("data-type");
            applyCombinedFilters();
        });
    });

    // Escuta eventos dos botões de Cidade
    cityButtons.forEach(btn => {
        btn.addEventListener("click", () => {
            cityButtons.forEach(b => b.classList.remove("active"));
            btn.classList.add("active");
            activeCity = btn.getAttribute("data-city");
            applyCombinedFilters();
        });
    });
});

// Manipula a expansão do Mapa de forma limpa e contextualizada
function toggleMap(id) {
    const mapWrapper = document.getElementById(`map-${id}`);
    const card = document.getElementById(`card-${id}`);
    const btn = card.querySelector(".btn-toggle-map");
    const btnText = btn.querySelector(".btn-text");
    const btnIcon = btn.querySelector(".map-icon");

    if (mapWrapper.style.display === "block") {
        mapWrapper.style.display = "none";
        btnText.innerText = "Como Chegar";
        btnIcon.innerText = "Compass";
    } else {
        mapWrapper.style.display = "block";
        btnText.innerText = "Fechar Mapa";
        btnIcon.innerText = "❌";
    }
}