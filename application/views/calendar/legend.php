<?php foreach ($typeDetails as $type): ?>
    <label class="label " style="<?=$type['color'] ? 'background-color:'.$type['color'] : ''?>;<?=$type['textcolor'] ? 'color:'.$type['textcolor'] : ''?>" for="chk<?=$type['id']?>">
        <?php if (isset($legendWithCheckbox)) { ?>
            <input type="checkbox" checked id="chk<?=$type['id']?>" class="filterStatus">
        <?php } ?>
        <?=$type['name']?></label>
<?php endforeach ?>
