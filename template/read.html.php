<?php include __DIR__ . "/header.html.php"; ?>
<?php
global $articles;
$v = $articles[$vars['category']][$vars['offset']];
?>
<div class="mdui-typo">
    <h1>
        <?= $v['title'] ?>
        <br />
        <small>
            作者: <?= $v['author'] ?> &nbsp; &nbsp;
            时间: <?= date("Y-m-d H:i:s", $v['time']) ?> &nbsp; &nbsp;
            分类: <?= $v['category'] ?>
        </small>
    </h1>
    <div>
        <?= $v['content'] ?>
    </div>
</div>
<?php include __DIR__ . "/copyright.html.php"; ?>
<?php include __DIR__ . "/footer.html.php"; ?>