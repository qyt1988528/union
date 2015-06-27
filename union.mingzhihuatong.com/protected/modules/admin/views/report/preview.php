<table class="table table-bordered">
    <thead>
    <tr>
        <?php foreach($this->getAdminAttributes() as $field) { ?>
            <th>
                <?php echo AdvData::model()->getAttributeLabel($field);?>
            </th>
        <?php } ?>
    </tr>
    </thead>
    <tbody>
    <?php foreach($models as $model) {
        $duplicated = $model->isDataExists();
        ?>
        <tr<?php if($duplicated){ ?> class="duplicated" <?php }?>>
            <?php foreach($this->getAdminAttributes() as $field) {?>
                <td<?php if(!$duplicated && $this->getEditType($field)){?> data-edittype="<?php echo $this->getEditType($field);?>"<?php } ?>>
                    <span><?php echo $model->getAttribute($field); ?></span>
                </td>
            <?php } ?>
        </tr>
    <?php } ?>
    </tbody>
</table>
