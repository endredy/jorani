<?php foreach ($typeDetails as $type): ?>
    <span class="label " style="<?=$type['color'] ? 'background-color:'.$type['color'] : ''?>;<?=$type['textcolor'] ? 'color:'.$type['textcolor'] : ''?>"><?=$type['name']?></span>
<?php endforeach ?>
