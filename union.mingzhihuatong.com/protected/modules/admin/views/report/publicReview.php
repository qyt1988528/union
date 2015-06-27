<style>
    td input{width:10px}
</style>
<table class="table table-bordered">
    <tr>
        <?php foreach($this->getAdminAttributes() as $field) { ?>
        <th>
            <?php echo AdvData::model()->getAttributeLabel($field);?>
        </th>
        <?php } ?>
    </tr>
    <?php foreach($data as $row) { ?>
        <tr<?php if($row['duplicated']){?> class="duplicated" <?php }?>>
            <?php foreach($row as $index => $item) {?>
                <?php if(is_integer($index)) { ?>
                    <td><input name='tr[]' value='<?php echo $item ? $item : '';?>'></input></td>
                <?php } ?>
            <?php } ?>
        </tr>
    <?php } ?>
</table>