// Base de dados das Indicações de Especialidade
const dadosEspecialidades = {
    otorrino: {
        titulo: "Otorrinolaringologia",
        oqueFaz: "Médico especializado no diagnóstico e tratamento clínico e cirúrgico das doenças que acometem o ouvido, o nariz e a garganta, englobando também estruturas correlatas da cabeça e pescoço.",
        doencas: "Amigdalite, sinusite, rinite crônica, otite (infecção de ouvido), desvio de septo nasal, perda auditiva, tontura/labirintite e distúrbios do sono (ronco e apneia).",
        onde: "Disponível nas Unidades Básicas de Saúde (UBS) de Salvador e Camaçari para triagem e encaminhamento ambulatorial via regulação do SUS."
    },
    odonto: {
        titulo: "Odontologia",
        oqueFaz: "Profissional encarregado da saúde bucal completa, realizando diagnóstico, prevenção e tratamento de problemas estruturais nos dentes, gengivas e ossos maxilares.",
        doencas: "Cáries profundas, gengivite, periodontite, perda dentária, tártaro acumulado e infecções endodônticas.",
        onde: "Atendimento direto em Clínicas Odontológicas integradas às UBSs de Lauro de Freitas e Dias d'Ávila."
    },
    gineco: {
        titulo: "Ginecologia e Pré-natal",
        oqueFaz: "Acompanhamento integral do sistema reprodutor feminino e cuidados obstétricos rigorosos durante a gestação (Pré-natal), garantindo a integridade da mãe e do feto.",
        doencas: "Infecções vaginais, miomas uterinos, irregularidades no ciclo menstrual, acompanhamento de gravidez de risco e prevenção do câncer de colo de útero.",
        onde: "Marcação prioritária nas Unidades de Saúde da Família (USF) e UBS em toda a região de abrangência."
    },
    pediatria: {
        titulo: "Pediatria",
        oqueFaz: "Especialista responsável pela supervisão do desenvolvimento físico e mental de crianças e adolescentes, atuando tanto de forma preventiva quanto curativa.",
        doencas: "Asma infantil, infecções respiratórias agudas, desnutrição ou obesidade infantil, viroses sazonais e acompanhamento vacinal.",
        onde: "Disponível em regime ambulatorial nas UBSs e para casos de Urgência e Emergência 24h nas UPAs da rede."
    },
    cardio: {
        titulo: "Cardiologia",
        oqueFaz: "Especialidade médica que se ocupa do diagnóstico, prevenção e tratamento das enfermidades que acometem o coração e os vasos sanguíneos do sistema circulatório.",
        doencas: "Hipertensão arterial sistêmica, insuficiência cardíaca, arritmias, infarto agudo do miocárdio e sopros no coração.",
        onde: "Encaminhamento especializado após consulta com Clínico Geral na UBS de referência."
    },
    clinica: {
        titulo: "Clínica Geral",
        oqueFaz: "Atua na visão global do paciente. É o primeiro ponto de contato na atenção primária, tratando de patologias comuns e gerenciando encaminhamentos de alta complexidade.",
        doencas: "Diabetes mellitus, infecções urinárias, dores crônicas, quadros gripais, anemias e acompanhamento preventivo contínuo.",
        onde: "Disponível em 100% das Unidades Básicas de Saúde (UBS) de forma diária."
    },
    vacina: {
        titulo: "Serviço de Vacinação",
        oqueFaz: "Setor responsável pela administração de imunobiológicos recomendados pelo Programa Nacional de Imunização (PNI) para proteção coletiva e individual.",
        doencas: "Prevenção ativa contra Influenza, Tétano, Hepatites, Febre Amarela, Tríplice Viral, HPV, entre outras doenças sazonais ou crônicas.",
        onde: "Salas de vacina abertas ao público em todas as UBSs e USFs da região metropolitana."
    }
};

document.addEventListener("DOMContentLoaded", () => {
    // LÓGICA DE EXIBIÇÃO DAS INDICAÇÕES
    const cards = document.querySelectorAll(".especialidade-card");
    const containerIndicacao = document.getElementById("indicacoes-container");
    const indTitulo = document.getElementById("indicacao-titulo");
    const indOqueFaz = document.getElementById("indicacao-oque-faz");
    const indDoencas = document.getElementById("indicacao-doencas");
    const indOnde = document.getElementById("indicacao-onde");

    cards.forEach(card => {
        card.addEventListener("click", () => {
            // Remove seleção ativa anterior
            cards.forEach(c => c.classList.remove("selected-card"));
            
            // Ativa o card clicado
            card.classList.add("selected-card");

            const key = card.getAttribute("data-target");
            const dados = dadosEspecialidades[key];

            if (dados) {
                indTitulo.innerText = dados.titulo;
                indOqueFaz.innerText = dados.oqueFaz;
                indDoencas.innerText = dados.doencas;
                indOnde.innerHTML = dados.onde;

                containerIndicacao.style.display = "block";
                containerIndicacao.scrollIntoView({ behavior: "smooth", block: "nearest" });
            }
        });
    });

    // LÓGICA DO SIMULADOR DE IMC
    const form = document.getElementById("imc-form");
    const inputs = form.querySelectorAll("input, select");
    const btnCalcular = document.getElementById("btn-calcular");
    const resultBox = document.getElementById("imc-resultado");
    const valImc = document.getElementById("val-imc");
    const classImc = document.getElementById("class-imc");
    const orientacaoImc = document.getElementById("orientacao-imc");

    function verificarPreenchimento() {
        let valido = true;
        inputs.forEach(i => { if (!i.value) valido = false; });
        
        if (valido) {
            btnCalcular.classList.add("ready");
            btnCalcular.removeAttribute("disabled");
        } else {
            btnCalcular.classList.remove("ready");
            btnCalcular.setAttribute("disabled", "true");
        }
    }

    inputs.forEach(i => {
        i.addEventListener("input", verificarPreenchimento);
        i.addEventListener("change", verificarPreenchimento);
    });

    form.addEventListener("submit", (e) => {
        e.preventDefault();
        const peso = parseFloat(document.getElementById("peso").value);
        const alturaM = parseFloat(document.getElementById("altura").value) / 100;
        
        const imc = peso / (alturaM * alturaM);
        valImc.innerText = imc.toFixed(1);
        classImc.className = ""; 

        if (imc < 18.5) {
            classImc.innerText = "Abaixo do Peso";
            classImc.classList.add("text-orange");
            orientacaoImc.innerHTML = "<strong>Orientações HAWB:</strong> Busque agendar uma consulta inicial na <strong>UBS</strong> com Clínico Geral ou Nutricionista para investigação metabólica.";
        } else if (imc >= 18.5 && imc < 25) {
            classImc.innerText = "Peso Normal";
            classImc.classList.add("text-green");
            orientacaoImc.innerHTML = "<strong>Orientações HAWB:</strong> Excelente! Seus parâmetros encontram-se dentro da normalidade estável. Mantenha rotinas preventivas na sua <strong>UBS</strong>.";
        } else if (imc >= 25 && imc < 30) {
            classImc.innerText = "Sobrepeso";
            classImc.classList.add("text-orange");
            orientacaoImc.innerHTML = "<strong>Orientações HAWB:</strong> Fase de alerta preventivo. Procure a <strong>UBS</strong> para acompanhamento eletivo de novos hábitos alimentares.";
        } else {
            classImc.innerText = "Obesidade Detectada";
            classImc.classList.add("text-red");
            orientacaoImc.innerHTML = "<strong>Orientações HAWB:</strong> Níveis elevados com riscos vasculares. Marque suporte clínico na sua <strong>UBS</strong> para triagem cardiológica preventiva.";
        }

        resultBox.style.display = "block";
    });
});