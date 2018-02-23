<?php
declare(strict_types=1);

class Event
{
    public $id;
    public $name;
    public $startDate;
    public $endDate;
    public $attendLimit;
    public $attendArr = array();
    public function __construct($id, $name, $startDate, $endDate, $attendLimit) {
        $this->id          = $id;
        $this->name        = $name;
        $this->startDate   = $startDate;
        $this->endDate     = $endDate;
        $this->attendLimit = $attendLimit;
    }

    public function reserve($user) {
        // 报名人数是否超过限制
        if ($this->attendLimit > $this->getAttendNumber()) {
        	//重复报名
            if (array_key_exists($user->id, $this->attendArr)) {
                throw new EventException('Duplicated reservation', EventException::DUPLICATED_RESERVATION);
            }
            // 使用者报名
            $this->attendArr[$user->id] = $user;

            return true;
        }

        return false;
    }

    public function getAttendNumber() {
        return sizeof($this->attendArr);
    }

    public function unReserve($user) {
        unset($this->attendArr[$user->id]);
    }
}