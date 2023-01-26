<?php
/**
 * This view allows an employees (or HR admin/Manager) to create a new leave request
 * @copyright  Copyright (c) 2014-2019 Benjamin BALET
 * @license      http://opensource.org/licenses/AGPL-3.0 AGPL-3.0
 * @link            https://github.com/bbalet/jorani
 * @since         0.1.0
 */
?>

<h2><?php echo lang('leaves_create_title');?> &nbsp;<?php echo $help; ?></h2>

<!-- tui calendar includes -->
<!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous"/>-->
<!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>-->

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>

<link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui.time-picker/latest/tui-time-picker.css">
<link rel="stylesheet" type="text/css" href="https://uicdn.toast.com/tui.date-picker/latest/tui-date-picker.css">
<link rel="stylesheet" href="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.css" />
<link rel="stylesheet" type="text/css" href="../assets/css/tui/default.css">
<link rel="stylesheet" type="text/css" href="../assets/css/tui/icons.css">

<script src="https://uicdn.toast.com/tui.code-snippet/v1.5.2/tui-code-snippet.min.js"></script>
<script src="https://uicdn.toast.com/tui.time-picker/v2.0.3/tui-time-picker.min.js"></script>
<script src="https://uicdn.toast.com/tui.date-picker/v4.0.3/tui-date-picker.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/chance/1.0.13/chance.min.js"></script>
<script src="https://uicdn.toast.com/calendar/latest/toastui-calendar.min.js"></script>

<div class="code-html">
    <div id="menu">
      <span id="menu-navi">
        <button type="button" class="btn btn-default btn-sm" data-action="move-today">Ma</button>
        <button type="button" class="btn btn-default btn-sm" data-action="move-prev">
          <i class="mdi mdi-arrow-left" data-action="move-prev"></i>
        </button>
        <button type="button" class="btn btn-default btn-sm" data-action="move-next">
          <i class="mdi mdi-arrow-right" data-action="move-next"></i>
        </button>
      </span>
        <span id="renderRange" class="render-range"></span>
    </div>

    <div id="calendar" style="height: 600px; width: 600px"></div>
</div>

<script type="text/javascript" class="code-js">

    const cals = [ <?php foreach ($typeDetails as $t){ if ($t['id'] == 0) continue; ?>
            {
                id: '<?=$t['id']?>',
                name: '<?=$t['name']?>',
                color: '#ffffff',
                borderColor: '<?=$t['color'] != '' ? $t['color'] : 'green'?>',
                backgroundColor: '<?=$t['color'] != '' ? $t['color'] : 'green'?>',
                dragBackgroundColor: '<?=$t['color'] != '' ? $t['color'] : 'green'?>',
            },
        <?php } ?>
    ];

    var cal = new tui.Calendar('#calendar', {
        defaultView: 'month',
        taskView: false,
        // isAllday: true,
        // scheduleView: ['allday'],  // Can be also ['allday', 'time']
/*        template: {
            milestone: function(schedule) {
                return '<span style="color:red;"><i class="fa fa-flag"></i> ' + schedule.title + '</span>';
            },
            milestoneTitle: function() {
                return 'Milestone';
            },
            task: function(schedule) {
                return '&nbsp;&nbsp;#' + schedule.title;
            },
            taskTitle: function() {
                return '<label><input type="checkbox" />Task</label>';
            },
            titlePlaceholder: function() {
                return 'Megjegyzés';
            },
            allday: function(schedule) {
                return schedule.title;// + ' <i class="fa fa-refresh"></i>';
            },
            time: function(schedule) {
                return schedule.title + ' <i class="fa fa-refresh"></i>' + moment(schedule.start.toDate()).format('YYYY.MM.DD hh:mm a');
            },
            popupIsAllday() {
                return 'Egész nap'; // All Day
            },
        },
*/        usageStatistics: false,
        useFormPopup: true,
        useDetailPopup: true,
        <!-- useCreationPopup: true,  -->
        <!-- useDetailPopup: true, -->
        <!-- useCreationPopup: false, -->
        <!-- useDetailPopup: false, -->
        week: {
         dayNames: ['Vas', 'Hét', 'Ked', 'Sze', 'Csü', 'Pén', 'Szo'],
        },
        // visibleEventCount: 6,
        month: {
            startDayOfWeek: 1,
            dayNames: ['Vas', 'Hét', 'Ked', 'Sze', 'Csü', 'Pén', 'Szo'],
            //narrowWeekend: true
            moreLayerSize: {
                height: 'auto'
            }, isAlways6Weeks: false,
        },
        calendars: cals,

        // timezone: {
            // zones: [
            // {
            //   timezoneName: this.dataset.jsCalendarTzinfoIdentifier, // this.dataset.jsCalendarTzinfoIdentifier = 'America/New_York'
            //   displayLabel: this.dataset.jsCalendarTzinfoIdentifier,
            //   tooltip: this.dataset.jsCalendarTzinfoIdentifier,
            // },
            // ],
        //     offsetCalculator: function (timezoneName, timestamp) {
        //         // matches 'getTimezoneOffset()' of Date API
        //         // e.g. +09:00 => -540, -04:00 => 240
        //         return new Date(timestamp).getTimezoneOffset(); //moment.tz.zone(timezoneName).utcOffset(timestamp);
        //     },
        // },
    });

    // function getLocalTimezoneOffset(timestamp) {
    //     return new Date(timestamp).getTimezoneOffset();
    // }
    // cal.Calendar.setTimezoneOffsetCallback(getLocalTimezoneOffset);

    cal.createEvents([<?php foreach ($events as $e){ ?>
        {
            id: '<?=$e['id']?>',
            calendarId: '<?=$e['type']?>',
            title: '<?=preg_replace('/[\n\r\']+/', ' ', $e['cause'])?>',
            category: 'allday', // time
            isAllday: true,
            isReadOnly: true,    // only planned can be modified, but it has no rest api
            dueDateClass: '',
            start: '<?=$e['startdate']?>',
            end: '<?=$e['enddate']?>',
            raw: <?=json_encode(['status' => (int)$e['status'], 'duration' => (float)$e['duration']])?>,
            <?php /* if ($e['status_name'] == 'Requested') { ?>
            customStyle: {
                background: "repeating-linear-gradient(45deg, transparent, transparent 10px, #ccc 10px, #ccc 20px)"
            }
            <?php } */ ?>
        },
        <?php } ?>
        // {
        //     id: '2',
        //     calendarId: '1',
        //     title: 'second schedule',
        //     category: 'time',
        //     dueDateClass: '',
        //     start: '2022-11-21T17:30:00+09:00',
        //     end: '2022-11-24T17:31:00+09:00'
        // }
    ]);

    cal.createEvents([<?php foreach ($allEvents as $u => $events){
        foreach($events as $e){
        ?>
        {
            id: '<?=$e['id']?>',
            calendarId: '<?=$e['type']?>',
            title: '<?=preg_replace('/[\n\r\']+/', ' ', $u)?>',
            category: 'allday', // time
            isAllday: true,
            isReadOnly: true,    // schedule is read-only
            dueDateClass: '',
            start: '<?=$e['startdate']?>',
            end: '<?=$e['enddate']?>',
            <?php /* if ($e['status_name'] == 'Requested') { ?>
            customStyle: {
                background: "repeating-linear-gradient(45deg, transparent, transparent 10px, #ccc 10px, #ccc 20px)"
            }
            <?php } */ ?>
        },
        <?php } } ?>
    ]);

    var baseURL = '<?php echo base_url();?>';
    cal.on('beforeCreateEvent', (e) => {
        // Calling the instance method when the instance event is invoked
        // console.log(e);

        // const myStart = moment($('input[name=start]').val(), 'YYYY-MM-DD').toDate();
        // e.start.setFullYear(myStart.getFullYear());
        // e.start.setMonth(myStart.getMonth());
        // e.start.setDate(myStart.getDate());
        // e.start.setHours(8);

        const a = isAllowed(e);
        if (!a.ok){
            alert('Ez nem fog menni, '+a.text+'.');
            return;
        }
        // csrf_test_jorani=3d338ba32d600883701b07478dded5d5&type=11&viz_startdate=11%2F23%2F2022&startdate=2022-11-23&startdatetype=Morning&
        // viz_enddate=11%2F24%2F2022&enddate=2022-11-24&enddatetype=Afternoon&duration=2&extrainput=&cause=bla+bla&status=2
        $.ajax({
            type: "POST",
            url: baseURL + "leaves/create",
            data:  {
                csrf_test_jorani: '<?php echo $_COOKIE[$this->config->item('csrf_cookie_name')];?>',
                type: e.calendarId,
                viz_startdate: moment(e.start.toDate()).format('MM/DD/YYYY'),
                startdate: moment(e.start.toDate()).format('YYYY-MM-DD'),
                startdatetype: 'Morning',
                viz_enddate: moment(e.end.toDate()).format('MM/DD/YYYY'),
                enddate: moment(e.end.toDate()).format('YYYY-MM-DD'),
                enddatetype: 'Afternoon',
                duration: moment(e.end.toDate()).diff(moment(e.start.toDate()), 'days')+1,
                extrainput:'',
                cause: e.title,
                status: 2
            }
        }).done(function (d) {
            console.log('lementettuk a joraniba');
        });


        cal.createEvents([
            {
                ...e,
                isAllday: true,
                isReadOnly: true,
                <!-- id: uuid(), -->
            },
        ]);
    });

    cal.on('beforeUpdateEvent', (eventObj) => {
        // Calling the instance method when the instance event is invoked
        console.log(eventObj);
        cal.updateEvent(eventObj.event.id, eventObj.event.calendarId, eventObj.changes);
    });


    cal.on('beforeDeleteEvent', (eventInfo) => {
        // Calling the instance method when the instance event is invoked
        console.log(eventInfo);
        //cal.deleteEvent(eventInfo.id, eventInfo.calendarId);
        http://localhost/jorani/leaves/cancel/867
            $.ajax({
                type: "GET",
                url: baseURL + "leaves/cancel/" + eventInfo.id,
            }).done(function (d) {
                console.log('cancel a joraniba');
            });

    });


    function getWeekNumber(d){
        const s = new Date(d.getFullYear(), 0, 1);
        var days = Math.floor((d - s) / (24 * 60 * 60 * 1000));
        return Math.ceil(days / 7);
    }

    // https://stackoverflow.com/questions/37069186/calculate-working-days-between-two-dates-in-javascript-excepts-holidays
    function getWeekDays(start, end){
        let _weekdays = [1,2,3,4,5];
        var c=0;
        var currentDate = start.toDate();
        while (currentDate <= end) {
            if ( _weekdays.includes(currentDate.getDay())){
                c++;
            }
            currentDate.setDate(currentDate.getDate() +1);
        }
        return c;
    }

    function isAllowed(ev){

        const weeklyLimits = {
            <?php foreach ($leaveLimits as $id => $limit){
                if ($limit){
                    echo "$id: $limit";
                }
            }
            ?>
        }
        const free = {"id": <?=$defaultType?>, <?=isset($credit) ? '"days" : ' . $credit : ''?>}

        const currWeek = getWeekNumber(ev.start);
        const currDuration = ev.start < ev.end ? getWeekDays(ev.start, ev.end) : getWeekDays(ev.end, ev.start);
        var c = {};

        if (ev.calendarId == free.id && currDuration > free.days)
            return {"ok" : false, "text": "nincs ennyi szabadságod"};

        const limit = weeklyLimits[ev.calendarId];
        if (limit === undefined)
            return {"ok" : true, "text": ""}; // ennek nincs heti korlatozasa

        for(k of cal.getStoreState().calendar.events.internalMap.values()){
//            console.log(k);
            if (k.raw === undefined || k.raw == null) continue; // other user
            if (k.calendarId == ev.calendarId && k.raw.status < 4){
                const duration = k.raw.duration; //k.start < k.end ? getWeekDays(k.start, k.end) : getWeekDays(k.end, k.start); // weekend?
                const week = getWeekNumber(k.start);
                if (week == currWeek) {
                    if (c[week] == undefined) c[week] = 0;
                    c[week] += duration;
                    if (c[currWeek] + currDuration > limit)
                        return {"ok": false, "text": "átlépnéd a heti korlátot"}; // todo: a tobbinek irodai napnak kell lennie (pl 3 szabi + 2 ho nem mehet)
                }
            }
        }
        return {"ok" : true, "text": ""};
    }

    $(function(){
        modifyScheduleCreationPopup();
        $('#menu-navi').on('click', onClickNavi);
        setRenderRangeText();
    });
    /**
     * @description will modify the creation popup by watch for parent dom element and on the moment that children are
     *              added it will attempt to find the popup elements and modify them
     */
    function modifyScheduleCreationPopup(){

        const prefix = '.toastui-calendar-'; // '.tui-full-calendar-'
        let parentWrapperObserver = new MutationObserver( (mutations) => {
            // console.log('most fogom');

            //$(prefix + 'section-allday,'+prefix + 'section-state').addClass('d-none').css('height', '0px');
            //$(prefix + 'section-location').parent().addClass('d-none').css('display', 'none');

            $('.toastui-calendar-popup-section-allday').find('input[name=isAllday]').click();
            // $(prefix + 'popup-section-allday').find('input[name=isAllday]').prop('checked', true);
            //
            $(prefix + 'state-section,' + prefix+'popup-section-allday,' +prefix + 'popup-section-private ').css('display', 'none'); // prefix + 'popup-section-allday,'+
            $(prefix + 'popup-section-location').parent().css('display', 'none');


            const saveButton = $(prefix + 'popup-confirm');
            const updateDlg = saveButton.find(prefix + 'template-popupUpdate').length == 1;
            if (updateDlg)
                return;

            const masik = saveButton.clone().appendTo(saveButton.parent()).attr('title', 'a beállított értékekkel átugrik a hagyományos felületre').attr('id', 'indirectSaveId');
            saveButton.find(prefix + 'template-popupSave').html('Request now').attr('title', 'menti az igényelt kimenőt (és kihagyjuk a hagyományos felületet)');

            masik.off();
            masik.on('click', function(ev) {
                ev.preventDefault();
                ev.stopImmediatePropagation();
                // ev.stopPropagation();
                let t = 1, typeText = $('.toastui-calendar-event-calendar').text();
                for(i=0; i<cals.length; i++){
                    if (cals[i].name == typeText){
                        t = cals[i].id;
                        break;
                    }
                }
                window.location.href = window.location.href.replace('createEx', 'create?s=' + $('input[name=start]').val() + '&e=' + $('input[name=end]').val() + '&t=' + t + '&c=' + encodeURIComponent($('input[name=title]').val()));
            });

			// $('.tui-full-calendar-p').parent().addClass('d-none').css('display', 'none');//.parent().parent().parent().outerHeight(100); -->
        })

        let htmlDomElement = document.querySelectorAll(prefix + 'event-form-popup-slot');//'floating-layer'); // toastui-calendar-floating-layer
        htmlDomElement.forEach(function(i){
            parentWrapperObserver.observe(i, {childList: true});
        });

        // console.log('jovok' + htmlDomElement);
    }

    function getDataAction(target) {
        return target.dataset ? target.dataset.action : target.getAttribute('data-action');
    }

    function onClickNavi(e) {
        var action = getDataAction(e.target);

        switch (action) {
            case 'move-prev':
                cal.prev();
                break;
            case 'move-next':
                cal.next();
                break;
            case 'move-today':
                cal.today();
                break;
            default:
                return;
        }

        setRenderRangeText();
        // setSchedules();
    }

    function setRenderRangeText() {
        var renderRange = document.getElementById('renderRange');
        var options = cal.getOptions();
        var viewName = cal.getViewName();
        var html = [];
        if (viewName === 'day') {
            html.push(moment(cal.getDate().getTime()).format('YYYY.MM.DD'));
        } else if (viewName === 'month' &&
            (!options.month.visibleWeeksCount || options.month.visibleWeeksCount > 4)) {
            html.push(moment(cal.getDate().getTime()).format('YYYY.MM'));
        } else {
            html.push(moment(cal.getDateRangeStart().getTime()).format('YYYY.MM.DD'));
            html.push(' ~ ');
            html.push(moment(cal.getDateRangeEnd().getTime()).format(' MM.DD'));
        }
        renderRange.innerHTML = html.join('');
    }

</script>


<div class="row-fluid">
    <div class="span8">

<?php echo validation_errors(); ?>

<?php

/*
// var_dump($typesWithExtraInput);
//var_dump($typeDetails);
$attributes = array('id' => 'frmLeaveForm');
echo form_open('leaves/create', $attributes) ?>

    <label for="type">
        <?php echo lang('leaves_create_field_type');?>
        &nbsp;<span class="muted" id="lblCredit" title="<?php echo lang('leaves_view_field_duration'); ?>"><?php if (!is_null($credit)) { ?>(<?php echo $credit; ?>)<?php } ?></span>
    </label>
    <select class="input-xxlarge" name="type" id="type">
    <?php foreach ($types as $typeId => $TypeName): ?>
        <option value="<?php echo $typeId; ?>" <?php if ($typeId == $defaultType) echo "selected"; ?>><?php echo $TypeName; ?></option>
    <?php endforeach ?>
    </select>

    <label for="viz_startdate"><?php echo lang('leaves_create_field_start');?></label>
    <input type="text" name="viz_startdate" id="viz_startdate" value="<?php echo set_value('startdate'); ?>" autocomplete="off" />
    <input type="hidden" name="startdate" id="startdate" />
    <select name="startdatetype" id="startdatetype">
        <option value="Morning" selected><?php echo lang('Morning');?></option>
        <option value="Afternoon"><?php echo lang('Afternoon');?></option>
    </select><br />

    <label for="viz_enddate"><?php echo lang('leaves_create_field_end');?></label>
    <input type="text" name="viz_enddate" id="viz_enddate" value="<?php echo set_value('enddate'); ?>" autocomplete="off" />
    <input type="hidden" name="enddate" id="enddate" />
    <select name="enddatetype" id="enddatetype">
        <option value="Morning"><?php echo lang('Morning');?></option>
        <option value="Afternoon" selected><?php echo lang('Afternoon');?></option>
    </select><br />

    <label for="duration"><?php echo lang('leaves_create_field_duration');?> <span id="tooltipDayOff"></span></label>
    <?php if ($this->config->item('disable_edit_leave_duration') == TRUE) { ?>
    <input type="text" name="duration" id="duration" value="<?php echo set_value('duration'); ?>" readonly />
    <?php } else { ?>
    <input type="text" name="duration" id="duration" value="<?php echo set_value('duration'); ?>" />
    <?php } ?>

    <span style="margin-left: 2px;position: relative;top: -5px;" id="spnDayType"></span>

    <?php if (true) { ?>
        <label for="extrainput" id="extrainputLabel"><?php echo $typesWithExtraInput['extrainput'];?></label>
        <input type="text" name="extrainput" id="extrainput" value="<?php echo set_value('extrainput'); ?>" />
    <?php }  ?>

    <div class="alert hide alert-error" id="lblCreditAlert" onclick="$('#lblCreditAlert').hide();">
        <button type="button" class="close">&times;</button>
        <?php echo lang('leaves_create_field_duration_message');?>
    </div>

    <div class="alert hide alert-error" id="lblOverlappingAlert" onclick="$('#lblOverlappingAlert').hide();">
        <button type="button" class="close">&times;</button>
        <?php echo lang('leaves_create_field_overlapping_message');?>
    </div>

    <div class="alert hide alert-error" id="lblOverlappingDayOffAlert" onclick="$('#lblOverlappingDayOffAlert').hide();">
        <button type="button" class="close">&times;</button>
        <?php echo lang('leaves_flash_msg_overlap_dayoff');?>
    </div>

    <label for="cause"><?php echo lang('leaves_create_field_cause');?></label>
    <textarea name="cause"><?php echo set_value('cause'); ?></textarea>

    <br/><br/>
    <button name="status" value="1" type="submit" class="btn btn-primary"><i class="mdi mdi-calendar-question" aria-hidden="true"></i>&nbsp; <?php echo lang('Planned');?></button>
    &nbsp;&nbsp;
    <button name="status" value="2" type="submit" class="btn btn-primary "><i class="mdi mdi-check"></i>&nbsp; <?php echo lang('Requested');?></button>
    <br/><br/>
    <a href="<?php echo base_url(); ?>leaves" class="btn btn-danger"><i class="mdi mdi-close"></i>&nbsp; <?php echo lang('leaves_create_button_cancel');?></a>
</form>

    </div>
</div>

<div class="modal hide" id="frmModalAjaxWait" data-backdrop="static" data-keyboard="false">
    <div class="modal-header">
        <h1><?php echo lang('global_msg_wait');?></h1>
    </div>
    <div class="modal-body">
        <img src="<?php echo base_url();?>assets/images/loading.gif"  align="middle">
    </div>
 </div>

<link rel="stylesheet" href="<?php echo base_url();?>assets/css/flick/jquery-ui.custom.min.css">
<script src="<?php echo base_url();?>assets/js/jquery-ui.custom.min.js"></script>
<?php //Prevent HTTP-404 when localization isn't needed
if ($language_code != 'en') { ?>
<script src="<?php echo base_url();?>assets/js/i18n/jquery.ui.datepicker-<?php echo $language_code;?>.js"></script>
<?php } ?>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/moment-with-locales.min.js" type="text/javascript"></script>
<script src="<?php echo base_url();?>assets/js/bootbox.min.js"></script>
<link rel="stylesheet" href="<?php echo base_url();?>assets/select2-4.0.5/css/select2.min.css">
<script src="<?php echo base_url();?>assets/select2-4.0.5/js/select2.full.min.js"></script>

<?php require_once dirname(BASEPATH) . "/local/triggers/leave_view.php"; ?>
<script>
$(document).on("click", "#showNoneWorkedDay", function(e) {
  showListDayOffHTML();
});
</script>
<script type="text/javascript">
    var baseURL = '<?php echo base_url();?>';
    var userId = <?php echo $user_id; ?>;
    var leaveId = null;
    var languageCode = '<?php echo $language_code;?>';
    var dateJsFormat = '<?php echo lang('global_date_js_format');?>';
    var dateMomentJsFormat = '<?php echo lang('global_date_momentjs_format');?>';

    var noContractMsg = "<?php echo lang('leaves_validate_flash_msg_no_contract');?>";
    var noTwoPeriodsMsg = "<?php echo lang('leaves_validate_flash_msg_overlap_period');?>";

    var overlappingWithDayOff = "<?php echo lang('leaves_flash_msg_overlap_dayoff');?>";
    var listOfDaysOffTitle = "<?php echo lang('leaves_flash_spn_list_days_off');?>";

function validate_form() {
    var fieldname = "";

    //Call custom trigger defined into local/triggers/leave.js
    if (typeof triggerValidateCreateForm == 'function') {
       if (triggerValidateCreateForm() == false) return false;
    }

    if ($('#viz_startdate').val() == "") fieldname = "<?php echo lang('leaves_create_field_start');?>";
    if ($('#viz_enddate').val() == "") fieldname = "<?php echo lang('leaves_create_field_end');?>";
    if ($('#duration').val() == "" || $('#duration').val() == 0) fieldname = "<?php echo lang('leaves_create_field_duration');?>";
    if (fieldname == "") {
        return true;
    } else {
        bootbox.alert(<?php echo lang('leaves_validate_mandatory_js_msg');?>);
        return false;
    }
}

//Disallow the use of negative symbols (through a whitelist of symbols)
function keyAllowed(key) {
  var keys = [8, 9, 13, 16, 17, 18, 19, 20, 27, 46, 48, 49, 50,
    51, 52, 53, 54, 55, 56, 57, 91, 92, 93
  ];
  if (key && keys.indexOf(key) === -1)
    return false;
  else
    return true;
}

$(function () {
    //Selectize the leave type combo
    $('#type').select2().change(function () {
        const id = $(this).find("option:selected").val();
        toggleExtraInput(id);
    }).change(); // esteve

<?php if ($this->config->item('disallow_requests_without_credit') == TRUE) {?>
    var durationField = document.getElementById("duration");
    durationField.setAttribute("min", "0");
    durationField.addEventListener('keypress', function(e) {
        var key = !isNaN(e.charCode) ? e.charCode : e.keyCode;
        if (!keyAllowed(key))
        e.preventDefault();
    }, false);

    // Disable pasting of non-numbers
    durationField.addEventListener('paste', function(e) {
        var pasteData = e.clipboardData.getData('text/plain');
        if (pasteData.match(/[^0-9]/))
        e.preventDefault();
    }, false);
<?php }?>
});

<?php if ($this->config->item('csrf_protection') == TRUE) {?>
$(function () {
    $.ajaxSetup({
        data: {
            <?php echo $this->security->get_csrf_token_name();?>: "<?php echo $this->security->get_csrf_hash();?>",
        }
    });
});
<?php }?>

    function toggleExtraInput(selectedTypeId){
        if (selectedTypeId == <?=$typesWithExtraInput['id']?>){
            $('#extrainputLabel,#extrainput').show();
            $('#extrainputLabel').attr('required', 'required'); // if it is visible, it is mandatory
        }else
            $('#extrainputLabel,#extrainput').hide();
    }
</script>
<script type="text/javascript" src="<?php echo base_url();?>assets/js/lms/leave.edit-0.7.0.js" type="text/javascript"></script>
*/
