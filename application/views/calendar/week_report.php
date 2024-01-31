<?php

class CronTest extends CI_Controller {

    /*
     *
summary:
user has 2 settings: which level of the organization will be in the report + weekly (on Monday at 8:00) or daily (each workday at 8:00)

The report shows only workdays, and even where is content (any types of leaves)

If there is no content, no email will be sent.
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


    // email html css must be inline (otherwise some email clients may render it badly)
    function printLeave($leave){
        $requestedStyle = "background-image: url('". base_url() . "assets/images/requested.png'); background-repeat: no-repeat; background-position: right; padding-right: 10px; background-origin: content-box;";
        $am = "width: 50%;";
        $pm = "width: 50%; float: right;";
        $style = "padding: 2px 5px;"; // common
        if ($leave['type'] == 'am')
            $style .= $am;
        else if ($leave['type'] == 'pm')
            $style .= $pm;
        if ($leave['class'] == 'allrequested')
            $style .= $requestedStyle;
        return '<tr><td><div style="' . $style . 'background-color: '.$leave['color'].'; color: '.$leave['textColor'].'">'.$leave['name'] . '</div></td></tr>';
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
            $days = (int)$interval->format('%a') + 1; // 8h = 1 working day

            $type = 'full';
            if ($l->startdatetype == 'Morning' && $l->enddatetype == 'Morning')
                $type = 'am';
            else if ($l->startdatetype == 'Afternoon' && $l->enddatetype == 'Afternoon')
                $type = 'pm';

            $day = $s->format("Ymd");

            $this->addLeave2Day($daily, $l, $day, $type);

            if ($days <= 1) continue; /// 1 day leave

            for($i=0; $i<$days-2; $i++){ // start and end day are added outside of for
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

        <table style="width: 350px !important; border-spacing:1px;">


                <?php for ($i = 1; $i <= $max; $i++) {
                    $day = $dateH->format("Ymd");
                    if (isset($daily[$day])) {
                        echo ' <tr><td style="background-color: lightgrey; color: black; padding: 5px; font-size: 14pt;">' . /*$formatter->format($dateH)*/$dateH->format('Y. M. d.') . '</td></tr>';
                        foreach($daily[$day] as $e){
                            echo $this->printLeave($e);
                        }
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
