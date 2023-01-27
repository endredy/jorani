<h2><?php echo $title;?></h2>

<?php
$attributes = array('id' => 'frmLeaveForm');
echo form_open('leaves/createM', $attributes) ?>


    <select class="input-xxlarge" name="type" id="type">
        <?php foreach ($types as $typeId => $TypeName): ?>
            <option value="<?php echo $typeId; ?>" <?php if ($typeId == $defaultType) echo "selected"; ?>><?php echo $TypeName; ?></option>
        <?php endforeach ?>
    </select>

    <table>
        <tr>
            <td></td>
            <td>
                <?php echo lang('calendar_tabular_field_month');?>
                <br/>
                <select name="month" id="month" multiple size="12" style="width:100px">
                <?php for($i=0; $i<12; $i++){ ?>}
                    <option value="<?=$i?>"><?=$i+1?></option>
                <?php } ?>
                </select>
            </td>
            <td>
                <br/>
                <select name="day" id="day" multiple size="<?=count($days)?>" style="width:100px">
                    <?php for($i=0; $i<count($days); $i++){ ?>}
                        <option value="<?=$i?>"><?=$days[$i]?></option>
                    <?php } ?>
                </select>
            </td>
            <td style="margin-left: 100px">
                selected dates:
                <div id="datesId" style="overflow-y: scroll; width: 200px; height: 100px; font-style: italic"></div>
                <div id="progress"></div>
            </td>
        </tr>
        <tr>
            <td colspan="3">
                <label for="cause"><?php echo lang('leaves_create_field_cause');?></label>
                <textarea name="cause"><?php echo set_value('cause'); ?></textarea>

            </td>
        </tr>
    </table>

    <input type="hidden" name="dates" id="dates" value=""/>
    <button name="reqId" value="2" type="button" onclick="req(this.form)" class="btn btn-primary" title="A kiválasztott dátumokat beküldi, mintha egyesével foglaltad volna :)"><i class="mdi mdi-check"></i>&nbsp; <?php echo lang('Requested');?></button>

</form>

<script>
    var baseURL = '<?php echo base_url();?>';
    var userId = <?php echo $user_id; ?>;
    var selectedDates = [];
    var progress = 0;
    var reqList = [];

    $(function(){$('#month,#day').click(function(){calc();})});
    function calc(){

        var t = $("#type").val(), m = $("#month").val(), d = $("#day").val(), year = new Date().getFullYear();
        selectedDates = [];

        if (d == null || m == null)
            return;
        m = m.map(Number);
        d = d.map(Number)

        // intervals
        var intervalLength = [], prev = -1;
        if (d.length > 1) {
            for (q = 0; q < d.length; q++) {
                if (q + 1 < d.length) {

                    if (d[q] + 1 != d[q + 1]) {
                        if (prev != -1)
                            intervalLength[prev] = d[q]-prev;
                        prev = -1;
                    } else {
                        if (prev == -1)
                            prev = d[q];
                    }
                }else{
                    if (prev != -1)
                        intervalLength[prev] = d[q]-prev;
                }
            }
        }
        // console.log(intervalLength);

        let currD = new Date(year, m[0], 1);
        while(currD.getFullYear() == year){
            if(!m.includes(currD.getMonth())){
                if (currD.getMonth() < 11) {
                    currD = new Date(year, currD.getMonth() + 1, 1);
                    continue;
                }else
                    break;
            }

            const rd = currD.getUTCDay();
            if (d.includes(rd)) {
                const inter = intervalLength[rd];
                if (inter !== undefined) {
                    const n = addDays(currD, inter);
                    selectedDates.push([currD, n]);
                    currD = n;
                    // j += inter;
                }else {
                    selectedDates.push([currD, currD]);
                }
            }
            currD = addDays(currD, 1);
        }
        var h = '';
        for(q=0; q<selectedDates.length; q++){
            h += selectedDates[q][0].toLocaleDateString("hu-HU") + "-" + selectedDates[q][1].toLocaleDateString("hu-HU") + "<br/>"; // l[q].getMonth()+1) + '/' + l[q].getDate() + ' ' +
        }
        $('#datesId').html(h);
        // console.log(l);
    }

    function addDays(date, days) {
        var result = new Date(date);
        result.setDate(result.getDate() + days);
        return result;
    }

    function updateProgress(){
        var h = '';
        if (progress < selectedDates.length){
            h = '<img src="'+baseURL + 'assets/images/loading.gif"/> ';
        }else{
            alert("<?=lang('leaves_create_flash_msg_success')?>");
            $('button[name=reqId]').prop('disabled', false);
        }
        h += progress + "/" + selectedDates.length;
        $('#progress').html(h);
    }

    function req(f){

        var t = $("#type").val(), m = $("#month").val(), d = $("#day").val();
        if (t == undefined || d == null || m == null || m.length == 0 || d.length == 0){
            alert('Állítsd be mindhárom paramétert.');
            return;
        }

        $('button[name=reqId]').prop('disabled', true);
        var comment = $('textarea[name=cause]').val(), cId = $("#type").val();
        for(q=0; q<selectedDates.length; q++) {
            reqList.push({
                type: cId,
                startdate: moment(selectedDates[q][0]).format('YYYY-MM-DD'),
                startdatetype: 'Morning',
                enddate: moment(selectedDates[q][1]).format('YYYY-MM-DD'),
                enddatetype: 'Afternoon',
                duration: moment(selectedDates[q][1]).diff(moment(selectedDates[q][0]), 'days') + 1,
                extrainput: '',
                cause: comment,
                status: 2
            });
            // console.log(baseURL + "leaves/create" + " " + JSON.stringify(d));
        }

        $('#dates').val(JSON.stringify(reqList)); //{data:
        f.submit();
    }
</script>
