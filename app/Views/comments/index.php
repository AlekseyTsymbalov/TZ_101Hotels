<!doctype html>
<html lang="ru">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Комментарии') ?></title>

    <meta name="csrf-token-name" content="<?= csrf_token() ?>">
    <meta name="csrf-token-value" content="<?= csrf_hash() ?>">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/css/comments.css">
</head>
<body>
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4">
                <h1 class="h3 mb-3 mb-md-0">Список комментариев</h1>

                <div class="form-inline">
                    <label class="mr-2" for="sort">Сортировка</label>
                    <select id="sort" class="form-control mr-2">
                        <option value="created_at" <?= $sort === 'created_at' ? 'selected' : '' ?>>По дате добавления</option>
                        <option value="id" <?= $sort === 'id' ? 'selected' : '' ?>>По ID</option>
                    </select>

                    <select id="direction" class="form-control">
                        <option value="desc" <?= $direction === 'desc' ? 'selected' : '' ?>>По убыванию</option>
                        <option value="asc" <?= $direction === 'asc' ? 'selected' : '' ?>>По возрастанию</option>
                    </select>
                </div>
            </div>

            <div id="alert-box"></div>

            <div id="comments-list-container">
                <?= view('comments/partials/list', [
                    'comments' => $comments,
                    'pager' => $pager,
                    'sort' => $sort,
                    'direction' => $direction,
                ]) ?>
            </div>

            <div class="card shadow-sm mt-4">
                <div class="card-body">
                    <h2 class="h5 mb-3">Добавить комментарий</h2>

                    <form id="comment-form" action="/comments" method="post" novalidate>
                        <?= csrf_field() ?>

                        <div class="form-group">
                            <label for="name">Email</label>
                            <input
                                type="email"
                                class="form-control"
                                id="name"
                                name="name"
                                maxlength="255"
                                placeholder="name@example.com"
                                required
                            >
                            <div class="invalid-feedback" data-error-for="name"></div>
                        </div>

                        <div class="form-group">
                            <label for="date">Дата</label>
                            <input
                                type="text"
                                class="form-control"
                                id="date"
                                name="date"
                                maxlength="255"
                                placeholder="Например: 10.04.2026 14:30"
                                required
                            >
                            <div class="invalid-feedback" data-error-for="date"></div>
                        </div>

                        <div class="form-group">
                            <label for="text">Комментарий</label>
                            <textarea
                                class="form-control"
                                id="text"
                                name="text"
                                rows="4"
                                maxlength="1000"
                                placeholder="Введите текст комментария"
                                required
                            ></textarea>
                            <div class="invalid-feedback" data-error-for="text"></div>
                        </div>

                        <button type="submit" class="btn btn-primary">Отправить</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="/assets/js/comments.js"></script>
</body>
</html>
