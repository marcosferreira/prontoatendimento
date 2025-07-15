<?= $this->extend('Shield/layout') ?>

<?= $this->section('title') ?><?= lang('Auth.login') ?> <?= $this->endSection() ?>

<?= $this->section('main') ?>

<div class="row g-0 vh-100">
    <!-- Coluna do Banner (Esquerda) -->
    <div class="col-lg-7 d-none d-lg-block">
        <div class="auth-banner">
            <!-- O conteúdo do banner é controlado via CSS (background-image) -->
        </div>
    </div>

    <!-- Coluna do Formulário (Direita) -->
    <div class="col-lg-5 d-flex flex-column justify-content-center p-0">
        <div class="auth-card h-100 d-flex flex-column justify-content-center px-md-5 px-4">
            
            <div class="auth-form-container">
                <div class="auth-logo text-center mb-4">
                    <img src="<?= base_url('assets/images/logo-blue.png') ?>" alt="Logo" width="80">
                </div>

                <h5 class="card-title text-center mb-4"><?= lang('Auth.login') ?></h5>

                <?php if (session('error') !== null) : ?>
                    <div class="alert alert-danger" role="alert"><?= session('error') ?></div>
                <?php elseif (session('errors') !== null) : ?>
                    <div class="alert alert-danger" role="alert">
                        <?php if (is_array(session('errors'))) : ?>
                            <?php foreach (session('errors') as $error) : ?>
                                <?= $error ?>
                                <br>
                            <?php endforeach ?>
                        <?php else : ?>
                            <?= session('errors') ?>
                        <?php endif ?>
                    </div>
                <?php endif ?>

                <?php if (session('message') !== null) : ?>
                <div class="alert alert-success" role="alert"><?= session('message') ?></div>
                <?php endif ?>

                <form action="<?= url_to('login') ?>" method="post">
                    <?= csrf_field() ?>

                    <!-- Email -->
                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="floatingEmailInput" name="email" inputmode="email" autocomplete="email" placeholder="<?= lang('Auth.email') ?>" value="<?= old('email') ?>" required>
                        <label for="floatingEmailInput">
                            <i class="fas fa-envelope"></i> <?= lang('Auth.email') ?>
                        </label>
                    </div>

                    <!-- Password -->
                    <div class="form-floating mb-3">
                        <input type="password" class="form-control" id="floatingPasswordInput" name="password" inputmode="text" autocomplete="current-password" placeholder="<?= lang('Auth.password') ?>" required>
                        <label for="floatingPasswordInput">
                            <i class="fas fa-lock"></i> <?= lang('Auth.password') ?>
                        </label>
                    </div>

                    <!-- Remember me & Forgot Password -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <?php if (setting('Auth.sessionConfig')['allowRemembering']): ?>
                            <div class="form-check">
                                <input type="checkbox" name="remember" class="form-check-input" id="remember" <?php if (old('remember')): ?> checked<?php endif ?>>
                                <label class="form-check-label" for="remember">
                                    <?= lang('Auth.rememberMe') ?>
                                </label>
                            </div>
                        <?php endif; ?>

                        <?php if (setting('Auth.allowMagicLinkLogins')) : ?>
                            <a href="<?= url_to('magic-link') ?>"><?= lang('Auth.forgotPassword') ?></a>
                        <?php endif ?>
                    </div>


                    <div class="d-grid col-12 mx-auto m-3">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-sign-in-alt"></i> <?= lang('Auth.login') ?>
                        </button>
                    </div>

                    <?php if (setting('Auth.allowRegistration')) : ?>
                        <!-- <p class="text-center mt-3"><?= lang('Auth.needAccount') ?> <a href="<?= url_to('register') ?>"><?= lang('Auth.register') ?></a></p> -->
                    <?php endif ?>

                </form>
            </div>

        </div>
    </div>
</div>

<?= $this->endSection() ?>
