<?php
/**
 * Template de paginação simples em português
 */
use CodeIgniter\Pager\PagerRenderer;

/**
 * @var PagerRenderer $pager
 */
$pager->setSurroundCount(2);
?>

<nav aria-label="Navegação de páginas">
    <ul class="pagination justify-content-center">
        <?php if ($pager->hasPreviousPage()) : ?>
            <li class="page-item">
                <a class="btn page-link" href="<?= $pager->getFirst() ?>">Primeira</a>
            </li>
            <li class="page-item">
                <a class="btn page-link" href="<?= $pager->getPreviousPage() ?>">Anterior</a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a class="btn page-link" href="<?= $link['uri'] ?>"><?= $link['title'] ?></a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNextPage()) : ?>
            <li class="page-item">
                <a class="btn page-link" href="<?= $pager->getNextPage() ?>">Próxima</a>
            </li>
            <li class="page-item">
                <a class="btn page-link" href="<?= $pager->getLast() ?>">Última</a>
            </li>
        <?php endif ?>
    </ul>
</nav>
