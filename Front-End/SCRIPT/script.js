/// ============================================
// INICIALIZAÇÃO
// ============================================

document.addEventListener("DOMContentLoaded", function () {
  initProfileSelection();
  initFormMasks();
  initPasswordStrength();
  initPasswordToggles();
});

// ============================================
// PERFIL
// ============================================

let selectedProfile = "employee";

/*function initProfileSelection() {
  const profileCards = document.querySelectorAll(".profile-card");

  profileCards.forEach((card) => {
    card.addEventListener("click", function () {
      profileCards.forEach((c) => c.classList.remove("active"));
      this.classList.add("active");

      selectedProfile = this.dataset.profile;

      console.log("Cheguei aqui 1");
      // salva no input hidden (IMPORTANTE pro PHP)
      let perfilInput = document.getElementById("perfil");
      if (!perfilInput) {
        perfilInput = document.createElement("input");
        perfilInput.type = "hidden";
        perfilInput.name = "perfil";
        perfilInput.id = "perfil";
        document.getElementById("registerForm").appendChild(perfilInput);
        console.log("Cheguei aqui 2");
      }
      console.log(selectedProfile);

      perfilInput.value = selectedProfile;
    });
  });
}*/

function initProfileSelection() {
  const profileCards = document.querySelectorAll(".profile-card");
  const form = document.getElementById("registerForm");

  profileCards.forEach((card) => {
    card.addEventListener("click", function () {
      // Remove active de todos e adiciona no clicado
      profileCards.forEach((c) => c.classList.remove("active"));
      this.classList.add("active");

      const selectedProfile = this.dataset.profile;

      // Busca ou cria o input hidden
      let perfilInput = document.getElementById("perfil");
      if (!perfilInput) {
        perfilInput = document.createElement("input");
        perfilInput.type = "hidden";
        perfilInput.name = "perfil";
        perfilInput.id = "perfil";
        form.appendChild(perfilInput);
      }

      perfilInput.value = selectedProfile;
      console.log("Perfil selecionado:", selectedProfile); // Corrigido aqui
    });
  });
}
// ============================================
// MÁSCARAS
// ============================================

function initFormMasks() {
  const cpfInput = document.getElementById("cpf");
  const telefoneInput = document.getElementById("telefone");

  if (cpfInput) {
    cpfInput.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");

      value = value.replace(/(\d{3})(\d)/, "$1.$2");
      value = value.replace(/(\d{3})(\d)/, "$1.$2");
      value = value.replace(/(\d{3})(\d{1,2})$/, "$1-$2");

      e.target.value = value;
    });
  }

  if (telefoneInput) {
    telefoneInput.addEventListener("input", function (e) {
      let value = e.target.value.replace(/\D/g, "");

      value = value.replace(/(\d{2})(\d)/, "($1) $2");
      value = value.replace(/(\d{5})(\d)/, "$1-$2");

      e.target.value = value;
    });
  }
}

// ============================================
// FORÇA DA SENHA
// ============================================

function initPasswordStrength() {
  const senhaInput = document.getElementById("senha");
  const strengthFill = document.querySelector(".strength-fill");
  const strengthText = document.getElementById("strengthText");

  if (!senhaInput) return;

  senhaInput.addEventListener("input", function () {
    const password = this.value;
    const strength = calculatePasswordStrength(password);

    if (strengthFill) {
      strengthFill.style.width = strength.percentage + "%";
      strengthFill.style.background = strength.color;
    }

    if (strengthText) {
      strengthText.textContent = strength.text;
      strengthText.style.color = strength.color;
    }
  });
}

function calculatePasswordStrength(password) {
  let strength = 0;

  if (password.length >= 8) strength += 25;
  if (password.length >= 12) strength += 25;
  if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength += 25;
  if (/[0-9]/.test(password)) strength += 15;
  if (/[^a-zA-Z0-9]/.test(password)) strength += 10;

  let result = {
    percentage: strength,
    text: "Fraca",
    color: "#e74c3c",
  };

  if (strength >= 70) {
    result.text = "Forte";
    result.color = "#27ae60";
  } else if (strength >= 40) {
    result.text = "Média";
    result.color = "#f39c12";
  }

  return result;
}

// ============================================
// TOGGLE SENHA
// ============================================

function initPasswordToggles() {
  const buttons = document.querySelectorAll(".password-toggle");

  buttons.forEach((btn) => {
    btn.addEventListener("click", function () {
      const target = document.getElementById(this.dataset.target);

      if (!target) return;

      const type = target.type === "password" ? "text" : "password";
      target.type = type;

      this.innerHTML =
        type === "text"
          ? '<i class="fas fa-eye-slash"></i>'
          : '<i class="fas fa-eye"></i>';
    });
  });
}
