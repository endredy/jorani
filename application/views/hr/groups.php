
<br/>

<h2><?php echo lang('leaves_hr_concurrent_employees_title');?></h2>
<?php echo lang('leaves_hr_concurrent_employees_desc');?>

<link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<style>
    .ui-front {
        z-index: 1051;
    }
</style>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>

<table class="table table-hover" style="width: 50%">
    <thead>
    <th>#</th>
    <th>tagok</th>
    <th></th>
    </thead>
    <tbody>
<?php
    foreach ($groups as $row) {
        echo '<tr><td>' . $row[0]['groupid'] . '</td><td>';
        $names = [];
        $ids = [];
        foreach ($row as $r) {
            echo '' . $r['lastname'] . ' ' . $r['firstname'] . ', ';
            $names []= $r['lastname'] . ' ' . $r['firstname'];
            $ids []= $r['id'];
        }
        echo '</td><td>';
        echo '<a href="#" class="btn btn-primary" onclick="editGroup('. $row[0]['groupid'].', ['.implode(', ', $ids).'], \''.implode(', ', $names).'\')"><i class="mdi mdi-account-plus"></i>&nbsp;' . lang('hr_overtime_thead_tip_edit') . '</a>';
        echo ' <a href="#" class="btn btn-danger" onclick="if (confirm(\'' . lang('leaves_index_popup_delete_question') . '\')) deleteGroup('. $row[0]['groupid'] . ')"><i class="mdi mdi-account-plus"></i>&nbsp;' . lang('hr_leaves_thead_tip_delete') . '</a>';
        echo '</td></tr>';

    }
?>
    </tbody>
</table>

<a href="#" class="btn btn-primary" onclick="newGroup()"><i class="mdi mdi-account-plus"></i>&nbsp;<?=lang('hr_employees_button_create_user')?></a>

<script>

    var selectedIds = [], groupId = 0;

    function editGroup(gId, ids){

        groupId = gId;
        $( "#selectable li" ).removeClass("ui-selected");
        for(id of ids){
            $( "#selectable li[data="+id+"]").addClass("ui-selected");
        }
        updateSelectedUsers($("#selectable"));
        // $('#names').val(names);
        $('#nameids').val(ids);
        $('#myModal').modal({show: true});
    }


    function newGroup(){

        groupId = 0;
        $( "#selectable li" ).removeClass("ui-selected");
        updateSelectedUsers($("#selectable"));
        $('#myModal').modal({show: true});
    }

    // $( function() {
    //     var tags = [
    //         {value: 1, label: "ActionScript"},
    //         {value: 2, label: "AppleScript"},
    //         {value: 3, label: "Asp"},
    //         {value: 4, label: "BASIC"}
    //     ];
    //     $( "#names" ).autocomplete({
    //         source: function( request, response ) {
    //             var matcher = new RegExp( $.ui.autocomplete.escapeRegex( request.term.split(/[, ]+/).pop() ), "i" );
    //             response( $.grep( tags, function( item ){
    //                 return matcher.test( item.label );
    //             }) );
    //         },
    //         select: function( event, ui ) {
    //             console.log(ui);
    //
    //             const o = $(event.target), id = $('#nameids'), v = o.val() + ' ' + ui.item.label; // le kell szedni az eddig begepeltet
    //             id.val(id.val() + ', ' + ui.item.value);
    //             // o.val(o.val() + ' ' + ui.item.value);
    //             ui.item.value = v;
    //         }
    //     });
    // } );


    function deleteGroup(gId){
        sendGroup('del', gId, []);
    }

    function updateSelectedUsers(parent){
        var result = $( "#nameids" ).val(''), res = $('#select-result').empty();
        selectedIds.length = 0;
        $( ".ui-selected", parent ).each(function(e) {
            var index = $(this).attr('data');
            result.val(result.val() + ', ' + index );
            res.append($(this).html() + ', ');
            selectedIds.push(index);
        });
    }

    function sendGroup(op, groupId, idArr){

        $.ajax({
            url: "<?php echo base_url();?>hr/employees/groups/edit/" + op + "/" + groupId,
            type: "GET",
            data: { ids: idArr}
        }).done(function(d) {
            console.log(d);
            window.location.reload();
        });
    }

    $( function() {
        $( "#selectable" ).selectable({
            stop: function() {
                updateSelectedUsers(this);
            }
        });

        $.ajax({
            url: "<?php echo base_url();?>hr/employees/entity/0/true/all/greater/empty/greater/empty",
            type: "GET",
            data: {}
        }).done(function(d) {
            // console.log(d);
            var html = '';
            for(e of d.data){
                html += '<li class="ui-widget-content" data="' + e.id + '">' + e.firstname + ' ' + e.lastname + '</li>';
            }
            $('#selectable').html(html);
        });
    } );
</script>


<style>
    #feedback { font-size: 1.4em; }
    #selectable .ui-selecting { background: #FECA40; }
    #selectable .ui-selected { background: #F39814; color: white; }
    #selectable { list-style-type: none; margin: 0; padding: 0; width: 60%; }
    #selectable li { margin: 3px; padding: 0.4em; font-size: 1.4em; height: 18px; }
</style>


<!-- Modal -->
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h3 id="myModalLabel"><?php echo lang('leaves_hr_concurrent_employees_edit')?></h3>
    </div>
    <div class="modal-body">
        <p>

            <input type="hidden" name="nameids" id="nameids"/>

            <p id="feedback">
            <span><?php echo lang('leaves_hr_concurrent_employees_selected');?></span>
            <br/>
            <i><span id="select-result" style="color: rgb(128,128,128)">-</span></i>
            </p>

            <ol id="selectable" style="max-height: 320px; overflow-y: auto;">
            </ol>
        </p>
    </div>
    <div class="modal-footer">
        <button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo lang('hr_employees_popup_entity_button_cancel')?></button>
        <button class="btn btn-primary" onclick="sendGroup(groupId === 0 ? 'new' : 'edit', groupId, selectedIds)" data-dismiss="modal"><?php echo lang('hr_employees_popup_entity_button_ok')?></button>
    </div>
</div>


