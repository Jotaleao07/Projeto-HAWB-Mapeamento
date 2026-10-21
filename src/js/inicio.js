document.addEventListener("DOMContentLoaded", () => {
    
    // Configuração do redirecionamento dos cards de serviços
    const cards = document.querySelectorAll(".service-card");
    
    cards.forEach(card => {
        card.addEventListener("click", () => {
            const targetDestination = card.getAttribute("data-target");
            
            // Aqui faz o redirecionamento. 
            // Se no futuro trocar por páginas reais, ex: window.location.href = "sintomas.html";
            window.location.hash = targetDestination; 
            
            // Log no console apenas para fins de debug e testes
            console.log(`Redirecionando para: ${targetDestination}`);
        });
    });

    // Ação do Botão "Começar Agora" (Leva até a seção de serviços)
    const btnComecar = document.getElementById("btnComecar");
    if(btnComecar) {
        btnComecar.addEventListener("click", () => {
            document.querySelector(".services-section").scrollIntoView({ behavior: "smooth" });
        });
    }
});