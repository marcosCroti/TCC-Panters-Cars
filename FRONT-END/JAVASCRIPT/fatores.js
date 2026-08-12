let time = document.getElementById("cntTime");

let tempo = 0;

function contar(){
const cronometro = setInterval(() => {
    if (tempo < 60) {
        tempo++;
        time.innerText = tempo + " seg";
    } else {
        clearInterval(cronometro); // Para o contador em 60
    }
}, 1000);

}

function zerar(){
    tempo = 0;
    exit(contar());
}