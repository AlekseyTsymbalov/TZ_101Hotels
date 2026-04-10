<?php
$currentPage = (int) ($pager->getCurrentPage('default') ?? 1);
$pageCount = (int) ($pager->getPageCount('default') ?? 1);
?>

<div class="comments-wrapper">
    <?php if (empty($comments)): ?>
        <div class="alert alert-light border text-center mb-0">
            Комментариев пока нет. Будьте первым.
        </div>
    <?php else: ?>
        <?php foreach ($comments as $comment): ?>
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-start">
                        <div class="mb-3 mb-md-0">
                            <div class="font-weight-bold"><?= esc($comment['name']) ?></div>
                            <div class="text-muted small">Пользовательская дата: <?= esc($comment['date']) ?></div>
                            <div class="text-muted small">Добавлен: <?= esc($comment['created_at']) ?></div>
                            <div class="text-muted small">ID: <?= esc($comment['id']) ?></div>
                        </div>

                        <button
                            type="button"
                            class="btn btn-outline-danger btn-sm js-delete-comment"
                            data-id="<?= esc($comment['id']) ?>"
                        >
                            Удалить
                        </button>
                    </div>

                    <hr>

                    <p class="mb-0 comment-text"><?= nl2br(esc($comment['text'])) ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php if ($pageCount > 1): ?>
        <nav aria-label="Пагинация комментариев">
            <ul class="pagination flex-wrap mb-0">
                <?php for ($page = 1; $page <= $pageCount; $page++): ?>
                    <li class="page-item <?= $page === $currentPage ? 'active' : '' ?>">
                        <a
                            class="page-link js-page-link"
                            href="#"
                            data-page="<?= $page ?>"
                            data-sort="<?= esc($sort) ?>"
                            data-direction="<?= esc($direction) ?>"
                        >
                            <?= $page ?>
                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
