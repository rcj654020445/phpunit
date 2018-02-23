<?php
use PHPUnit\Framework\TestCase;

final class EventTest extends TestCase
{
	private $event;
	private $user;
	public function setUp() {
        $eventId          = 1;
        $eventName        = '活动1';
        $eventStartDate   = '2016-11-01 18:00:00';
        $eventEndDate     = '2016-11-01 20:00:00';
        $eventAttendLimit = 10;
        $this->event      = new Event($eventId, $eventName, $eventStartDate, $eventEndDate, $eventAttendLimit);

        $userId     = 1;
        $userName   = 'User1';
        $userEmail  = 'user1@zoco.space';
        $this->user = new User($userId, $userName, $userEmail);
    }

    public function tearDown() {
        $this->event = null;
        $this->user  = null;
    }
    

    
    public function testReserve() {
        $this->event->reserve($this->user);
        $expectedNumber = 1;

        // 预期报名人数
        $this->assertEquals($expectedNumber, $this->event->getAttendNumber());

        // 报名清单中有已经报名的人
        $this->assertContains($this->user, $this->event->attendArr);

        return [$this->event, $this->user];
    }


    /**
    * 测试取消报名
    *
    * @param $obj
    * @depends testReserve
    */
    public function testUnReserve($obj) {
        $event = $obj[0];
        $user  = $obj[1];

        // 使用者取消报名
        $event->unReserve($user);

        $unReserveExpectedCount = 0;

        // 预期报名人数
        $this->assertEquals($unReserveExpectedCount, $event->getAttendNumber());

        // 报名清单中没有已经取消报名的人
        $this->assertNotContains($user, $event->attendArr);
    }
    

    
    

    /**
     * 测试报名人数限制
     * @param $eventId
     * @param $eventName
     * @param $eventStartDate
     * @param $eventEndDate
     * @param $eventAttendLimit
     * @dataProvider eventsDataProvider
     */
    public function testAttendeeLimitReserve($eventId, $eventName, $eventStartDate, $eventEndDate, $eventAttendLimit) {
        
        $event      = new Event($eventId, $eventName, $eventStartDate, $eventEndDate, $eventAttendLimit);
        $userNumber = 6;

        // 建立不同使用者报名
        for ($userCount = 1; $userCount < $userNumber; $userCount++) {
            $userId    = $userCount;
            $userName  = 'User ' . $userId;
            $userEmail = 'user' . $userId . '@zoco.space';
            $user      = new User($userId, $userName, $userEmail);

            $reservedResult = $event->reserve($user);

            // 报名人數是否超过
            if ($userCount > $eventAttendLimit) {
                // 无法报名
                $this->assertFalse($reservedResult);
            } else {
                $this->assertTrue($reservedResult);
            }
        }
    }


    public function eventsDataProvider() {
        $eventId                   = 1;
        $eventName                 = '活动1';
        $eventStartDate            = '2016-11-01 12:00:00';
        $eventEndDate              = '2016-11-01 13:00:00';
        $eventAttendeeLimitNotFull = 5;
        $eventAttendeeFull         = 10;

        $eventsData = array(
            array(
                $eventId,
                $eventName,
                $eventStartDate,
                $eventEndDate,
                $eventAttendeeLimitNotFull
            ),
            array(
                $eventId,
                $eventName,
                $eventStartDate,
                $eventEndDate,
                $eventAttendeeFull
            )
        );

        return $eventsData;
    }

    /**
    * @expectedException EventException
    * @expectedExceptionMessage Duplicated reservation
    * @expectedExceptionCode    1
    */
    public function testDuplicatedReservationWithException() {
        // 测试重复报名，预期丢出异常
        // 同一个使用者报名两次
        $this->event->reserve($this->user);
        $this->event->reserve($this->user);
    }



//更新详情  https://phpunit.de/manual/6.5/zh_cn/index.html
}