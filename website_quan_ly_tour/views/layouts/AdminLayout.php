<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8" />
    <title><?= $title ?? 'Trang chủ quản lý tour' ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="author" content="FPOLY HN" />
    <meta name="description" content="Website Quản Lý Tour FPOLY HN"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/styles/overlayscrollbars.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"/>
    <link rel="stylesheet" href="<?= asset('dist/css/adminlte.css') ?>" />
</head>
<body class="layout-fixed sidebar-expand-lg bg-body-tertiary">
<div class="app-wrapper">
    <?php block('header'); ?>
    <?php block('aside'); ?>

    <main class="app-main">
        <div class="app-content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-6">
                        <h3 class="mb-0"><?= $pageTitle ?? 'Trang chủ' ?></h3>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-sm-end">
                            <li class="breadcrumb-item"><a href="<?= BASE_URL ?>home">Home</a></li>
                            <?php if (isset($breadcrumb)): ?>
                                <?php foreach ($breadcrumb as $item): ?>
                                    <li class="breadcrumb-item <?= $item['active'] ?? false ? 'active' : '' ?>"
                                        <?= $item['active'] ?? false ? 'aria-current="page"' : '' ?>>
                                        <?php if ($item['active'] ?? false): ?>
                                            <?= $item['label'] ?>
                                        <?php else: ?>
                                            <a href="<?= $item['url'] ?>"><?= $item['label'] ?></a>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="app-content">
            <div class="container-fluid">
                <?= $content ?? '' ?>
            </div>
        </div>
    </main>

    <?php block(block: 'footer'); ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/overlayscrollbars@2.10.1/browser/overlayscrollbars.browser.es6.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
<script src="<?= asset('dist/js/adminlte.js') ?>"></script>
</body>
</html>
