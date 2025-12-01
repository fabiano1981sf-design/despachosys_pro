<div class="row">
    <div class="col-lg-6 d-none d-lg-block bg-login-image"></div>
    <div class="col-lg-6">
        <div class="p-5">
            <div class="text-center">
                <h1 class="h4 text-gray-900 mb-4">Bem-vindo de Volta!</h1>
            </div>
            
            <?php if (isset($msg)): ?>
                <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show" role="alert">
                    <?php echo $msg['text']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <form class="user" method="POST" action="index.php?page=login">
                <input type="hidden" name="login" value="1">
                <div class="form-group">
                    <input type="email" class="form-control form-control-user"
                        id="exampleInputEmail" aria-describedby="emailHelp"
                        placeholder="Digite seu Email..." name="email" required>
                </div>
                <div class="form-group">
                    <input type="password" class="form-control form-control-user"
                        id="exampleInputPassword" placeholder="Senha" name="password" required>
                </div>
                <div class="form-group">
                    <div class="custom-control custom-checkbox small">
                        <input type="checkbox" class="custom-control-input" id="customCheck">
                        <label class="custom-control-label" for="customCheck">Lembrar-me</label>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-user btn-block">
                    Login
                </button>
            </form>
            <hr>
            <div class="text-center">
                <a class="small" href="index.php?page=rastrear">Rastrear Despacho</a>
            </div>
        </div>
    </div>
</div>
