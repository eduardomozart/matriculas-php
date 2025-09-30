<?php
include_once("login_verifica.php");
include_once("conexaodb.php");

// Escreve o conteúdo recebido pelo formulário na tela
// var_dump $_GET;

$nome_usuario = $_GET["nome_usuario"];
$senha = $_GET["senha"];
$id_perfil = $_GET["id_perfil"];

echo "O nome de usuário é: " . $nome_usuario . "<br>";
echo "A senha é: " . $senha . "<br>";
echo "O perfil é: ";
// Valida se o perfil é válido
switch ($id_perfil) {
    case 1:
        echo "Administrador<br>";
        break;
    case 2:
        echo "Auxiliar de matrículas<br>";
        break;
    case 3:
        echo "Atendente<br>";
        break;
    default:
        // Se não for nenhuma das opções (1, 2 ou 3),
        // Encerra o código PHP (não processa nenhuma linha depois do "die")
        echo "Erro: Perfil inválido!<br>";
        die("<a href='javascript:history.back()'>Voltar</a>");
}

if (empty($nome_usuario)) {
    echo "Erro: O nome de usuário não pode estar vazio.<br>";
    die("<a href='javascript:history.back()'>Voltar</a>");
}

if (empty($senha)) {
    echo "Erro: A senha não pode estar vazia.<br>";
    die("<a href='javascript:history.back()'>Voltar</a>");
}

// Verifica se o nome de usuário informado já existe no banco
try {
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE NomeUsuario = :nome_usuario");
    $stmt->bindParam(':nome_usuario', $nome_usuario);
    $stmt->execute();

    // Retorna as linhas de resultados como um vetor (array) associativo
    // Ao invés de tornar "array[0]" para a 1ª coluna, ele retorna "array[idUsuarios]"
    // Ou seja, um array associativo vincula o nome da coluna aos resultados retornados.
    $resultados = $stmt->setFetchMode(PDO::FETCH_ASSOC);

    // Pegue os resultados
    $dados = $stmt->fetchAll();
    if(count($dados) >= 1) {
        echo "Erro: O nome de usuário informado já existe no banco de dados.<br>";
        die("<a href='javascript:history.back()'>Voltar</a>");
    }
} catch(PDOException $e) {
  echo "Erro: " . $e->getMessage();
}

// Depois de todas as validações realizadas, criar o novo usuário no banco de dados.
try {
    $stmt = $pdo->prepare("INSERT INTO `usuarios`(`NomeUsuario`, `Senha`, `idPerfil`) VALUES (:nome_usuario,:senha,:id_perfil)");
    $stmt->bindParam(':nome_usuario', $nome_usuario);
    $stmt->bindParam(':senha', md5($senha));
    $stmt->bindParam(':id_perfil', $id_perfil);
    $stmt->execute();

    // Se o comando foi executado com sucesso, o número de linhas adicionadas é maior do que zero.
    if ($stmt->rowCount() > 0) {
        echo "O usuário '" . $nome_usuario . "' foi adicionado com sucesso ao banco de dados.<br>";
        echo "Você será redirecionado automaticamente a página anterior após 5 segundos.<br>";
        // Retorna a página anterior após 5 segundos usando JavaScript.
        echo "<script>
          setTimeout(function() {
            history.back();
          }, 5000);
        </script>";
        echo "Caso você não seja redirecionado automaticamente, <a href='javascript:history.back()'>clique aqui</a>.";
    }
} catch(PDOException $e) {
    echo "Erro: " . $e->getMessage();
}