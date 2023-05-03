<?php 
/**
 * This view allows an HR admin to create a new leave type.
 * @copyright  Copyright (c) 2014-2023 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.2.0
 */
?>

<?php $attributes = array('id' => 'formCreateLeaveType');
echo form_open('leavetypes/create', $attributes); ?>
    <label for="name"><?php echo lang('leavetypes_popup_create_field_name');?></label>
    <input type="text" name="name" id="name" pattern=".{1,}" required />
    <label for="acronym"><?php echo lang('leavetypes_popup_create_field_acronym');?></label>
    <div class="input-append">
        <input type="text" name="acronym" id="acronym" />
        <a id="cmdSuggestAcronym" class="btn btn-primary" title="<?php echo lang('leavetypes_popup_create_button_suggest');?>">
            <i class="mdi mdi-auto-fix" aria-hidden="true"></i>
        </a>
    </div>
    <label for="deduct_days_off">
        <input type="checkbox" name="deduct_days_off" id="deduct_days_off" />
        <?php echo lang('leavetypes_popup_create_field_deduct');?>
    </label>

    <label for="nodeduction">
        <input type="checkbox" class="filterStatus" name="nodeduction" id="nodeduction" value="1"/>
        <?php echo lang('leavetype_nodeduction'); ?> <i class="mdi mdi-information-outline nolink" style="font-size:16px" title="<?php echo lang('leavetype_nodeduction_hint');?>"></i>
    </label>

    <label for="noapproval">
        <input type="checkbox" class="filterStatus" name="noapproval" id="noapproval" value="1"/>
        <?php echo lang('leavetype_noapproval'); ?> <i class="mdi mdi-information-outline nolink" style="font-size:16px" title="<?php echo lang('leavetype_noapproval_hint');?>"></i>
    </label>

    <label for="approvebyadmin">
        <input type="checkbox" class="filterStatus" name="approvebyadmin" id="approvebyadmin" value="1"/>
        <?php echo lang('leavetype_approve_by_admin'); ?> <i class="mdi mdi-information-outline nolink" style="font-size:16px" title="<?php echo lang('leavetype_approve_by_admin_hint');?>"></i>
    </label>

<label for="extrainput"><?php echo lang('leavetype_extrainput');?> <i class="mdi mdi-information-outline nolink" style="font-size:16px" title="<?php echo lang('leavetype_extrainput_hint');?>"></i></label>
<input id="extrainput" name="extrainput" value="" />

    <label for="limit"><?php echo lang('leavetype_limit');?> <i class="mdi mdi-information-outline nolink" style="font-size:16px" title="<?php echo lang('leavetype_limit_hint');?>"></i></label>
    <input id="spinner" name="limit" type="number" min="0" style="width: 60px;" value="">

    <label for="color"><?php echo lang('leavetype_color');?></label>
    <input data-jscolor="{}" id="color" name="color" value="" />

    <label for="color"><?php echo lang('leavetype_textcolor');?></label>
    <input data-jscolor="{}" id="textcolor" name="textcolor" value="" />

    <br />
</form>
<button id="cmdCreateLeaveType" class="btn btn-primary"><?php echo lang('leavetypes_popup_create_button_create');?></button>

<script type="text/javascript" src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<script type="text/javascript">
    $(function () {
        //Check if the leave type is unique
        $('#cmdCreateLeaveType').click(function() {
            var typeNames = [<?php echo implode(', ', array_map(function ($entry) { return '"' . $entry['name'] . '"'; }, $leavetypes)); ?>];
            if (typeNames.indexOf($('#name').val()) > -1) {
                bootbox.alert("<?php echo lang('leavetypes_js_unique_error_msg');?>");
            } else {
                $('#formCreateLeaveType').submit();
            }
        });
        
        //Suggest an acronym by using the first letters of the leave type name
        $('#cmdSuggestAcronym').click(function() {
            var toMatch = $('#name').val();
            var result = toMatch.replace(/(\w)\w*\W*/g, function (_, i) {
                return i.toUpperCase();
              });
            $('#acronym').val(result);
        });

        jscolor.install(); // init color picker
    });
</script>
