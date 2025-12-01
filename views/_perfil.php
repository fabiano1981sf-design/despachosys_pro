<?php
// views/_perfil.php - Perfil do Usuário (Atualizado e Seguro)

$pageTitle = "Meu Perfil";
$user = SystemCore::getUser(); // já traz foto_perfil

if (!$user) {
    echo "<script>alert('Usuário não encontrado.'); window.location='index.php?page=logout';</script>";
    exit;
}

// SALVAR ALTERAÇÕES DO PERFIL
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'atualizar_perfil') {
    
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = $_POST['senha'] ?? '';

    if (empty($nome) || empty($email)) {
        $msg = "Nome e e-mail são obrigatórios!";
        $tipo = "danger";
    } else {
        $pdo = DB::getInstance();

        // Atualiza nome e e-mail
        $sql = "UPDATE users SET nome = ?, email = ? WHERE id = ?";
        $params = [$nome, $email, $user['id']];

        // Se informou senha nova, atualiza com hash
        if (!empty($senha)) {
            $sql = "UPDATE users SET nome = ?, email = ?, senha_hash = ? WHERE id = ?";
            $params = [$nome, $email, password_hash($senha, PASSWORD_DEFAULT), $user['id']];
        }

        try {
            $pdo->prepare($sql)->execute($params);
            $_SESSION['user_name'] = $nome;

            // Upload de foto (se houver)
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
                $extensoes = ['jpg','jpeg','png','gif'];
                $ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                
                if (in_array($ext, $extensoes) && $_FILES['foto']['size'] <= 2*1024*1024) { // máx 2MB
                    $novo_nome = "uploads/avatars/" . $user['id'] . "_" . time() . ".$ext";
                    move_uploaded_file($_FILES['foto']['tmp_name'], "../" . $novo_nome);
                    $pdo->prepare("UPDATE users SET foto_perfil = ? WHERE id = ?")->execute([$novo_nome, $user['id']]);
                    $user['foto_perfil'] = $novo_nome; // atualiza na sessão
                }
            }

            $msg = "Perfil atualizado com sucesso!";
            $tipo = "success";
            $user = SystemCore::getUser(); // recarrega dados atualizados
        } catch (Exception $e) {
            $msg = "Erro ao salvar: " . $e->getMessage();
            $tipo = "danger";
        }
    }

    echo "<div class='alert alert-$tipo alert-dismissible fade show'>
            $msg
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}
?>

<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg border-0">
                <div class="card-header bg-primary text-white text-center py-4">
                    <h3 class="mb-0">Meu Perfil</h3>
                </div>
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <img src="<?= htmlspecialchars($user['foto_perfil'] ?? 'assets/img/default-avatar.png') ?>"
                             alt="Foto de <?= htmlspecialchars($user['nome']) ?>"
                             class="rounded-circle img-thumbnail shadow"
                             style="width: 150px; height: 150px; object-fit: cover; border: 4px solid #fff;">
                        <h4 class="mt-3"><?= htmlspecialchars($user['nome']) ?></h4>
                        <p class="text-muted"><?= ucfirst($user['role']) ?> • <?= htmlspecialchars($user['email']) ?></p>
                    </div>

                    <form method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="atualizar_perfil">

                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nome Completo</label>
                                <input type="text" name="nome" class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($user['nome']) ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">E-mail</label>
                                <input type="email" name="email" class="form-control form-control-lg" 
                                       value="<?= htmlspecialchars($user['email']) ?>" required>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Nova Senha <small class="text-muted">(deixe em branco para manter)</small></label>
                                <input type="password" name="senha" class="form-control form-control-lg" 
                                       placeholder="••••••••" minlength="6">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">Foto de Perfil</label>
                                <input type="file" name="foto" class="form-control" accept="image/*">
                                <small class="text-muted">Formatos: JPG, PNG, GIF (máx. 2MB)</small>
                            </div>
                        </div>

                        <div class="d-grid mt-5">
                            <button type="submit" class="btn btn-primary btn-lg">
                                Atualizar Perfil
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center py-4 bg-light">
                    <small class="text-muted">
                        Último acesso: <?= date('d/m/Y H:i') ?> • 
                        ID do usuário: #<?= $user['id'] ?>
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>