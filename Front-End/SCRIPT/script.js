// ============================================
// INICIALIZAÇÃO
// ============================================

let selectedProfile = 'employee'; // Padrão

document.addEventListener('DOMContentLoaded', function() {
    initProfileSelection();
    initFormMasks();
    initPasswordStrength();
    initPasswordToggles();
    initFormValidation();
});

// ============================================
// SELEÇÃO DE PERFIL
// ============================================

function initProfileSelection() {
    const profileCards = document.querySelectorAll('.profile-card');
    
    profileCards.forEach(card => {
        card.addEventListener('click', function() {
            // Remove active de todos
            profileCards.forEach(c => c.classList.remove('active'));
            
            // Adiciona active no clicado
            this.classList.add('active');
            
            // Salva perfil selecionado
            selectedProfile = this.dataset.profile;
            
            console.log('Perfil selecionado:', selectedProfile);
        });
    });
}

// ============================================
// MÁSCARAS DE ENTRADA
// ============================================

function initFormMasks() {
    // Máscara de CPF
    const cpfInput = document.getElementById('cpf');
    cpfInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value.length <= 11) {
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        }
        
        e.target.value = value;
    });

    // Máscara de Telefone
    const telefoneInput = document.getElementById('telefone');
    telefoneInput.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, '');
        
        if (value.length <= 11) {
            value = value.replace(/(\d{2})(\d)/, '($1) $2');
            value = value.replace(/(\d{5})(\d)/, '$1-$2');
        }
        
        e.target.value = value;
    });
}

// ============================================
// FORÇA DA SENHA
// ============================================

function initPasswordStrength() {
    const senhaInput = document.getElementById('senha');
    const strengthFill = document.querySelector('.strength-fill');
    const strengthText = document.getElementById('strengthText');
    
    senhaInput.addEventListener('input', function() {
        const password = this.value;
        const strength = calculatePasswordStrength(password);
        
        // Atualizar barra
        strengthFill.style.width = strength.percentage + '%';
        strengthFill.style.background = strength.color;
        
        // Atualizar texto
        strengthText.textContent = strength.text;
        strengthText.style.color = strength.color;
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
        text: 'Fraca',
        color: '#e74c3c'
    };
    
    if (strength >= 70) {
        result.text = 'Forte';
        result.color = '#27ae60';
    } else if (strength >= 40) {
        result.text = 'Média';
        result.color = '#f39c12';
    }
    
    return result;
}

// ============================================
// TOGGLE DE SENHA
// ============================================

function initPasswordToggles() {
    const toggleButtons = document.querySelectorAll('.password-toggle');
    
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const targetId = this.dataset.target;
            const input = document.getElementById(targetId);
            const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
            input.setAttribute('type', type);
            
            // Atualizar ícone
            if (type === 'text') {
                this.innerHTML = '<i class="fas fa-eye-slash"></i>';
            } else {
                this.innerHTML = '<i class="fas fa-eye"></i>';
            }
        });
    });
}

// ============================================
// VALIDAÇÃO DO FORMULÁRIO
// ============================================

function initFormValidation() {
    const form = document.getElementById('registerForm');
    
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Limpar erros anteriores
        clearErrors();
        
        let isValid = true;
        
        // Validar Nome
        const nome = document.getElementById('nome').value.trim();
        if (nome.length < 3) {
            showError('nome', 'Nome deve ter pelo menos 3 caracteres');
            isValid = false;
        }
        
        // Validar CPF
        const cpf = document.getElementById('cpf').value.replace(/\D/g, '');
        if (cpf.length !== 11) {
            showError('cpf', 'CPF inválido');
            isValid = false;
        }
        
        // Validar Telefone
        const telefone = document.getElementById('telefone').value.replace(/\D/g, '');
        if (telefone.length < 10) {
            showError('telefone', 'Telefone inválido');
            isValid = false;
        }
        
        // Validar E-mail
        const email = document.getElementById('email').value;
        if (!isValidEmail(email)) {
            showError('email', 'E-mail inválido');
            isValid = false;
        }
        
        // Validar Senha
        const senha = document.getElementById('senha').value;
        if (senha.length < 8) {
            showError('senha', 'Senha deve ter no mínimo 8 caracteres');
            isValid = false;
        }
        
        // Validar Confirmação de Senha
        const confirmarSenha = document.getElementById('confirmarSenha').value;
        if (senha !== confirmarSenha) {
            showError('confirmarSenha', 'As senhas não coincidem');
            isValid = false;
        }
        
        if (isValid) {
            submitForm();
        } else {
            document.querySelector('.register-card').classList.add('shake');
            setTimeout(() => {
                document.querySelector('.register-card').classList.remove('shake');
            }, 500);
        }
    });
}

function isValidEmail(email) {
    const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
}

function showError(inputId, message) {
    const input = document.getElementById(inputId);
    const wrapper = input.closest('.input-wrapper');
    
    wrapper.classList.add('error');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'error-message';
    errorDiv.textContent = message;
    
    wrapper.parentElement.appendChild(errorDiv);
}

function clearErrors() {
    document.querySelectorAll('.error-message').forEach(el => el.remove());
    document.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
}

// ============================================
// ENVIO DO FORMULÁRIO
// ============================================

function submitForm() {
    const btn = document.querySelector('.btn-register');
    
    // Estado de loading
    btn.disabled = true;
    btn.classList.add('loading');
    btn.innerHTML = '<div class="spinner"></div> Criando conta...';
    
    // Coletar dados
    const formData = {
        perfil: selectedProfile,
        nome: document.getElementById('nome').value,
        cpf: document.getElementById('cpf').value,
        telefone: document.getElementById('telefone').value,
        email: document.getElementById('email').value,
        senha: document.getElementById('senha').value
    };
    
    console.log('Dados do cadastro:', formData);
    
    // Simular envio
    setTimeout(() => {
        btn.classList.remove('loading');
        btn.classList.add('success');
        btn.innerHTML = '<i class="fas fa-check"></i> Conta criada!';
        
        setTimeout(() => {
            alert('Conta criada com sucesso!\nPerfil: ' + (selectedProfile === 'admin' ? 'Administrador' : 'Funcionário'));
            window.location.href = 'login.html';
        }, 1500);
    }, 2000);
}