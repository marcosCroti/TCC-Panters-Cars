console.log("trolha games");
console.log("PERRY O ORNITORRINCO");

// Elementos da DOM
const botaoscan = document.getElementById("btn-scan");
const gerenciarBotao = document.getElementById("btn-cancel");
let botaoinspecao = document.getElementById("btn-insp");
const loading = document.getElementById("loading");
let link = "";
if (botaoinspecao) botaoinspecao.disabled = true;

const logo = document.getElementById("qrIdle");
let icone = document.querySelector(".fas");
const spin = document.getElementById("spinner");

if (spin) spin.style.display = "none";
if (loading) loading.style.display = "none";

// Eventos
if (botaoscan) botaoscan.addEventListener("click", capturarEProcessar);
if (gerenciarBotao) gerenciarBotao.addEventListener("click", ligaDesliga);
if (botaoinspecao) botaoinspecao.addEventListener("click", mudarpagina);

// URL do modelo
const URL = "https://teachablemachine.withgoogle.com/models/Tj6G-iKq6/";

let model, webcam, labelContainer, maxPredictions;
let isModelReady = false;
let statusCam = "desligado";
let animationFrameId;

function setCarregando(carregando) {
  if (carregando) {
    if (spin) spin.style.display = "block";
    if (logo) logo.style.display = "none";
    if (loading) loading.style.display = "block";
  } else {
    if (loading) loading.style.display = "none";
    if (spin) spin.style.display = "none";
  }
}

function alternarCor() {
  console.log(`O valor de statusCam é ${statusCam}`);
  if (!gerenciarBotao) return;
  
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
  setCarregando(true);
  statusCam = "carregando";

  const carregouSucesso = await init();

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
  setCarregando(false);

  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
  }

  if (webcam) {
    webcam.stop();
    const container = document.getElementById("webcam-container");
    if (container && webcam.canvas && container.contains(webcam.canvas)) {
      container.removeChild(webcam.canvas);
    }
    if (logo) logo.style.display = "";
  }
  
  // Limpa o container de rótulos caso exista
  if (labelContainer) {
    labelContainer.innerHTML = "";
  }

  statusCam = "desligado";
  isModelReady = false;
  alternarCor();
  console.log("Desligado");
}

function ligaDesliga() {
  if (statusCam === "carregando") return;
  
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
    
    // Carrega o modelo e os metadados
    model = await tmImage.load(modelURL, metadataURL);
    maxPredictions = model.getTotalClasses();

    // Configuração da Webcam conforme standard TM (200x200 ou personalizável)
    const flip = true;
    webcam = new tmImage.Webcam(680, 440, flip);

    if (logo) logo.style.display = "none";

    await webcam.setup();
    await webcam.play();

    // Renderiza a webcam na DOM
    const webcamContainer = document.getElementById("webcam-container");
    if (webcamContainer) {
      webcamContainer.appendChild(webcam.canvas);
    }

    // Prepara container de lista de classes (padrão Teachable Machine)
    labelContainer = document.getElementById("label-container");
    if (labelContainer) {
      labelContainer.innerHTML = "";
      for (let i = 0; i < maxPredictions; i++) {
        labelContainer.appendChild(document.createElement("div"));
      }
    }

    isModelReady = true;

    // Inicia o loop continuo de renderização
    animationFrameId = window.requestAnimationFrame(loop);

    return true;
  } catch (e) {
    console.error("Erro ao iniciar:", e);
    alert("Erro ao acessar a webcam ou carregar o modelo.");
    return false;
  }
}

async function loop() {
  if (webcam && isModelReady) {
    webcam.update();
    // Atualização contínua das porcentagens caso exista o labelContainer
    if (labelContainer && labelContainer.childNodes.length > 0) {
      await predictLoop();
    }
  }
  animationFrameId = window.requestAnimationFrame(loop);
}

// Atualização visual em tempo real no label-container
async function predictLoop() {
  if (!model || !webcam) return;
  const prediction = await model.predict(webcam.canvas);
  for (let i = 0; i < maxPredictions; i++) {
    const classPrediction =
      prediction[i].className + ": " + prediction[i].probability.toFixed(2);
    if (labelContainer.childNodes[i]) {
      labelContainer.childNodes[i].innerHTML = classPrediction;
    }
  }
}

// Ação disparada no botão de escanear (Sua regra de negócio)
async function capturarEProcessar() {
  link = "";
  if (!isModelReady || !model || !webcam) return;

  const prediction = await model.predict(webcam.canvas);

  let melhorClasse = "";
  let maiorValor = 0;
  let limiteconfiavel = 0.95; // 90% de confiança mínima

  for (let i = 0; i < maxPredictions; i++) {
    if (prediction[i].probability > maiorValor) {
      maiorValor = prediction[i].probability;
      melhorClasse = prediction[i].className;
    }
  }

  const elResultado = document.getElementById("resultado-vencedor");

  // SUCESSO: Confiança igual ou superior ao limite
  if (maiorValor >= limiteconfiavel) {
    if (elResultado) {
      elResultado.innerText = melhorClasse;
      elResultado.style.display = "block";
    }

    if (melhorClasse === "Para-choque") {
      link = `http://localhost/Progama%C3%A7%C3%A3o%20Back-End/TCC2/BACK-END/TELAS-ADMIN/inspecao.php?opicao=para_choque`;
    } else {
      link = `http://localhost/Progama%C3%A7%C3%A3o%20Back-End/TCC2/BACK-END/TELAS-ADMIN/inspecao.php?opicao=${melhorClasse.toLocaleLowerCase().normalize("NFD").replace(/[\u0300-\u036f]/g, "")}`;
    }

    if (botaoinspecao) {
      botaoinspecao.disabled = link === "";
    }

    console.log(link);

  // FALHA: Baixa confiança
  } else {
    console.log("Não foi possível encontrar uma peça. Tente novamente.");
    
    if (elResultado) {
      elResultado.innerText = "Não foi possível encontrar uma peça. Tente novamente.";
      elResultado.style.display = "block";
    }

    link = "";
    if (botaoinspecao) {
      botaoinspecao.disabled = true;
    }
  }
}

function mudarpagina() {
  if (link !== "") {
    window.location.href = link;
  }
}