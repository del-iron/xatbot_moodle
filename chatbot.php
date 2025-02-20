<?php
header("Content-Type: text/plain");

session_start();

$userName = $_POST["user"] ?? "Usuário";
$message = strtolower(trim($_POST["message"] ?? ""));

if (!isset($_SESSION["context"])) {
    $_SESSION["context"] = null;
}

$respostas = [
    "como acessar o moodle" => "Vá até o site da sua instituição e faça login com seu usuário e senha.",
    "como enviar um trabalho" => "Entre na disciplina, clique na atividade e selecione 'Enviar arquivo'.",
    "o que é moodle" => "Moodle é uma plataforma de ensino à distância usada para cursos online."
];

// Sistema de perguntas guiadas
$perguntas = [
    "senha" => "Você esqueceu sua senha ou deseja alterá-la?",
    "login" => "Você está enfrentando problemas para acessar sua conta ou deseja criar uma nova?",
    "notas" => "Você quer ver suas notas em uma disciplina específica ou todas as matérias?"
];

$respostas_guiadas = [
    "esqueci minha senha" => "Clique em 'Esqueci minha senha' na página de login do Moodle e siga as instruções.",
    "alterar senha" => "Acesse seu perfil no Moodle, vá até 'Configurações' e escolha a opção para mudar sua senha.",
    "problema no login" => "Verifique se está digitando o usuário e senha corretamente. Se o problema persistir, contate o suporte.",
    "criar conta" => "Se sua instituição permite, você pode criar uma conta na página de cadastro do Moodle."
];

if ($_SESSION["context"]) {
    $resposta = $respostas_guiadas[$message] ?? "Desculpe, não entendi. Pode reformular?";
    $_SESSION["context"] = null;
} else {
    $resposta = "Desculpe, $userName, não entendi. Tente perguntar de outra forma.";

    foreach ($respostas as $chave => $valor) {
        if (strpos($message, $chave) !== false) {
            $resposta = "$userName, " . $valor;
            break;
        }
    }

    foreach ($perguntas as $chave => $pergunta) {
        if (strpos($message, $chave) !== false) {
            $resposta = $pergunta;
            $_SESSION["context"] = $chave;
            break;
        }
    }
}

echo $resposta;
?>
