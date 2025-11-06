<?php

// Configurações de exibição de erros
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Inclui o script de conexão existente (conexao.php)
// Certifique-se de que este arquivo contém a variável $con (sua conexão MySQLi)
require_once 'conexao.php';
// Define o conjunto de caracteres da conexão para UTF-8
$con->set_charset("utf8");

// Decodifica a entrada JSON (ignorado, mas mantido da sua estrutura original)
json_decode(file_get_contents('php://input'), true);

// NOVO SQL: Seleciona todos os campos da tabela 'aluno'
$sql = "SELECT nrMatricula, nmAluno, dtMatricula, dtNascimento, nrTelefone, idCursoCadastro FROM aluno";

$result = $con->query($sql);

$response = [];

if ($result && $result->num_rows > 0) {
    // Itera sobre os resultados e adiciona ao array de resposta
    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
} else {
    // Resposta de erro/vazia: Estrutura baseada nos campos da tabela 'aluno'
    $response[] = [
        "nrMatricula" => 0,
        "nmAluno" => "",
        "dtMatricula" => "",
        "dtNascimento" => "",
        "nrTelefone" => "",
        "idCursoCadastro" => 0
    ];
}

// Define o cabeçalho para JSON e UTF-8
header('Content-Type: application/json; charset=utf-8');
// Envia a resposta como JSON, preservando caracteres UTF-8 (acentos, etc.)
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// Fecha a conexão com o banco de dados
$con->close();
?>