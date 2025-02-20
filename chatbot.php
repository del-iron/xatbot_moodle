<?php
// Define o tipo de resposta como texto puro (plain text) para evitar formatação HTML.
header("Content-Type: text/plain");

// Inicia a sessão para armazenar informações do usuário durante a conversa.
session_start();

// Obtém o nome do usuário a partir do POST ou usa "Usuário" como padrão caso não seja enviado.
$userName = $_POST["user"] ?? "Usuário";

// Obtém a mensagem enviada pelo usuário, converte para minúsculas e remove espaços extras.
$message = strtolower(trim($_POST["message"] ?? ""));

// Se a variável de contexto ainda não foi definida na sessão, inicializa como nula.
if (!isset($_SESSION["context"])) {
    $_SESSION["context"] = null;
}

// Define respostas diretas para perguntas comuns.
$respostas = [
    "como acessar o moodle" => "Vá até o site da sua instituição e faça login com seu usuário e senha.",
    "como enviar um trabalho" => "Entre na disciplina, clique na atividade e selecione 'Enviar arquivo'.",
    "o que é moodle" => "Moodle é uma plataforma de ensino à distância usada para cursos online."
];

// Define perguntas que podem levar a um fluxo guiado de resposta.
$perguntas = [
    "senha" => "Você esqueceu sua senha ou deseja alterá-la?",
    "login" => "Você está enfrentando problemas para acessar sua conta ou deseja criar uma nova?",
    "notas" => "Você quer ver suas notas em uma disciplina específica ou todas as matérias?"
];

// Define respostas específicas para os fluxos guiados.
$respostas_guiadas = [
    "esqueci minha senha" => "Clique em 'Esqueci minha senha' na página de login do Moodle e siga as instruções.",
    "alterar senha" => "Acesse seu perfil no Moodle, vá até 'Configurações' e escolha a opção para mudar sua senha.",
    "problema no login" => "Verifique se está digitando o usuário e senha corretamente. Se o problema persistir, contate o suporte.",
    "criar conta" => "Se sua instituição permite, você pode criar uma conta na página de cadastro do Moodle."
];

// Verifica se há um contexto armazenado na sessão (exemplo: se o usuário iniciou um fluxo guiado).
if ($_SESSION["context"]) {
    // Se o usuário deu continuidade ao fluxo, tenta encontrar a resposta específica.
    $resposta = $respostas_guiadas[$message] ?? "Desculpe, não entendi. Pode reformular?";
    
    // Reseta o contexto para evitar que a próxima pergunta ainda esteja nesse fluxo.
    $_SESSION["context"] = null;
} else {
    // Resposta padrão caso a pergunta não seja encontrada nos bancos de respostas.
    $resposta = "Desculpe, $userName, não entendi. Tente perguntar de outra forma.";

    // Percorre a lista de respostas diretas para verificar se alguma corresponde à pergunta do usuário.
    foreach ($respostas as $chave => $valor) {
        if (strpos($message, $chave) !== false) {
            $resposta = "$userName, " . $valor;
            break; // Para a busca ao encontrar uma correspondência.
        }
    }

    // Percorre a lista de perguntas para ativar um fluxo guiado caso corresponda à mensagem do usuário.
    foreach ($perguntas as $chave => $pergunta) {
        if (strpos($message, $chave) !== false) {
            $resposta = $pergunta;
            $_SESSION["context"] = $chave; // Armazena o contexto da conversa para a próxima interação.
            break;
        }
    }
}

// Exibe a resposta para o usuário.
echo $resposta;
?>
