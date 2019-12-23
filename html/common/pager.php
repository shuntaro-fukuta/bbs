<div>
  <?php if (isset($page_numbers)) : ?>
  <?php if (!($paginator->isFirstPage())) : ?>
    <a href="<?php echo $paginator->getPreviousPageUrl() ?>">&lt;</a>
  <?php endif ?>

  <?php foreach ($page_numbers as $page_number) : ?>
    <?php if (!($paginator->isCurrentPage($page_number))) : ?>
    <a href="<?php echo $paginator->buildPageUrl($page_number) ?>"><?php echo $page_number ?></a>
    <?php else : ?>
    <?php echo $page_number ?>
    <?php endif ?>
  <?php endforeach ?>

  <?php if (!($paginator->isLastPage())) : ?>
    <a href="<?php echo $paginator->getNextPageUrl() ?>">&gt;</a>
  <?php endif ?>
  <?php endif ?>
</div>