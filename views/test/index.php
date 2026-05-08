<!-- <?php
// dd($category[0]['category']['name']);
?> -->

<?php foreach ($data as $item) : ?>
    <h2><?= $item['category']->name ?></h2>
    <ul>
        <?php foreach ($item['products'] as $product) : ?>
            <li><?= $product->name ?></li>
        <?php endforeach; ?>
    </ul>
<?php endforeach;  ?>

