<?php

// Configuração
ini_set('display_errors', 1); 
error_reporting(E_ALL);

// Headers para API REST
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

// 1. Conexão com o Banco de Dados
require_once 'conexao.php'; // Garanta que este arquivo estabelece a conexão $con
$con->set_charset("utf8");

// 2. Recebimento e Decodificação do JSON
$inputJSON = file_get_contents('php://input');
$jsonParam = json_decode($inputJSON, true);

if (!$jsonParam) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Dados JSON inválidos ou ausentes.']);
    exit;
}

// 3. Extração, Validação e Formatação dos Dados
$nmAluno         = trim($jsonParam['nmAluno'] ?? '');
$dtMatricula    = trim($jsonParam['dtMatricula'] ?? '');
$dtNascimento   = trim($jsonParam['dtNascimento'] ?? '');
$nrTelefone      = trim($jsonParam['nrTelefone'] ?? '');
$idCursoCadastro = intval($jsonParam['idCursoCadastro'] ?? 0);

// 4. Preparação da Query
$sql = "
    INSERT INTO aluno (nmAluno, dtMatricula, dtNascimento, nrTelefone, idCursoCadastro)
    VALUES (?, ?, ?, ?, ?)
";

$stmt = $con->prepare($sql);

if (!$stmt) {
    http_response_code(500); // Internal Server Error
    error_log("Erro ao preparar a consulta: " . $con->error);
    echo json_encode(['success' => false, 'message' => 'Ocorreu um erro interno na preparação da query.']);
    exit;
}

// 5. Bind e Execução
// Tipagem: s (nmAluno), s (dtMatricula), s (dtNascimento), s (nrTelefone), i (idCursoCadastro)
$stmt->bind_param("ssssi", $nmAluno, $dtMatricula, $dtNascimento, $nrTelefone, $idCursoCadastro);

if ($stmt->execute()) {
    http_response_code(201); // Created
    echo json_encode([
        'success' => true, 
        'message' => 'Aluno matriculado com sucesso!',
        'nrMatricula' => $stmt->insert_id // Retorna o ID de matrícula gerado
    ]);
} else {
    // Tratamento de erro de execução (ex: falha na conexão, chave estrangeira inexistente, etc.)
    http_response_code(500); 
    error_log("Erro no registro do aluno: " . $stmt->error); 
    
    // Mensagem genérica para o usuário final por segurança
    echo json_encode(['success' => false, 'message' => 'Erro ao registrar aluno no banco de dados.']);
}

$stmt->close();
$con->close();

?>