<?php
/**
 * Created by PhpStorm.
 * User: yesue
 * Date: 2018/7/4
 * Time: 17:28
 */

namespace core\heartbeat;
class main
{
    private $time_lock = null;

    public function __construct()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        while (file_get_contents("core/heartbeat/ifrun.dat")==1) {
        }
        if (file_exists("core/heartbeat/run.lock")) {
            $temp = file_get_contents("core/heartbeat/run.lock");
            if ($temp != $this->time_lock) exit;
            unlink(core / heartbeat / run . lock);
        }
        $this->time_lock = time();
        $fp = fopen('run.lock', 'a+');
        fwrite($fp, $this->time_lock);
        fclose($fp);
        $fp = fopen('a.txt', 'a+');
        fwrite($fp, date("Y-m-d H:i:s",time()));
        fclose($fp);

        sleep(5);
    }
}