<nav aria-label="Pagination">
    <ul class="pagination pagination-sm">
        <li class="page-item <?php if ($this->pagePrevious < 1): ?>disabled<?php endif ?>">
            <a class="page-link"
               href="?application=topic&controller=<?php echo $this->topic->id; ?>&page=1"
               data-toggle="tooltip" data-placement="bottom" title="Première page"
               aria-label="Première page">
                <span aria-hidden="true">&laquo;</span>
            </a>
        </li>
        <li class="page-item <?php if ($this->pagePrevious < 1): ?>disabled<?php endif ?>">
            <a class="page-link"
               href="?application=topic&controller=<?php echo $this->topic->id; ?>&page=<?php echo $this->pagePrevious; ?>">Précédent</a>
        </li>
        <?php for ($i = $this->pageFirstLink; $i <= $this->pageLastLink; $i++): ?>
            <li <?php echo $this->pageCurrent === $i
                ? 'class="page-item active d-none d-sm-block" aria-current="page"'
                : 'class="page-item d-none d-sm-block"';
            ?>>
                <a class="page-link"
                   href="?application=topic&controller=<?php echo $this->topic->id; ?>&page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            </li>
        <?php endfor; ?>
        <li class="page-item <?php if ($this->pageNext > $this->pagesNb): ?>disabled<?php endif ?>">
            <a class="page-link"
               href="?application=topic&controller=<?php echo $this->topic->id; ?>&page=<?php echo $this->pageNext; ?>">Suivant</a>
        </li>
        <li class="page-item <?php if ($this->pageNext > $this->pagesNb): ?>disabled<?php endif ?>">
            <a class="page-link"
               href="?application=topic&controller=<?php echo $this->topic->id; ?>&page=<?php echo $this->pagesNb; ?>"
               data-toggle="tooltip" data-placement="bottom" title="Dernière page"
               aria-label="Dernière page">
                <span aria-hidden="true">&raquo;</span>
            </a>
        </li>
        <li class="d-flex align-items-center ml-3">
            <small>Page <?php echo $this->pageCurrent; ?> sur <?php echo $this->pagesNb; ?></small>
        </li>
    </ul>
</nav>