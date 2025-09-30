<?php
include_once("login_verifica.php");
include_once("conexaodb.php");

// var_dump($_GET);

$id_curso = intval($_GET["id_curso"]);

// ID do curso não pode ser vazio.
if (empty($id_curso)) {
    echo "Erro: ID do curso não pode ser vazio!<br>";
    die("<a href='javascript:history.back()'>Voltar</a>");
}

// ID do curso deve ser um número inteiro.
if (!is_int($id_curso)) {
    echo "Erro: ID do curso não é um número inteiro!<br>";
    die("<a href='javascript:history.back()'>Voltar</a>");
}

try {
    $stmt = $pdo->prepare("DELETE FROM cursointeresse WHERE idCursoInteresse = :id_curso");
    $stmt->bindParam(":id_curso",$id_curso);
    // Se o comando de exclusão foi executado com sucesso.
    if($stmt->execute()) {
        echo "Curso #" . $id_curso . " excluído com sucesso.<br>";
        echo "<a href='" . $_SERVER["HTTP_REFERER"] . "'>Voltar</a>";
    }
} catch(PDOException $e) {
    die("Erro durante a interação com o banco de dados: " . $e->getMessage());
}