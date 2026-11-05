// Painel de detalhes da equipe — consome `baseDadosEquipe` injetado pelo Blade
// (origem: tabela `team_members`), sem nenhum dado fixo neste arquivo.
document.addEventListener("DOMContentLoaded", () => {
    if (typeof baseDadosEquipe === "undefined") return;

    const cardsMembros = document.querySelectorAll(".membro-card");
    const containerDetalhes = document.getElementById("detalhes-membro-container");

    const txtNome = document.getElementById("detalhe-nome");
    const txtCargo = document.getElementById("detalhe-cargo");
    const txtBio = document.getElementById("detalhe-bio");
    const containerTags = document.getElementById("detalhe-tags");
    const linkLn = document.getElementById("link-linkedin");
    const linkGh = document.getElementById("link-github");

    cardsMembros.forEach(card => {
        card.addEventListener("click", () => {
            const key = card.getAttribute("data-member");
            const dados = baseDadosEquipe[key];

            if (!dados) return;

            // Clique no card já ativo fecha o painel
            if (card.classList.contains("card-ativo")) {
                card.classList.remove("card-ativo");
                containerDetalhes.style.display = "none";
                return;
            }

            cardsMembros.forEach(c => c.classList.remove("card-ativo"));
            card.classList.add("card-ativo");

            txtNome.innerText = dados.nome;
            txtCargo.innerText = dados.cargo;
            txtBio.innerText = dados.bio || "Sem biografia informada.";

            // Redes profissionais (LinkedIn / GitHub)
            if (dados.linkedin) {
                linkLn.setAttribute("href", dados.linkedin);
                linkLn.style.display = "";
            } else {
                linkLn.style.display = "none";
            }

            if (dados.github) {
                linkGh.setAttribute("href", dados.github);
                linkGh.style.display = "";
            } else {
                linkGh.style.display = "none";
            }

            // Badges de hard skills
            containerTags.innerHTML = "";
            (dados.skills || []).forEach(skill => {
                const spanSkill = document.createElement("span");
                spanSkill.title = skill.label || "";

                const icon = document.createElement("i");
                icon.className = skill.icon;
                spanSkill.appendChild(icon);

                if (skill.label) {
                    spanSkill.appendChild(document.createTextNode(" " + skill.label));
                }

                containerTags.appendChild(spanSkill);
            });

            if (!dados.skills || dados.skills.length === 0) {
                containerTags.innerHTML = "<span>Nenhuma tecnologia informada.</span>";
            }

            containerDetalhes.style.display = "block";
            containerDetalhes.scrollIntoView({ behavior: "smooth", block: "nearest" });
        });
    });
});
