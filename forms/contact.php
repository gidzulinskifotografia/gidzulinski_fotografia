<?php
/**
 * Script Simples para Envio de E-mail
 * Baseado na função mail() do PHP, compatível com a maioria das hospedagens.
 */

// 1. CONFIGURAÇÃO DE E-MAIL
// *******************************************************************

// Substitua 'SEU_EMAIL@dominio.com' pelo seu endereço de e-mail real.
$receiving_email_address = 'gidzulinskifotografia@gmail.com';

// Assunto padrão para os e-mails recebidos (opcional)
$default_subject = "Nova Mensagem do Site - Gidzulinski";


// 2. FUNÇÃO DE RESPOSTA AJAX
// *******************************************************************

function ajax_response($message, $status = 500) {
    // Configura o cabeçalho para indicar que é uma resposta de erro
    if ($status != 200) {
        http_response_code($status);
        echo $message;
    } else {
        // Para sucesso, retorna apenas 'OK' conforme o 'validate.js' espera.
        echo 'OK';
    }
    exit;
}


// 3. COLETA E VALIDAÇÃO DE DADOS
// *******************************************************************

// Verifica se a requisição é POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    ajax_response("Método de requisição inválido.", 405);
}

// Verifica se os campos obrigatórios foram submetidos
if (!isset($_POST['name']) || !isset($_POST['email']) || !isset($_POST['subject']) || !isset($_POST['message'])) {
    ajax_response("Por favor, preencha todos os campos obrigatórios.", 400);
}

// Limpa e valida os dados
$name    = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
$email   = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
$subject = filter_var(trim($_POST['subject']), FILTER_SANITIZE_STRING);
$message = filter_var(trim($_POST['message']), FILTER_SANITIZE_STRING);

// Validação de e-mail
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    ajax_response("Endereço de e-mail inválido.", 400);
}

// Se o campo 'message' estiver vazio após a sanitização
if (empty($message)) {
    ajax_response("A mensagem não pode estar vazia.", 400);
}


// 4. PREPARAÇÃO DO E-MAIL
// *******************************************************************

$email_subject = (!empty($subject)) ? "{$subject} - Contato Gidzulinski" : $default_subject;

// Monta o corpo do e-mail em formato de texto
$email_content = "Nome: {$name}\n";
$email_content .= "Email: {$email}\n\n";
$email_content .= "Mensagem:\n{$message}\n";

// Cabeçalhos para evitar que o e-mail vá para o SPAM
$headers  = "From: {$name} <{$email}>\r\n";
$headers .= "Reply-To: {$email}\r\n";
$headers .= "X-Mailer: PHP/" . phpversion();


// 5. ENVIO DO E-MAIL
// *******************************************************************

if (mail($receiving_email_address, $email_subject, $email_content, $headers)) {
    // Sucesso no envio
    ajax_response('OK', 200);
} else {
    // Erro no envio
    ajax_response('Erro ao enviar a mensagem. Por favor, tente novamente mais tarde ou use o Whatsapp.', 500);
}

?>