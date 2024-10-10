<?php
    session_start();
    ob_start();
    include_once('../../config.php');

	// Verifica se os dados foram enviados
	if (isset($_POST['category_order'])) {
		$categoryOrder = $_POST['category_order']; // Array de IDs na nova ordem

		// Atualiza o campo 'position' para cada categoria na ordem correta
		foreach ($categoryOrder as $position => $categoryId) {
			$stmt = $conn_pdo->prepare("UPDATE tb_categories SET position = :position WHERE id = :id");
			$stmt->execute([
				':position' => $position + 1, // Inicia com 1
				':id' => $categoryId
			]);
		}

		echo json_encode(['status' => 'success', 'message' => 'Ordem das categorias atualizada com sucesso.']);
	} else {
		echo json_encode(['status' => 'error', 'message' => 'Dados inválidos.']);
	}
	?>