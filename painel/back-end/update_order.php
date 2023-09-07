<?php
  if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["order"])) {
    // Aqui você deve implementar a lógica para atualizar a ordem no banco de dados
    // O array $_POST["order"] contém a nova ordem dos IDs das linhas
    // Você pode usar isso para atualizar a ordem no banco de dados usando a cláusula IN do SQL, por exemplo
    // Certifique-se de validar e sanitizar os dados antes de usá-los no SQL
    // E retornar uma resposta apropriada, como um JSON com status de sucesso ou erro
    $response = ["status" => "success"];
  } else {
    $response = ["status" => "error", "message" => "Invalid request"];
  }
  echo json_encode($response);
?>