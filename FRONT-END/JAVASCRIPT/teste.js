console.log("trolha games");

// Elementos da DOM
const botaoscan = document.getElementById("btn-scan");
const gerenciarBotao = document.getElementById("btn-cancel");
const logo = document.getElementById("qrIdle");
let icone = document.querySelector(".fas");

// Eventos
if (botaoscan) botaoscan.addEventListener("click", capturarEProcessar);
if (gerenciarBotao) gerenciarBotao.addEventListener("click", ligaDesliga);

// URL atualizada para o novo modelo do Teachable Machine
const URL = "https://teachablemachine.withgoogle.com/models/Tj6G-iKq6/";

let model, webcam, maxPredictions;
let isModelReady = false;
let statusCam = "desligado";
let animationFrameId; // Guarda o ID do loop de animação para permitir pausá-lo

function alternarCor() {
  console.log(`O valor de statusCam é ${statusCam}`);
  if (statusCam === "ligado") {
    gerenciarBotao.innerHTML = "<i class='fas fa-clipboard'></i> Inpecionar";
  } else if (statusCam === "desligado") {
    gerenciarBotao.innerHTML = "<i class='fa-solid fa-power-off'></i> Parar inspecionamento";
  }
}

function ligar() {
  console.log("ligando");
  init();
  statusCam = "ligado";
  return statusCam;
}

function desligar() {
  console.log("desligando");

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
  console.log("Desligado");
}

function ligaDesliga() {
  if (statusCam === "carregando") {
    return; // Evita cliques duplos enquanto carrega
  } else {
    alternarCor();
    if (statusCam === "desligado") {
      statusCam = "carregando";
      ligar();
    } else {
      statusCam = "carregando";
      desligar();
    }
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

    // Configura a Webcam (mantendo a resolução e espelhamento)
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
    
  } catch (e) {
    console.error("Erro ao iniciar:", e);
    alert("Erro ao acessar a webcam ou carregar o modelo.");
    statusCam = "desligado";
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
  }

  const elResultado = document.getElementById("resultado-vencedor");
  const elProbabilidade = document.getElementById("probabilidade");

  if (maiorValor >= limiteconfiavel) {
    if (elResultado) elResultado.innerText = melhorClasse;
    if (elProbabilidade) elProbabilidade.innerText = (maiorValor * 100).toFixed(1) + "% de certeza";
  } else {
    console.log("Não é possível achar algo, tente outra coisa");
    if (elResultado) elResultado.innerText = "Não é possivel achar algo, tente outra coisa";
    if (elProbabilidade) elProbabilidade.innerText = "";
  }
}