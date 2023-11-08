<?php
// Ensure $totalPages and $page variables are passed to this file before including it.

if (!isset($totalPages) || !isset($page)) {
    // Handle error: required variables not set
    exit('Pagination requires totalPages and currentPage variables.');
}

$searchQuery = $_GET['search_query'] ?? '';
?>

<!-- Pagination link generation with search query included -->
<ul class="pagination">
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
        <?php $link = '?page=' . $i . (!empty($searchQuery) ? '&search_query=' . urlencode($searchQuery) : ''); ?>
        <li class='page-item <?= ($page === $i) ? 'active' : '' ?>'>
            <a class='page-link' href='<?= $link ?>'>
                <?= $i ?>
            </a>
        </li>
    <?php endfor; ?>
</ul>