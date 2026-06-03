// Link do seu modelo (mantenha a barra ao final)
const gerenciarBotao = document.getElementById("btn-cancel");
gerenciarBotao.addEventListener("click", ligaDesliga);

const URL = "https://teachablemachine.withgoogle.com/models/Ulneln_-F/";

let model, webcam, maxPredictions;
let isModelReady = false;
const logo = document.getElementById("qrIdle");
let statusCam = "desligado";
let animationFrameId; // Guardará o ID do loop para podermos pará-lo
let icone = document.querySelector(".fa-solid");

function alternarCor() {
  console.log(`O valor de statusCam é ${statusCam}`);
  if (statusCam === "ligado") {
    gerenciarBotao.innerHTML =
      " <i class='fa-solid fa-camera'></i> Ligar Câmera ";
    icone.classList.add();
  } else if (statusCam === "desligado") {
    gerenciarBotao.innerHTML = 
    "<i class='fa-solid fa-power-off'></i> Desligar Câmera";
  }
}

function ligar() {
  //   if (statusCam === "desligado") {
  // statusCam = "ligado";
  console.log("ligando");
  init();
  statusCam = "ligado";
  console.log(statusCam);
  return statusCam;
  //   }
}

function desligar() {
  //   if (statusCam === "ligado") {
  // statusCam = "desligado";
  console.log("desligando");
  console.log(statusCam);

  // 1. Para o loop de animação imediatamente
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
  }

  if (webcam) {
    webcam.stop(); // 2. Desliga a captura da câmera no hardware
    // 3. Limpa o elemento HTML onde o canvas foi inserido
    //   document.getElementById("scannerBox").innerHTML = "";
    document.getElementById("scannerBox").removeChild(webcam.canvas);
    logo.style.display = "";
  }
  statusCam = "desligado";
  console.log("Desligado");
  //   }
}

function ligaDesliga() {
  if (statusCam === "carregando") {
    return; //não funciona enquanto está nesse estado intermediário
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
    // Carrega o modelo
    model = await tmImage.load(modelURL, metadataURL);
    maxPredictions = model.getTotalClasses();

    // Configura a Webcam
    const flip = true;
    webcam = new tmImage.Webcam(680, 440, flip);

    logo.style.display = "none";

    await webcam.setup();
    await webcam.play();

    // Adiciona o canvas da webcam na tela
    document.getElementById("scannerBox").appendChild(webcam.canvas);

    // Inicia o loop e salva o ID dele
    animationFrameId = window.requestAnimationFrame(loop);
  } catch (e) {
    console.error("Erro ao iniciar:", e);
    alert("Erro ao acessar a webcam ou carregar o modelo.");
  }
}

// 2. Loop visual (atualizado para registrar o ID da animação)
async function loop() {
  webcam.update();
  animationFrameId = window.requestAnimationFrame(loop);
}

// 3. Função disparada pelo botão (A "Mágica")
async function capturarEProcessar() {
  if (!isModelReady) return;

  const prediction = await model.predict(webcam.canvas);

  let melhorClasse = "";
  let maiorValor = 0;
  let resultados = {};
  for (let i = 0; i < maxPredictions; i++) {
    resultados[prediction[i].className] = prediction[i].probability;
    if (prediction[i].probability > maiorValor) {
      maiorValor = prediction[i].probability;
      melhorClasse = prediction[i].className;
    }
  }

  // Atualiza a interface com o resultado
  document.getElementById("resultado-vencedor").innerText = melhorClasse;
  document.getElementById("probabilidade").innerText =
    (maiorValor * 100).toFixed(1) + "% de certeza";
}
