<?php
require_once "db.php";

$sql = "SELECT nome_fun, CPF, email, setor, quantidade_pecas_no_dia, status_fun FROM funcionario ";
$stmt = $pdo->query($sql);
$funcionario = $stmt->fetchAll();
?>

<h1>Cadastrar funcionário</h1>

<a href="cadastrar_fun.php">Adicionar</a>

<br><br>

<table border="1" cellpadding="8">
<tr>
<th>Funcionário</th>
<th>CPF</th>
<th>E-mail</th>
<th>Setor</th>
<th>Quantidade de peças no dia</th>
<th>Status</th>
<th>Ações</th>
</tr>

<?php foreach ($funcionario as $fun) { ?>
<tr>
<td><?php echo $funcionario["nome_fun"]; ?></td>
<td><?php echo $funcionario["CPF"]; ?></td>
<td><?php echo $funcionario["email"]; ?></td>
<td><?php echo $funcionario["setor"]; ?></td>
<td><?php echo $funcionario["quantidade_pecas_no_dia"]; ?></td>
<td><?php echo $funcionario["status_fun"]; ?></td>
<td>
<a href="editar_fun.php?id=<?php echo $fun["nome_fun"]; ?>">Editar</a> |
<a href="excluir_fun.php?id=<?php echo $fun["nome_fun"]; ?>">Excluir</a>
</td>
</tr>
<?php } ?>
</table>
