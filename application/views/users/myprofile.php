<?php
/**
 * This view displays the profile (basic information) of the connected user.
 * If ICS feed is activated, a link allows the user to import non-working days into a remote calendar application.
 * @copyright  Copyright (c) 2014-2023 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.3.0
 */
?>

<div class="row-fluid">
    <div class="span6">
        <h2><?php echo lang('users_myprofile_title');?></h2>
        <div class="row-fluid">
            <div class="span6"><strong><?php echo lang('users_myprofile_field_firstname');?></strong></div>
            <div class="span6"><?php echo $user['firstname'];?></div>
        </div>

        <div class="row-fluid">
            <div class="span6"><strong><?php echo lang('users_myprofile_field_lastname');?></strong></div>
            <div class="span6"><?php echo $user['lastname'];?></div>
        </div>

        <div class="row-fluid">
            <div class="span6"><strong><?php echo lang('users_myprofile_field_manager');?></strong></div>
            <div class="span6"><?php echo $manager_label;?></div>
        </div>

        <div class="row-fluid">
            <div class="span6"><strong><?php echo lang('users_myprofile_field_contract');?></strong></div>
            <div class="span6"><?php echo $contract_label;?>
            <?php if (($this->config->item('ics_enabled') == TRUE) && ($contract_id != 0)) {?>
            &nbsp;(<a id="lnkICS" href="#"><i class="mdi mdi-earth nolink"></i> ICS</a>)
            <?php } ?>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6"><strong><?php echo lang('users_myprofile_field_position');?></strong></div>
            <div class="span6"><?php echo $position_label;?></div>
        </div>

        <div class="row-fluid">
            <div class="span6"><strong><?php echo lang('users_myprofile_field_entity');?></strong></div>
            <div class="span6"><?php echo $organization_label;?></div>
        </div>

        <div class="row-fluid">
            <div class="span6"><strong><?php echo lang('users_myprofile_field_hired');?></strong></div>
            <div class="span6"><?php
        if (!is_null($user['datehired'])) {
            $date = new DateTime($user['datehired']);
            echo $date->format(lang('global_date_format'));            
        }
        ?></div>
        </div>

        <div class="row-fluid">
            <div class="span6"><strong><?php echo lang('users_myprofile_field_identifier');?></strong></div>
            <div class="span6"><?php echo $user['identifier'];?></div>
        </div>

        <div class="row-fluid">
            <div class="span6"><strong><?php echo lang('users_myprofile_field_language');?></strong></div>
            <div class="span6">
                <?php $languages = $this->polyglot->nativelanguages($this->config->item('languages'));?>
                <input type="hidden" name="last_page" value="session/failure" />
                <?php if (count($languages) == 1) { ?>
                <input type="hidden" name="language" value="<?php echo $language_code; ?>" />
                <?php } else { ?>
                <select class="input-medium" name="language" id="language">
                    <?php foreach ($languages as $lang_code => $lang_name) { ?>
                    <option value="<?php echo $lang_code; ?>" <?php if ($language_code == $lang_code) echo 'selected'; ?>><?php echo $lang_name; ?></option>
                    <?php }?>
                </select>
                <?php } ?>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span6"><strong><?php echo lang('users_email_report');?></strong></div>
            <div class="span6"><a href="#" onclick="$('#frmSelectEmailReport').modal('show');"><?=$email_report_label?></a>
            </div>
        </div>
    </div>
    <div class="span6">
        <h2>Apps</h2>
        <?php foreach ($apps as $app): ?>
        <div class="row-fluid">
            <div class="span12">
            <a href="<?php echo $app['redirect_uri']; ?>" target="_blank">
               <img width="50" src="<?php echo $app['icon_path']; ?>"></a>
            &nbsp;<?php echo $app['client_id']; ?>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>

<div class="row-fluid"><div class="span12">&nbsp;</div></div>

<div id="frmLinkICS" class="modal hide fade">
    <div class="modal-header">
        <h3>ICS<a href="#" onclick="$('#frmLinkICS').modal('hide');" class="close">&times;</a></h3>
    </div>
    <div class="modal-body" id="frmSelectDelegateBody">
        <div class='input-append'>
            <?php $icsUrl = base_url() . 'ics/dayoffs/' . $user_id . '/' . $contract_id . '?token=' . $this->session->userdata('random_hash');?>
            <input type="text" class="input-xlarge" id="txtIcsUrl" onfocus="this.select();" onmouseup="return false;"
                value="<?php echo $icsUrl;?>" />
                <button id="cmdCopy" class="btn" data-clipboard-text="<?php echo $icsUrl;?>">
                    <i class="mdi mdi-content-copy"></i>
                </button>
            <a href="#" id="tipCopied" data-toggle="tooltip" title="<?php echo lang('copied');?>" data-placement="right" data-container="#cmdCopy"></a>
        </div>
    </div>
    <div class="modal-footer">
        <a href="#" onclick="$('#frmLinkICS').modal('hide');" class="btn btn-primary"><?php echo lang('OK');?></a>
    </div>
</div>

<div id="frmSelectEntity" class="modal hide fade" style="z-index:1051">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="close">&times;</a>
        <h3><?php echo lang('calendar_organization_popup_entity_title');?></h3>
    </div>
    <div class="modal-body" id="frmSelectEntityBody">
        <img src="<?php echo base_url();?>assets/images/loading.gif">
    </div>
    <div class="modal-footer">
        <a href="#" onclick="select_entity();" class="btn btn-primary"><?php echo lang('calendar_organization_popup_entity_button_ok');?></a>
        <a href="#" onclick="$('#frmSelectEntity').modal('hide');" class="btn"><?php echo lang('calendar_organization_popup_entity_button_cancel');?></a>
    </div>
</div>



<?php echo form_open('users/editLimited/' . $user['id'], ['id' => 'reportForm']); ?>
<div id="frmSelectEmailReport" class="modal hide fade">
    <div class="modal-header">
        <a href="#" onclick="$('#frmSelectEmailReport').modal('hide');" class="close">&times;</a>
        <h3><?php echo lang('users_email_report');?></h3>
    </div>
    <div class="modal-body">


        <div class="row-fluid">
            <div class="span2"><?php echo lang('users_email_report_level')?></div>
            <div class="">
                <div class="input-prepend input-append">
                    <span class="add-on" id="spnAddOn"><i class="mdi mdi-sitemap"></i></span>
                    <input type="text" id="txtEntity" name="email_report_levelname" value="<?=$email_report_level_label?>" readonly />
                    <input type="hidden" id="email_report_level" name="email_report_level" value="<?=$email_report_level?>" readonly />
                    <button id="cmdSelectEntity" class="btn btn-primary" title="<?php echo lang('calendar_organization_button_select_entity');?>"><i class="mdi mdi-sitemap" aria-hidden="true"></i></button>
                </div>
            </div>
        </div>

        <div class="row-fluid">
            <div class="span2"><?php echo lang('users_email_report_frequency');?></div>
            <div class=""><select name="email_report_freq" id="freq">
                    <option value="weekly" <?=$email_report_freq !== 'daily' ? 'selected' : ''?>><?php echo lang('users_email_report_weekly');?></option>
                    <option value="daily" <?=$email_report_freq === 'daily' ? 'selected' : ''?>><?php echo lang('users_email_report_daily');?></option>
                </select>
            </div>
        </div>


    </div>
    <div class="modal-footer">
        <input type="hidden" id="del" name="del" value="0"/>
        <button type="submit" onclick="$('#del').val(1); return true;" class="btn btn-danger pull-left"><?php echo lang('extra_index_thead_tip_delete');?></button>
        <button type="submit" class="btn btn-primary"><?php echo lang('users_edit_popup_position_button_ok');?></button>
        <a href="#" onclick="$('#frmSelectEmailReport').modal('hide');" class="btn"><?php echo lang('users_edit_popup_position_button_cancel');?></a>
    </div>
</div>
</form>



<script type="text/javascript">
$(function() {
    //Copy/Paste ICS Feed
    var client = new ClipboardJS("#cmdCopy");
    $('#lnkICS').click(function () {
        $("#frmLinkICS").modal('show');
    });
    client.on( "success", function() {
        $('#tipCopied').tooltip('show');
        setTimeout(function() {$('#tipCopied').tooltip('hide')}, 1000);
    });

    //Refresh page language
    $('#language').select2();
    $('#language').on('select2:select', function (e) {
      var value = e.params.data.id;
      Cookies.set('language', value, { expires: 90, path: '/'});
      window.location.href = '<?php echo base_url();?>session/language?language=' + value;
    });


    //Popup select entity
    $("#cmdSelectEntity").click(function() {
        $("#frmSelectEntity").modal('show');
        $("#frmSelectEntityBody").load('<?php echo base_url(); ?>organization/select');
        return false; // not to submit
    });
    //Popup select list
    $("#cmdSelectList").click(function() {
        $("#frmSelectList").modal('show');
        $("#frmSelectListBody").load('<?php echo base_url(); ?>organization/lists');
    });
});

function select_entity() {
    const entity = $('#organization').jstree('get_selected')[0];
    const entityName = $('#organization').jstree().get_text(entity);
    $('#spnAddOn').html('<i class="mdi mdi-sitemap" aria-hidden="true"></i>');
    $('#txtEntity').val(entityName);
    $('#email_report_level').val(entity);
    $("#frmSelectEntity").modal('hide');
}
</script>
