<?php
defined('_JEXEC') or die;
if (JFactory::getApplication()->input->getInt('id', 0) == 0) echo JText::sprintf('COM_PROJECTS_MESSAGE_EDIT_PRICE_AFTER_SAVE');
?>
<fieldset class="adminform">
    <?php foreach ($this->price as $item) :?>
        <div class="control-group form-inline">
            <div class="control-label">
                <label for="price_<?php echo $item['id'];?>" class="hasPopover" title="<?php echo $item['title'];?>" data-content="<?php echo $item['title'];?>">
                    <?php echo $item['title'];?>
                </label>
            </div>
            <div class="controls">
                <div class="span2" style="text-align: right; vertical-align: bottom;"><?php echo $item['cost'];?> x</div>
                <div class="span8" style="vertical-align: bottom;">
                    <input
                        type="text"
                        name="jform[price][1][<?php echo $item['id'];?>]"
                        id="price_<?php echo $item['id'];?>"
                        value="<?php echo $item['value'];?>"
                        class="input"
                        placeholder=""
                        autocomplete="off"
                        style="width: 50px;"
                        aria-invalid="false" />&nbsp;
                    <span><?php echo $item['unit'];?></span>
                    <?php if ($item['isUnit2']):?>
                        + <?php echo $item['cost2'];?> x
                        <input
                                type="text"
                                name="jform[price][2][<?php echo $item['id'];?>]"
                                id="price_<?php echo $item['id'];?>_2"
                                value="<?php echo $item['value2'];?>"
                                class="input"
                                placeholder=""
                                autocomplete="off"
                                style="width: 50px;"
                                aria-invalid="false" />&nbsp;
                        <span><?php echo $item['unit2'];?></span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach;?>
</fieldset>