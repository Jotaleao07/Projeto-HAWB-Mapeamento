document.addEventListener("DOMContentLoaded", () => {
    const cards = document.querySelectorAll(".sintoma-card");
    const btnAnalisar = document.getElementById("btnAnalisar");
    
    const statusEmergencia = document.getElementById("statusEmergencia");
    const statusUpa = document.getElementById("statusUpa");
    const statusUbs = document.getElementById("statusUbs");

    // Efeito Visual: Altera o fundo e borda do bloco ao selecionar
    cards.forEach(card => {
        const checkbox = card.querySelector("input[type='checkbox']");
        
        checkbox.addEventListener("change", () => {
            if (checkbox.checked) {
                card.classList.add("selected-active");
            } else {
                card.classList.remove("selected-active");
            }
        });
    });

    // Lógica do botão analisador
    btnAnalisar.addEventListener("click", () => {
        // Reseta as caixas invisíveis
        statusEmergencia.style.display = "none";
        statusUpa.style.display = "none";
        statusUbs.style.display = "none";

        const selecionados = document.querySelectorAll(".sintoma-card input:checked");

        if (selecionados.length === 0) {
            alert("Por favor, selecione ao menos um sintoma para análise.");
            return;
        }

        let maiorGravidade = "ubs";

        // Verifica qual a maior gravidade selecionada
        selecionados.forEach(input => {
            const card = input.closest(".sintoma-card");
            const severidade = card.getAttribute("data-severity");

            if (severidade === "emergência") {
                maiorGravidade = "emergência";
            } else if (severidade === "upa" && maiorGravidade !== "emergência") {
                maiorGravidade = "upa";
            }
        });

        // Mostra o container equivalente conforme a imagem de exemplo
        if (maiorGravidade === "emergência") {
            statusEmergencia.style.display = "block";
            statusEmergencia.scrollIntoView({ behavior: "smooth" });
        } else if (maiorGravidade === "upa") {
            statusUpa.style.display = "block";
            statusUpa.scrollIntoView({ behavior: "smooth" });
        } else {
            statusUbs.style.display = "block";
            statusUbs.scrollIntoView({ behavior: "smooth" });
        }
    });
});