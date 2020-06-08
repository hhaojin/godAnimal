<?php

namespace Core\Process;

use Core\Helper\FileHelper;
use Swoole\Process;

class HotUpdate
{
    private $md5file;

    public function run()
    {
        $process = new Process(function () {
            while (true) {
                sleep(3);
                $value = FileHelper::getFile(ROOT_PATH . "/App/*", "App/Config");
                if (!$this->md5file) {
                    $this->md5file = $value;
                    continue;
                }
                if (strcmp($this->md5file, $value) !== 0) {
                    echo "热更新" . PHP_EOL;
                    $pidFile = ROOT_PATH . "/Pid/pid.pid";
                    $pid = intval(file_get_contents($pidFile));
                    Process::kill($pid, SIGUSR1);
                    $this->md5file = $value;
                }
            }
        });

        return $process;
    }


}