console.log("trolha games");
console.log("PERRY O ORNITORRINCO");
// Elementos da DOM
const botaoscan = document.getElementById("btn-scan");
const gerenciarBotao = document.getElementById("btn-cancel");
let botaoinspecao = document.getElementById("btn-insp");
const loading = document.getElementById("loading");
let link = "";
botaoinspecao.disabled = true
const logo = document.getElementById("qrIdle");
let icone = document.querySelector(".fas");
const spin = document.getElementById("spinner");
spin.style.display = "none";
loading.style.display = "none";
// Eventos
if (botaoscan) botaoscan.addEventListener("click", capturarEProcessar);
if (gerenciarBotao) gerenciarBotao.addEventListener("click", ligaDesliga);

botaoinspecao.addEventListener("click", mudarpagina);
// URL atualizada para o novo modelo do Teachable Machine
const URL = "https://teachablemachine.withgoogle.com/models/Tj6G-iKq6/";

let model, webcam, maxPredictions;
let isModelReady = false;
let statusCam = "desligado";
let animationFrameId; // Guarda o ID do loop de animação para permitir pausá-lo
// Função para controlar a visibilidade do Spinner
function setCarregando(carregando) {
  if (carregando) {
    spin.style.display = "block";// Exibe o spinner
    logo.style.display = "none";
    loading.style.display = "block";

  } else {
    loading.style.display = "none";
    spin.style.display = "none";  // Esconde o spinner
  }
}

function alternarCor() {
  console.log(`O valor de statusCam é ${statusCam}`);
  if (statusCam === "ligado") {
    gerenciarBotao.innerHTML = "<i class='fa-solid fa-power-off'></i> Desligar câmera";
    gerenciarBotao.classList.add('btn-alert-desl');
  } else if (statusCam === "desligado") {
    gerenciarBotao.innerHTML = "<i class='fa-solid fa-power-off'></i> Ligar câmera";
    gerenciarBotao.classList.remove('btn-alert-desl');
  }
}

async function ligar() {
  console.log("ligando");
  
  // 1. Exibe o spinner e altera o status
  setCarregando(true);
  statusCam = "carregando";

  // 2. Tenta carregar o modelo e a webcam
  const carregouSucesso = await init();

  // 3. Oculta o spinner após a tentativa
  setCarregando(false);

  if (carregouSucesso) {
    statusCam = "ligado";
    alternarCor();
  } else {
    statusCam = "desligado";
    alternarCor();
  }
}

function desligar() {
  console.log("desligando");

  // Oculta o spinner por garantia
  setCarregando(false);

  // 1. Para o loop de animação imediatamente
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
  }

  // 2. Interrompe a câmera e limpa o elemento HTML
  if (webcam) {
    webcam.stop();
    const container = document.getElementById("webcam-container");
    if (container && webcam.canvas && container.contains(webcam.canvas)) {
      container.removeChild(webcam.canvas);
    }
    if (logo) logo.style.display = "";
  }
  
  statusCam = "desligado";
  isModelReady = false;
  alternarCor();
  console.log("Desligado");
}

function ligaDesliga() {
  if (statusCam === "carregando") {
    return; // Evita cliques duplos enquanto carrega
  }
  
  if (statusCam === "desligado") {
    ligar();
  } else if (statusCam === "ligado") {
    desligar();
  }
}

async function init() {
  const modelURL = URL + "model.json";
  const metadataURL = URL + "metadata.json";

  try {
    console.log("ligando...");
    
    // Carrega o modelo e metadados
    model = await tmImage.load(modelURL, metadataURL);
    maxPredictions = model.getTotalClasses();

    // Configura a Webcam
    const flip = true;
    webcam = new tmImage.Webcam(680, 440, flip);

    if (logo) logo.style.display = "none";

    await webcam.setup();
    await webcam.play();

    // Adiciona o canvas da webcam na interface
    document.getElementById("webcam-container").appendChild(webcam.canvas);

    isModelReady = true;

    // Inicia o loop contínuo da webcam
    animationFrameId = window.requestAnimationFrame(loop);

    return true; // Sucesso
  } catch (e) {
    console.error("Erro ao iniciar:", e);
    alert("Erro ao acessar a webcam ou carregar o modelo.");
    return false; // Falha
  }
}

// Loop contínuo que atualiza o frame da câmera
async function loop() {
  if (webcam) {
    webcam.update();
  }
  animationFrameId = window.requestAnimationFrame(loop);
}

// Função de predição com filtro de limite de confiança
async function capturarEProcessar() {
  link = "";
  if (!isModelReady || !model || !webcam) return;

  const prediction = await model.predict(webcam.canvas);

  let melhorClasse = "";
  let maiorValor = 0;
  let limiteconfiavel = 0.90; // 90% de confiança mínima

  
  
  for (let i = 0; i < maxPredictions; i++) {
    if (prediction[i].probability > maiorValor) {
      maiorValor = prediction[i].probability;
      melhorClasse = prediction[i].className;

    }

    if (link === "") {
        botaoinspecao.disabled = true;
    }else {
        botaoinspecao.disabled = false;
    }
  }


  const elResultado = document.getElementById("resultado-vencedor");
  const elProbabilidade = document.getElementById("probabilidade");

  if (maiorValor >= limiteconfiavel) {
    if (elResultado) elResultado.innerText = melhorClasse;
    if (elProbabilidade) elProbabilidade.innerText = (maiorValor * 100).toFixed(1) + "% de certeza";

    if(melhorClasse == "Para-choque"){
      link = `http://localhost/Progama%C3%A7%C3%A3o%20Back-End/TCC2/BACK-END/TELAS-ADMIN/inspecao.php?opicao=para_choque`;
    }else{
      link = `http://localhost/Progama%C3%A7%C3%A3o%20Back-End/TCC2/BACK-END/TELAS-ADMIN/inspecao.php?opicao=${melhorClasse.toLocaleLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")}`;
    }

    if(link === ""){
        botaoinspecao.disabled = true;
    if(elResultado === "Não é possivel achar algo, tente outra coisa"){
        botaoinspecao.disabled = true; 
    }
    }else{
      botaoinspecao.disabled = false;
    }
 




    console.log(link);
    //window.location.href = link;
  } else {
    console.log("Não é possível achar algo, tente outra coisa");
    if (elResultado) elResultado.innerText = "Não é possivel achar algo, tente outra coisa";
    if (elProbabilidade) elProbabilidade.innerText = "";
  }
}

function mudarpagina(){
  window.location.href = link;
}