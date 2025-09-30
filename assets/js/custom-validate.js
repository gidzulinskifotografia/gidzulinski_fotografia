document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('contact-form');
    
    if (form) {
        form.addEventListener('submit', function (event) {
            // Previne o envio padrão do formulário
            event.preventDefault();

            // Pega a URL de destino (FormSubmit)
            const actionUrl = this.getAttribute('action');
            const formData = new FormData(this);

            // Validação Básica de E-mail (opcional, mas bom ter)
            const emailField = this.querySelector('#email');
            if (emailField && !isValidEmail(emailField.value)) {
                showValidationError('Por favor, insira um endereço de e-mail válido.');
                return;
            }

            // Exibir SweetAlert de "Enviando..."
            Swal.fire({
                title: 'Enviando Mensagem...',
                text: 'Por favor, aguarde.',
                icon: 'info',
                allowOutsideClick: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Envio via AJAX para o FormSubmit
            fetch(actionUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    // O FormSubmit usa o header Accept para confirmar que é AJAX.
                    'Accept': 'application/json' 
                }
            })
            .then(response => {
                // 1. O FormSubmit tem sucesso com status 200
                if (response.ok) {
                    // Verifica se há conteúdo para evitar o erro de JSON se a resposta for vazia
                    const contentType = response.headers.get("content-type");
                    if (contentType && contentType.includes("application/json")) {
                        return response.json(); // Se for JSON, leia o JSON
                    }
                    return {}; // Se for 200/OK, mas sem JSON (vazio), trate como sucesso
                } else {
                    // Erro no FormSubmit. Tenta ler a mensagem de erro.
                    return response.json().then(data => {
                        throw new Error(data.message || 'Falha no envio do formulário.');
                    });
                }
            })
            .then(data => {
                // Sucesso no envio
                Swal.fire({
                    title: 'Mensagem Enviada!',
                    text: 'Obrigado por entrar em contato. Responderemos em breve!',
                    icon: 'success',
                    confirmButtonText: 'Fechar'
                });
                form.reset(); // Limpa o formulário
            })
            .catch(error => {
                // Exibe o SweetAlert de Erro
                Swal.fire({
                    title: 'Erro!',
                    text: error.message || 'Ocorreu um erro no envio. Por favor, tente novamente.',
                    icon: 'error',
                    confirmButtonText: 'Fechar'
                });
            });
        });
    }

    // Função auxiliar para validação de e-mail
    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }
    
    // Função para mostrar erro de validação (SweetAlert)
    function showValidationError(message) {
        Swal.fire({
            title: 'Erro de Validação',
            text: message,
            icon: 'warning',
            confirmButtonText: 'Corrigir'
        });
    }
});