<?php
include_once("conexaodb.php");

// Verifica se o nome do curso está sendo recebido corretamente pelo backend (PHP).
// var_dump($_GET);

$nome_curso = $_GET['nome_curso'];

// echo "O nome do curso é: " . $nome_curso;

if (empty($nome_curso)) {
    echo "Erro: O nome do curso não pode ser vazio.";
    die("<a href='javascript:history.back()'>Voltar</a>");
}

// Verifica se o nome do curso informado já existe no banco
try {
    $stmt = $pdo->prepare("SELECT * FROM cursointeresse WHERE Nome = :nome_curso");
    $stmt->bindParam(':nome_curso', $nome_curso);
    $stmt->execute();

    // Retorna as linhas de resultados como um vetor (array) associativo
    // Ao invés de tornar "array[0]" para a 1ª coluna, ele retorna "array[idUsuarios]"
    // Ou seja, um array associativo vincula o nome da coluna aos resultados retornados.
    $resultados = $stmt->setFetchMode(PDO::FETCH_ASSOC);

    // Pegue os resultados
    $dados = $stmt->fetchAll();
    if(count($dados) >= 1) {
        echo "Erro: O nome do curso informado já existe no banco de dados.<br>";
        die("<a href='javascript:history.back()'>Voltar</a>");
    }
} catch(PDOException $e) {
  echo "Erro durante a interação com o banco de dados: " . $e->getMessage();
}

try {
    $stmt = $pdo->prepare("INSERT INTO `cursointeresse`(`Nome`) VALUES (:nome_curso)");
    $stmt->bindParam(':nome_curso', $nome_curso);
    $stmt->execute();
    
    //se o comando foi executado com sucesso, o número de linhas adicionadas é maior do que zero.
    if ($stmt->rowCount() > 0){
        echo "O curso '" . $nome_curso . "' foi adicionado com sucesso ao banco de dados.<br>";
        echo "Você será redirecionado automaticamente a página anterior após 5 segundos.<br>";
        //retorna a página anterior após 5 segundos usando JavaScript.
        echo"<script>
         setTimeout(function() {
            window.location.replace('" . $_SERVER["HTTP_REFERER"] . "');
         }, 5000);
       </script>";
        echo "Caso você não seja redirecionado automaticamente, <a href='" . $_SERVER["HTTP_REFERER"] . "'>clique aqui</a>.";
    }
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}
