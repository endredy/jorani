<?php

class CronTest extends CI_Controller {

    /*
     *
összefoglalva:
a user két dolgot állíthat be: a cég melyik szintjétől kezdve küldje + heti (ez hétfő 8-kor) vagy napi (minden hétköznap 8-kor)

a report csak munkanapokat mutatja, és ott is csak azt, ahol van tartalom (szabi, ho, stb)

Ha nincs tartalom, nem küld emailt.
     * */
    function report(){


        // which user wants report?
        $this->load->model('users_model');

        $users = $this->users_model->getUsers();

        foreach ($users as $user){
            $props = json_decode($user['user_properties']);
            if ($props === NULL || !isset($props->report)) continue;
//            var_dump($props);

            $weekly = $props->report->frequency !== 'daily';
            if ($weekly && date('w') !== 1) continue; // weekly report is only on monday

//            echo $user['email']; {"home_office_limit":"2", "report": {"entity": 0, "frequency": "daily"}}
            $message = $this->getHtml($props->report->entity, $weekly);
            if ($message === FALSE) continue; // it's an empty report, we don't send it (no spam :))

            $subject = ($weekly ? 'heti' : 'napi') . ' jelentés';
//echo $subject . ' ' . $message;
            sendMailByWrapper($this, $subject, $message, $user['email'], NULL);
        }
    }

    public function getData($entity_id = 0, $week = true){

        $this->load->model('leaves_model');

        $start = $this->input->get('start', TRUE); //'2024-01-22';
        if ($start === NULL) $start = date("Y-m-d");
        $end = date("Y-m-d", strtotime("+1 " . ($week ? "week" : "day"), strtotime($start)));
        $children = true;
        $statuses = '1|2|3|5';//$this->input->get('statuses');
        $data['start'] = $start;
        $data['leaveInfo'] = $this->leaves_model->department($entity_id, $start, $end, $children, $statuses, '6|7|9|10|11');
        return $data;
    }

    function addLeave2Day(&$arr, &$leave, $day, $type){

        if (!isset($arr[$day]))
            $arr[$day] = [];
        $arr[$day] []= ['name' => $leave->title, 'type' => $type, 'color' => $leave->color, 'textColor' => $leave->textColor, 'class' => $leave->stateClass];
    }


    function printLeave($leave){
        return '<div class="'.$leave['type'].' leave '.$leave['class'].'" style="background-color: '.$leave['color'].'; color: '.$leave['textColor'].'">'.$leave['name'] . '</div>';
    }

    public function getHtml($entity_id = 0, $week = true){
        $d = $this->getData($entity_id, $week);
        $leaveObj = json_decode($d['leaveInfo']);
        $start = $d['start'];

        $daily = [];
        $oneDay = DateInterval::createFromDateString('1 day');

        foreach ($leaveObj as $l){
            $s = new DateTime($l->start);
            $e = new DateTime($l->end);

            $interval = $e->diff($s);
            $days = $interval->format('%a');

            $type = 'full';
            if ($l->startdatetype == 'Morning' && $l->enddatetype == 'Morning')
                $type = 'am';
            else if ($l->startdatetype == 'Afternoon' && $l->enddatetype == 'Afternoon')
                $type = 'pm';

            $day = $s->format("Ymd");

            $this->addLeave2Day($daily, $l, $day, $type);

            if ($days <= 1) continue; /// 1 day leave

            for($i=0; $i<$days-1; $i++){
                $s->add($oneDay);
                $day = $s->format("Ymd");
                $this->addLeave2Day($daily, $l, $day, 'full');
            }

            // end day (it cannot be 'pm', just the 1st half of the day)
            $type = 'full';
            if ($l->enddatetype == 'Morning')
                $type = 'am';
            $day = $e->format("Ymd");
            $this->addLeave2Day($daily, $l, $day, $type);
        }

        if (count($daily) === 0){
            return FALSE;
        }

        $dateH = new DateTime($start);
//        $date = clone $dateH;
//        setlocale(LC_TIME, "hu_HU");
//        $formatter = new IntlDateFormatter(
//            "hu_HU",
//            IntlDateFormatter::FULL,
//            IntlDateFormatter::FULL,
//            'Europe/Budapest'
//        );
//        $formatter->setPattern('Y.MM.dd. EEEE');

        $max = $week ? 7 : 1;
        // <?=$week  ? '' : 'oneday'

        ob_start();
//        var_dump($leaveObj);
?>

        <style>
            .leave{
                padding: 2px 5px;
                margin: 1px;
            }
            .full{
            }
            .am{
                width: 50%;
            }
            .pm{
                width: 50%;
                float: right;
            }

            .header{
                background-color: lightgrey;
            }
            .oneday{
                width: 300px !important;
            }
            .allrequested{
                background-image: url("<?=base_url()?>assets/images/requested.png");
                background-repeat: no-repeat;
                background-position: right;
                /*background-color: rgba(0, 0, 0, 0);*/
                padding-right: 10px;
                background-origin: content-box;
            }

            .table-bordered {
                border:1px solid #ddd;
                border-collapse:separate;
                *border-collapse:collapse;
                border-left:0;
                -webkit-border-radius:4px;
                -moz-border-radius:4px;
                border-radius:4px
            }
            .table-bordered th,
            .table-bordered td {
                border-left:1px solid #ddd
            }
            .table th,
            .table td {
                padding:8px;
                line-height:20px;
                text-align:left;
                vertical-align:top;
                border-top:1px solid #ddd
            }

            .table {
                /*width:100%;*/
                margin-bottom:20px
            }
            .table {
                max-width:100%;
                background-color:transparent;
                border-collapse:collapse;
                border-spacing:0
            }

            /*body {*/
            /*    font-family: "Helvetica Neue",Helvetica,Arial,sans-serif;*/
            /*    font-size: 14px;*/
            /*    line-height: 20px;*/
            /*    color: #333;*/
            /*}*/

        </style>


        <table class="table table-bordered table-condensed oneday">


                <?php for ($i = 1; $i <= $max; $i++) {
                    $day = $dateH->format("Ymd");
                    if (isset($daily[$day])) {
                        echo ' <tr><td class="header">' . /*$formatter->format($dateH)*/$dateH->format('Y. M. d.') . '</td></tr>';
                        echo ' <tr><td>';
                        foreach($daily[$day] as $e){
                            echo $this->printLeave($e);
                        }
                        echo '</td> </tr>';
                    }
                    $dateH->add($oneDay);
                }?>

        </table>


        <?php
        return ob_get_clean();
    }
}


/*
 vertical table


<table class="table table-bordered table-condensed <?=$week ? '' : 'oneday'?>">
    <thead>
    <tr>
        <?php for ($i = 1; $i <= $max; $i++) {
            $day = $dateH->format("Ymd");
            if (isset($daily[$day])) {
                echo '<td>' . $formatter->format($dateH) . '</td>';
            }
            $dateH->add($oneDay);
        }?>
    </tr>
    </thead>
    <tbody>
    <tr>
        <?php for ($i = 1; $i <= $max; $i++) {

            $day = $date->format("Ymd");
            if (isset($daily[$day])){
                echo '<td>';
                foreach($daily[$day] as $e){
                    echo $this->printLeave($e);
                }
                echo '</td>';
            }
            $date->add($oneDay);

        }?>
    </tr>
    </tbody>
</table>

*/
