<?php
/**
 * Template de paginação personalizado para o SisPAM
 * Versão em Português Brasileiro com Bootstrap 5
 */

use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>
<div class="d-flex flex-column">
<nav aria-label="<?= lang('Pager.pageNavigation') ?>">
    <ul class="pagination justify-content-center">
        <?php if ($pager->hasPreviousPage()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>">
                    <i class="bi bi-chevron-double-left"></i>
                    <span class="d-none d-sm-inline ms-1"><?= lang('Pager.first') ?></span>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getPreviousPage() ?>" aria-label="<?= lang('Pager.previous') ?>">
                    <i class="bi bi-chevron-left"></i>
                    <span class="d-none d-sm-inline ms-1"><?= lang('Pager.previous') ?></span>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="page-link" href="<?= $link['uri'] ?>">
                    <?= $link['title'] ?>
                    <?php if ($link['active']) : ?>
                        <span class="visually-hidden">(página atual)</span>
                    <?php endif ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNextPage()) : ?>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getNextPage() ?>" aria-label="<?= lang('Pager.next') ?>">
                    <span class="d-none d-sm-inline me-1"><?= lang('Pager.next') ?></span>
                    <i class="bi bi-chevron-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a class="page-link" href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.last') ?>">
                    <span class="d-none d-sm-inline me-1"><?= lang('Pager.last') ?></span>
                    <i class="bi bi-chevron-double-right"></i>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>

<?php if ($pager->getPageCount() > 1) : ?>
    <div class="d-flex justify-content-center mt-2">
        <small class="text-muted">
            Página <?= $pager->getCurrentPageNumber() ?> de <?= $pager->getPageCount() ?>
            (<?= number_format($pager->getTotal()) ?> <?= $pager->getTotal() == 1 ? 'registro' : 'registros' ?> no total)
        </small>
    </div>
<?php endif ?>
</div>