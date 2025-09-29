<?php
/**
 * Script de Envio de E-mail Nativo
 * Não requer bibliotecas externas.
 */

// *******************************************************************
// 1. CONFIGURAÇÃO DE E-MAIL
// *******************************************************************

// Endereço para onde o formulário será enviado
// **MUITO IMPORTANTE: USE UM E-MAIL DO SEU PRÓPRIO DOMÍNIO NA HOSTINGER**
// (Ex: contato@gidzulinskifotografia.com.br)
$receiving_email_address = 'confidecor@confidecor.com.br'; // Usei o e-mail do seu index.html
$default_subject = "Nova Mensagem do Site - Gidzulinski";

// Resposta AJAX padrão (O validate.js espera "OK" para sucesso)
function ajax_response($message, $status = 200) {
    http_response_code($status);
    echo $message;
    exit;
}

// *******************************************************************
// 2. VALIDAÇÃO E FILTRAGEM
// *******************************************************************
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    ajax_response("Método inválido.", 405);
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$subject = trim($_POST['subject'] ?? '');
$message = trim($_POST['message'] ?? '');

if (empty($name) || empty($email) || empty($message) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ajax_response("Erro: Preencha todos os campos obrigatórios e use um e-mail válido.", 400);
}

// Verifica se o e-mail de destino foi configurado
if (empty($receiving_email_address) || !filter_var($receiving_email_address, FILTER_VALIDATE_EMAIL)) {
    ajax_response("Erro de configuração: O endereço de e-mail do destinatário não é válido.", 500);
}

// *******************************************************************
// 3. PREPARAÇÃO DO E-MAIL
// *******************************************************************

$email_subject = (!empty($subject)) ? "{$subject} - Contato Gidzulinski" : $default_subject;

$email_content = "Nome/Empresa: {$name}\n";
$email_content .= "Email: {$email}\n";
$email_content .= "Assunto: {$subject}\n";
$email_content .= "Mensagem:\n{$message}\n";

// *******************************************************************
// 4. CABEÇALHOS E ENVIO
// *******************************************************************

// Cria cabeçalhos, obrigatórios para o Hostinger
$headers = "From: " . $email . "\r\n";
$headers .= "Reply-To: " . $email . "\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();

// Tenta enviar o e-mail
if (mail($receiving_email_address, $email_subject, $email_content, $headers)) {
    // Retorna "OK" para o validate.js, que exibe a mensagem de sucesso
    echo "OK"; 
} else {
    // Caso o mail() falhe por razões internas do servidor
    ajax_response("Erro ao enviar a mensagem. Por favor, tente novamente mais tarde.", 500);
}

?>