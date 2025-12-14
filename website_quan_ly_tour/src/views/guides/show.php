<?php ob_start(); ?>

<link rel="stylesheet" href="<?= BASE_URL ?>/public/css/index.css">

<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1 fw-bold text-dark">Chi tiết Hướng dẫn viên</h3>
                <p class="text-muted mb-0">Thông tin hồ sơ và liên hệ</p>
            </div>
            <div>
                <a href="index.php?act=guides" class="btn btn-light border fw-bold">Quay lại</a>
            </div>
        </div>

        <div class="card card-modern">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <?php if (!empty($guide['avatar'])): ?>
                            <img src="<?= BASE_URL ?>public/uploads/guides/<?= htmlspecialchars($guide['avatar']) ?>" class="rounded-circle mb-3" style="width:140px;height:140px;object-fit:cover;">
                        <?php else: ?>
                            <div class="avatar-circle bg-soft-success d-flex align-items-center justify-content-center rounded-circle mb-3" style="width:140px;height:140px;font-size:48px;font-weight:bold;">
                                <?= htmlspecialchars(strtoupper(substr($guide['name'] ?? ($guide['user_id'] ?? 'U'),0,1))) ?>
                            </div>
                        <?php endif; ?>

                        <h4 class="fw-bold"><?= htmlspecialchars($guide['name'] ?? '-') ?></h4>
                        <div class="small text-muted"><i class="bi bi-envelope me-1"></i> <?= htmlspecialchars($guide['email'] ?? '-') ?></div>
                        <div class="mt-2">
                            <a href="index.php?act=guides/edit&id=<?= $guide['id'] ?>" class="btn btn-warning me-1">Sửa</a>
                            <a href="index.php?act=guides/delete&id=<?= $guide['id'] ?>" class="btn btn-danger" onclick="return confirm('Xác nhận xóa hồ sơ HDV này?')">Xóa</a>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <h5 class="mb-3">Chi tiết hồ sơ</h5>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">ID Profile</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['id']) ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">User ID</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['user_id'] ?? '-') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Ngày sinh</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['birthdate'] ?? '-') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Điện thoại</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['phone'] ?? '-') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Chứng chỉ</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['certificate'] ?? '-') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Ngôn ngữ</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['languages'] ?? '-') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Kinh nghiệm</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['experience'] ?? '-') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Lịch sử</div>
                            <div class="col-sm-8">
                                <?php
                                    $historyRaw = $guide['history'] ?? '';
                                    // Try to decode JSON history (common when history stored as JSON)
                                    $decoded = null;
                                    if (!empty($historyRaw)) {
                                        $decoded = json_decode($historyRaw, true);
                                        if (json_last_error() !== JSON_ERROR_NONE) {
                                            $decoded = null;
                                        }
                                    }

                                    // Recursive renderer for arrays
                                    function render_history_item($item) {
                                        if (is_array($item)) {
                                            // Special case: tours list -> fetch tour names and link to tour pages
                                            if (isset($item['tours']) && is_array($item['tours'])) {
                                                $ids = array_filter(array_map('intval', $item['tours']));
                                                if (!empty($ids)) {
                                                    $placeholders = implode(',', array_fill(0, count($ids), '?'));
                                                    $pdo = getDB();
                                                    $stmt = $pdo->prepare("SELECT id, name FROM tours WHERE id IN ($placeholders)");
                                                    $stmt->execute($ids);
                                                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    $map = [];
                                                    foreach ($rows as $r) $map[$r['id']] = $r['name'];
                                                    $html = '<ul class="list-unstyled mb-0">';
                                                    foreach ($ids as $tid) {
                                                        $tname = htmlspecialchars($map[$tid] ?? ('Tour #'. $tid));
                                                        $html .= '<li><a class="btn btn-success btn-sm rounded-pill d-inline-flex align-items-center me-1 mb-1" href="index.php?act=tour-show&id=' . $tid . '"><i class="bi bi-check-circle me-1"></i>' . $tname . '</a></li>';
                                                    }
                                                    $html .= '</ul>';
                                                    return $html;
                                                }
                                            }

                                            $html = '<ul class="list-unstyled mb-0">';
                                            foreach ($item as $k => $v) {
                                                $key = is_string($k) ? htmlspecialchars($k) : null;
                                                if (is_array($v)) {
                                                    $html .= '<li>' . ($key ? '<strong>'.$key.':</strong> ' : '') . render_history_item($v) . '</li>';
                                                } else {
                                                    $html .= '<li>' . ($key ? '<strong>'.$key.':</strong> ' : '') . htmlspecialchars((string)$v) . '</li>';
                                                }
                                            }
                                            $html .= '</ul>';
                                            return $html;
                                        }
                                        return '<div>' . nl2br(htmlspecialchars((string)$item)) . '</div>';
                                    }
                                ?>

                                <?php
                                    // Prepare entries to render
                                    $entries = [];
                                    if (!empty($decoded) && is_array($decoded)) {
                                        // If associative with 'tours' key
                                        if (array_key_exists('tours', $decoded)) {
                                            $entries[] = ['type' => 'tours', 'value' => $decoded['tours']];
                                        } else {
                                            // If numeric array
                                            $isNumeric = array_keys($decoded) === range(0, count($decoded) - 1);
                                            if ($isNumeric) {
                                                foreach ($decoded as $d) $entries[] = ['type' => 'item', 'value' => $d];
                                            } else {
                                                // associative: render each key => value
                                                foreach ($decoded as $k => $v) $entries[] = ['type' => 'kv', 'key' => $k, 'value' => $v];
                                            }
                                        }
                                    } elseif (!empty($historyRaw)) {
                                        $entries[] = ['type' => 'text', 'value' => $historyRaw];
                                    }

                                    if (empty($entries)) {
                                        echo '-';
                                    } else {
                                        // render as list-group with collapse for long lists
                                        $limit = 5;
                                        $total = count($entries);
                                        $collapseId = 'history-collapse-' . ($guide['id'] ?? uniqid());
                                        echo '<div class="list-group">';
                                        foreach ($entries as $i => $e) {
                                            $hidden = ($i >= $limit) ? ' d-none d-md-block' : '';
                                            $collapseWrapperStart = $i === $limit ? "<div id=\"$collapseId\" class=\"collapse\">" : '';
                                            $collapseWrapperEnd = ($i === $total - 1 && $total > $limit) ? '</div>' : '';
                                            if ($i === $limit) echo $collapseWrapperStart;

                                            echo '<div class="list-group-item bg-light small text-muted' . $hidden . '">';
                                            if ($e['type'] === 'tours') {
                                                $ids = is_array($e['value']) ? $e['value'] : json_decode($e['value'], true);
                                                if (!is_array($ids)) $ids = [$ids];
                                                // fetch tour names
                                                $idsClean = array_values(array_filter(array_map('intval', $ids)));
                                                if (!empty($idsClean)) {
                                                    $placeholders = implode(',', array_fill(0, count($idsClean), '?'));
                                                    $pdo = getDB();
                                                    $stmt = $pdo->prepare("SELECT id, name FROM tours WHERE id IN ($placeholders)");
                                                    $stmt->execute($idsClean);
                                                    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    $map = [];
                                                    foreach ($rows as $r) $map[$r['id']] = $r['name'];
                                                    foreach ($idsClean as $tid) {
                                                        $tname = htmlspecialchars($map[$tid] ?? ('Tour #'. $tid));
                                                        echo '<a class="btn btn-success btn-sm rounded-pill d-inline-flex align-items-center me-1 mb-1" href="index.php?act=tour-show&id=' . $tid . '"><i class="bi bi-check-circle me-1"></i>' . $tname . '</a>';
                                                    }
                                                } else {
                                                    echo '<span class="text-muted">No tours</span>';
                                                }
                                            } elseif ($e['type'] === 'kv') {
                                                echo '<strong>' . htmlspecialchars($e['key']) . ':</strong> ';
                                                if (is_array($e['value'])) echo render_history_item($e['value']); else echo nl2br(htmlspecialchars((string)$e['value']));
                                            } else { // item or text
                                                if (is_array($e['value'])) echo render_history_item($e['value']); else echo nl2br(htmlspecialchars((string)$e['value']));
                                            }
                                            echo '</div>';

                                            if ($i === $total - 1) echo $collapseWrapperEnd;
                                        }
                                        echo '</div>';

                                        if ($total > $limit) {
                                            echo '<div class="mt-2">';
                                            echo '<a class="btn btn-sm btn-link" data-bs-toggle="collapse" href="#' . $collapseId . '" role="button" aria-expanded="false" aria-controls="' . $collapseId . '">Xem thêm</a>';
                                            echo '</div>';
                                        }
                                    }
                                ?>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Đánh giá</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['rating'] ?? '-') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Sức khỏe</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['health_status'] ?? '-') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Nhóm</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['group_type'] ?? '-') ?></div>
                        </div>
                        <div class="row mb-2">
                            <div class="col-sm-4 text-muted">Chuyên môn</div>
                            <div class="col-sm-8">
                                <?php if (!empty($guide['specialty'])): ?>
                                    <?php
                                        // split by commas or semicolons and trim
                                        $items = preg_split('/[,;]+/', $guide['specialty']);
                                        $items = array_filter(array_map('trim', $items));
                                    ?>
                                    <?php foreach ($items as $it): ?>
                                        <span class="badge bg-primary me-1 mb-1"><?= htmlspecialchars($it) ?></span>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="row mt-3">
                            <div class="col-sm-4 text-muted">Ngày tạo</div>
                            <div class="col-sm-8"><?= htmlspecialchars($guide['created_at'] ?? '-') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();

view('layouts.AdminLayout', [
    'title' => 'Chi tiết Hướng dẫn viên',
    'pageTitle' => 'Guide detail',
    'content' => $content,
    'breadcrumb' => [
        ['label' => 'Dashboard', 'url' => BASE_URL . 'home'],
        ['label' => 'Guides', 'url' => BASE_URL . 'guides'],
        ['label' => 'Detail', 'active' => true],
    ],
]);

