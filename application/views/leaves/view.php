<?php
/**
 * This view allows users to view a leave request in read-only mode
 * @copyright  Copyright (c) 2014-2023 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>
<h2><?php echo lang('leaves_view_title');?><?php echo $leave['id']; if ($name != "") {?>&nbsp;<span class="muted">(<?php echo $name; ?>)</span><?php } ?></h2>

<div class="row">
  <div class="span6">

<div class="row-fluid">
    <div class="span12">

    <label for="startdate"><?php echo lang('leaves_view_field_start');?></label>
    <input type="text" name="startdate" value="<?php $date = new DateTime($leave['startdate']); echo $date->format(lang('global_date_format'));?>" readonly />
    <select name="startdatetype" readonly>
        <option selected><?php echo lang($leave['startdatetype']); ?></option>
    </select><br />

    <label for="enddate"><?php echo lang('leaves_view_field_end');?></label>
    <input type="text" name="enddate"  value="<?php $date = new DateTime($leave['enddate']); echo $date->format(lang('global_date_format'));?>" readonly />
    <select name="enddatetype" readonly>
        <option selected><?php echo lang($leave['enddatetype']); ?></option>
    </select><br />

    <label for="duration"><?php echo lang('leaves_view_field_duration');?></label>
    <input type="text" name="duration"  value="<?php echo $leave['duration']; ?>" readonly />

    <label for="type"><?php echo lang('leaves_view_field_type');?></label>
    <select name="type" readonly>
        <option selected><?php echo $leave['type_name']; ?></option>
    </select><br />

    <label for="cause"><?php echo lang('leaves_view_field_cause');?></label>
    <textarea name="cause" readonly><?php echo $leave['cause']; ?></textarea>

<?php $style= "dropdown-rejected";
switch ($leave['status']) {
    case LMS_PLANNED: $style= "dropdown-planned"; break;
    case LMS_REQUESTED: $style= "dropdown-requested"; break;
    case LMS_ACCEPTED: $style= "dropdown-accepted"; break;
    default: $style= "dropdown-rejected"; break;
} ?>
    <label for="status"><?php echo lang('leaves_view_field_status');?></label>
    <select name="status" class="<?php echo $style; ?>" readonly>
        <option selected><?php echo lang($leave['status_name']); ?></option>
    </select><br />
    <?php if($leave['status'] == LMS_PLANNED){ ?>
      <a href="<?php echo base_url();?>leaves/request/<?php echo $leave['id'] ?>/" class="btn btn-primary "><i class="mdi mdi-check"></i>&nbsp;<?php echo lang('Requested');?></a>
      <br/><br/>
    <?php } ?>
    <?php if ($leave['status'] == LMS_ACCEPTED) { ?>
      <a href="<?php echo base_url();?>leaves/cancellation/<?php echo $leave['id'] ?>" class="btn btn-primary"><i class="mdi mdi-undo"></i>&nbsp;<?php echo lang('Cancellation');?></a>
      <br/><br/>
    <?php } ?>
    <?php if ($leave['status'] == LMS_REQUESTED) { ?>
      <a href="<?php echo base_url();?>leaves/reminder/<?php echo $leave['id']; ?>" title="<?php echo lang('leaves_button_send_reminder');?>" class="btn btn-primary"><i class="mdi mdi-email"></i>&nbsp;<?php echo lang('leaves_button_send_reminder');?></a>
      <br/><br/>
    <?php } ?>

    <?php if (isset($homeOfficeLimit)) echo '<div class="alert alert-danger" role="alert">' . $homeOfficeLimit . '</div>'; ?>
    <?php if (isset($concurrentUserOverlapped)) echo '<div class="alert alert-danger" role="alert">' . $concurrentUserOverlapped . '</div>'; ?>

    <?php if (($leave['status'] == LMS_PLANNED) || $is_hr || $is_manager) { ?>
    <a href="<?php echo base_url();?>leaves/edit/<?php echo $leave['id'] ?>" class="btn btn-primary"><i class="mdi mdi-pencil"></i>&nbsp;<?php echo lang('leaves_view_button_edit');?></a>
    &nbsp;
    <?php } ?>
    <?php if (($leave['status'] == LMS_REQUESTED) && ($is_hr || $is_manager)) { ?>
        <a href="<?php echo base_url();?>requests/accept/<?php echo $leave['id'] ?>" class="btn btn-primary" <?php if (isset($homeOfficeLimit)) echo "onclick=\"return confirm('".str_replace("<br/>", "\\n", $homeOfficeLimit)."');\"";?>><i class="mdi mdi-check"></i>&nbsp;<?php echo lang('requests_index_thead_tip_accept');?></a>&nbsp;
    <?php } ?>
    <a href="<?php echo base_url() . $source; ?>" class="btn btn-primary"><i class="mdi mdi-arrow-left-bold"></i>&nbsp;<?php echo lang('leaves_view_button_back_list');?></a>

    </div>
</div>
</div>
<div class="span6">

    <style>
        .resizer { display:flex; margin:0; padding:0; resize:both; overflow:hidden }
        .resizer > .resized { flex-grow:1; margin:0; padding:0; border:0; min-width: 100%; height: 100%; min-height: 400px }
        .ugly { border:1px solid gray; }
        .highlight {background-color: yellow}
        .requestedCell,.requestedCellFirst,.requestedCellEnd { border-top: 3px solid yellow !important; border-bottom: 3px solid yellow !important;}
        .requestedCellFirst {border-left: 3px solid yellow !important}
        .requestedCellEnd {border-right: 3px solid yellow !important}
    </style>
    <div class="resizer ugly span12 hide" >
        <iframe id="tabFrameId" src="" class="resized"></iframe>
    </div>

    <?php $this->lang->load('calendar', $language); // for tabular calendar caption :)
//    $om = $this->load->model('organization_model' );
//    $o = $om->getDepartment($leave['employee']); // $this->organization_model->getDepartment($leave['employee']);
//    $o = $this->organ->getDepartment($leave['employee']);
//    $o = $om->organization_model->getDepartment($leave['employee']);
    $department = $this->organization_model->getDepartment($leave['employee']);
    $employeeName = $this->users_model->getName($leave['employee']);

//$o = userdepartment($leave['employee']);
    $showDepartmentTabularCalendar = $this->session->userdata['is_manager'] || $this->session->userdata['is_hr'];
//    var_dump($this->organization_model->getDepartment(2));

    if ($showDepartmentTabularCalendar){
    ?>
    <div id="onlyTabId"></div>
    <button type="button" class="btn btn-primary" onclick="toggleTabularClick(this)"><i class="mdi mdi-toggle-switch"></i> <?php echo lang('calendar_tabular_title');?></button>

    <script>
        var startD = new Date('<?=$leave['startdate']?>');
        var entity = <?=$department->id?>; // Id of the employee's organization entity
        var month = startD.getMonth(); //Monent.js uses 0 based numbers!
        var year = startD.getFullYear();
        var children = '1';
        var displayTypes = '1';
        var statuses = "?statuses=1|2|3|5";
        var first = 1;
        var url = '<?php echo base_url();?>calendar/tabular/partial/' +
            entity + '/' + (month + 1) + '/' + year + '/' + children + '/' +
            displayTypes + statuses;
        $(function(){
            // $("#onlyTabId").load(url, function(response, status, xhr) {
        $.get(url, null, function (response) {

                response = response.replace(/<td>(<?=$employeeName?>)/ig, '<td class="highlight">$1');
                response = response.replace(/(<td[^>]+class=["'][^'"]*)(["'])([^>]* data-id=["'](?:\d+;)*<?=$leave['id']?>(?:;?\d+)*["'])/ig, "$1 requestedCell$2$3");
                const onlyOneDay = (<?=$leave['duration']?> <= 1);
                response = response.replace(/requestedCell/, onlyOneDay ? "requestedCellFirst requestedCellEnd" : "requestedCellFirst");
                if (!onlyOneDay) {
                    const p = response.lastIndexOf('requestedCell');
                    if (p > 0) {
                        const l = 13;
                        response = response.substring(0, p) + 'requestedCellEnd' + response.substring(p+l);
                    }
                }
            // console.log(response);
            $('#onlyTabId').html(response);
            });
            // http://localhost/jorani/calendar/tabular/partial/2/12/2022/1/1
        });

        function toggleTabularClick(b){
            $('#onlyTabId').toggle();
            const a = $('#tabFrameId');
            if (first)
                a.attr('src', url.replace('partial/', ''));
            a.parent().toggle();
            const icon = $(b).find('i');
            if (icon.hasClass('mdi-toggle-switch-off'))
                icon.removeClass('mdi-toggle-switch-off').addClass('mdi-toggle-switch');
            else
                icon.removeClass('mdi-toggle-switch').addClass('mdi-toggle-switch-off');
        }
    </script>
<?php } ?>

  <h4><?php echo lang('leaves_comment_title');?></h4>
  <?php
  if(isset($leave["comments"])){

    echo "<div class='accordion' id='accordion'>";
    $i=1;
    foreach ($leave["comments"]->comments as $comments_item) {
      $date=new DateTime($comments_item->date);
      $dateFormat=$date->format(lang('global_date_format'));

      if($comments_item->type == "comment"){
        echo "<div class='accordion-group'>";
        echo "  <div class='accordion-heading'>";
        echo "    <a class='accordion-toggle' data-toggle='collapse' data-parent='#accordion' href='#collapse$i'>";
        echo "      $dateFormat : $comments_item->author" . lang('leaves_comment_author_saying');
        echo "    </a>";
        echo "  </div>";
        echo "  <div id='collapse$i' class=\"accordion-body collapse $comments_item->in\">";
        echo "    <div class='accordion-inner'>";
        echo "      $comments_item->value";
        echo "    </div>";
        echo "  </div>";
        echo "</div>";
      }else if ($comments_item->type == "change"){
        echo "<div class='accordion-group'>";
        echo "  <div class='accordion-heading'>";
        echo "    <h6 class='accordion-toggle' data-toggle='collapse' data-parent='#accordion'>";
        echo "      $dateFormat : " . lang('leaves_comment_status_changed');
        switch ($comments_item->status_number) {
          case 1: echo "<span class='label'>" . lang($comments_item->status) . "</span>"; break;
          case 2: echo "<span class='label label-warning'>" . lang($comments_item->status) . "</span>"; break;
          case 3: echo "<span class='label label-success'>" . lang($comments_item->status) . "</span>"; break;
          default: echo "<span class='label label-important' style='background-color: #ff0000;'>" . lang($comments_item->status) . "</span>"; break;
        }
        echo "    </h6>";
        echo "  </div>";
        echo "</div>";
      }
      $i++;
    }
    echo " </div>";
  }
   ?>
   <?php
   $attributes = array('id' => 'frmLeaveNewCommentForm');
   if (isset($_GET['source'])) {
       echo form_open('leaves/' . $leave['id'] . '/comments/add?source=' . $_GET['source'], $attributes);
   } else {
       echo form_open('leaves/' . $leave['id'] . '/comments/add', $attributes);
   }
   ?>
   <form method="post"
   <label for="comment"><?php echo lang('leaves_comment_new_comment');?></label>
   <textarea name="comment" class="form-control" rows="5" style="min-width: 100%"></textarea>
   <button type="submit" class="btn btn-primary"><i class="mdi mdi-comment-plus-outline"></i>&nbsp;<?php echo lang('leaves_comment_send_comment');?></button>
   &nbsp;
 </form>
</div>
</div>
