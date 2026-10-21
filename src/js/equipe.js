// Base de Conhecimento e Histórico de Atuação da Equipe HAWB
const baseDadosEquipe = {
    alisson: {
        nome: "João Victor Leão Silva",
        cargo: "Desenvolvedor full-stack",
        bio: "Técnico em informática para internet.",
        skills: [
            '<i class="devicon-html5-plain colored" title="HTML5"></i>',
            '<i class="devicon-css3-plain colored" title="CSS3"></i>',
            '<i class="devicon-javascript-plain colored" title="JavaScript"></i>',
            '<i class="devicon-github-original colored" title="GitHub"></i>',
            '<i class="devicon-figma-plain colored" title="Figma"></i>',
            '<i class="devicon-nodejs-plain colored" title="Node.js"></i>',
            '<i class="devicon-php-plain colored" title="PHP"></i>',
            '<i class="devicon-laravel-plain colored" title="Laravel"></i>'
        ],
        linkedin: "https://linkedin.com/in/alisson-hawb",
        github: "https://github.com/alisson-hawb"
    },
    elen: {
        nome: "Ariel França",
        cargo: "Product Owner & Desenvolvedor full-stack",
        bio: "Técnico em informática para internet.",
        skills: [
            '<i class="devicon-html5-plain colored" title="HTML5"></i>',
            '<i class="devicon-css3-plain colored" title="CSS3"></i>',
            '<i class="devicon-javascript-plain colored" title="JavaScript"></i>',
            '<i class="devicon-github-original colored" title="GitHub"></i>',
            '<i class="devicon-figma-plain colored" title="Figma"></i>',
            '<i class="devicon-nodejs-plain colored" title="Node.js"></i>',
            '<i class="devicon-php-plain colored" title="PHP"></i>',
            '<i class="devicon-laravel-plain colored" title="Laravel"></i>'
        ],
        linkedin: "https://linkedin.com/in/elen-hawb",
        github: "https://github.com/elen-hawb"
    },
    gabriel: {
        nome: "Derick Souza",
        cargo: "Desenvolvedor full-stack",
        bio: "Técnico em informática para internet.",
        skills: [
            '<i class="devicon-html5-plain colored" title="HTML5"></i>',
            '<i class="devicon-css3-plain colored" title="CSS3"></i>',
            '<i class="devicon-javascript-plain colored" title="JavaScript"></i>',
            '<i class="devicon-github-original colored" title="GitHub"></i>',
            '<i class="devicon-figma-plain colored" title="Figma"></i>',
            '<i class="devicon-nodejs-plain colored" title="Node.js"></i>',
            '<i class="devicon-php-plain colored" title="PHP"></i>',
            '<i class="devicon-laravel-plain colored" title="Laravel"></i>'
        ], 
        linkedin: "https://linkedin.com/in/gabriel-hawb",
        github: "https://github.com/gabriel-hawb"
    },
    italo: {
        nome: "Adryano Silva",
        cargo: "Desenvolvedor full-stack",
        bio: "Técnico em informática para internet.",
        skills: [
            '<i class="devicon-html5-plain colored" title="HTML5"></i>',
            '<i class="devicon-css3-plain colored" title="CSS3"></i>',
            '<i class="devicon-javascript-plain colored" title="JavaScript"></i>',
            '<i class="devicon-github-original colored" title="GitHub"></i>',
            '<i class="devicon-figma-plain colored" title="Figma"></i>',
            '<i class="devicon-nodejs-plain colored" title="Node.js"></i>',
            '<i class="devicon-php-plain colored" title="PHP"></i>',
            '<i class="devicon-laravel-plain colored" title="Laravel"></i>'
        ],
        linkedin: "https://linkedin.com/in/italo-hawb",
        github: "https://github.com/italo-hawb"
    },
    roberta: {
        nome: "Matheus Costa",
        cargo: "Desenvolvedor full-stack",
        bio: "Técnico em informática para internet.",
        skills: [
            '<i class="devicon-html5-plain colored" title="HTML5"></i>',
            '<i class="devicon-css3-plain colored" title="CSS3"></i>',
            '<i class="devicon-javascript-plain colored" title="JavaScript"></i>',
            '<i class="devicon-github-original colored" title="GitHub"></i>',
            '<i class="devicon-figma-plain colored" title="Figma"></i>',
            '<i class="devicon-nodejs-plain colored" title="Node.js"></i>',
            '<i class="devicon-php-plain colored" title="PHP"></i>',
            '<i class="devicon-laravel-plain colored" title="Laravel"></i>'
        ],
        linkedin: "https://linkedin.com/in/roberta-hawb",
        github: "https://github.com/roberta-hawb"
    },
   ronald: {
        nome: "Ana Luisa",
        cargo: "Desenvolvedora | Auxiliar do projeto",
        bio: "Engenheira de software | Professora de computação | Pesquisadora | Microsoft Certified Educator | Redadora de Conteúdo Web | Palestrante.",
        skills: [
            '<i class="devicon-html5-plain colored" title="HTML5"></i>',
            '<i class="devicon-css3-plain colored" title="CSS3"></i>',
            '<i class="devicon-javascript-plain colored" title="JavaScript"></i>',
            '<i class="devicon-github-original colored" title="GitHub"></i>',
            '<i class="devicon-figma-plain colored" title="Figma"></i>',
            '<i class="devicon-nodejs-plain colored" title="Node.js"></i>',
            '<i class="devicon-php-plain colored" title="PHP"></i>',
            '<i class="devicon-laravel-plain colored" title="Laravel"></i>',
            '<i class="devicon-postgresql-plain colored" title="PostgreSQL"></i>'
        ],
        linkedin: "https://www.linkedin.com/in/PERFIL-ANA-LUISA",
        github: "https://github.com/USUARIO-ANA-LUISA"
    }
};

document.addEventListener("DOMContentLoaded", () => {
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

            // Caso o usuário clique em um cartão que já está selecionado/ativo, fecha o painel
            if (card.classList.contains("card-ativo")) {
                card.classList.remove("card-ativo");
                containerDetalhes.style.display = "none";
                return;
            }

            // Remove o estado ativo de todos os outros componentes do grid
            cardsMembros.forEach(c => c.classList.remove("card-ativo"));
            card.classList.add("card-ativo");

            if (dados) {
                // Seta os valores dinamicamente
                txtNome.innerText = dados.nome;
                txtCargo.innerText = dados.cargo;
                txtBio.innerText = dados.bio;
                
                // Atualiza links das redes
                linkLn.setAttribute("href", dados.linkedin);
                linkGh.setAttribute("href", dados.github);

                // Constrói os badges de Hard Skills com ícones
                containerTags.innerHTML = "";
                dados.skills.forEach(skill => {
                    const spanSkill = document.createElement("span");
                    spanSkill.innerHTML = skill; // ALTERADO AQUI: innerHTML renderiza o ícone <i class="...">
                    containerTags.appendChild(spanSkill);
                });

                // Apresenta o painel na viewport
                containerDetalhes.style.display = "block";
                containerDetalhes.scrollIntoView({ behavior: "smooth", block: "nearest" });
            }
        });
    });
});