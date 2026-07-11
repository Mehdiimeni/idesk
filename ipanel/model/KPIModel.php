<?php

namespace ipanel\model;

class KPIModel
{
    private $forwardsTable = 'forwards';
    private $ticketsTable = 'tickets';
    private $adminTable = 'admins';
    private $conn;
    private $workingDays = ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday'];

    private $start_date;
    private $end_date;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function setTimeReport($start = 30, $end = 0)
    {
        $this->start_date = date('Y-m-d', strtotime("-$start days"));
        $this->end_date = date('Y-m-d', strtotime("-$end days"));
    }

    private function calculateWorkingDays($startDate, $endDate)
    {
        $start = new \DateTime($startDate);
        $end = new \DateTime($endDate);

        // تعریف روزهای کاری و ساعات کاری مرتبط
        $workingHours = [
            'Saturday' => ['start' => 8.0, 'end' => 17.5],  // ۱۷:۳۰
            'Sunday' => ['start' => 8.0, 'end' => 17.5],
            'Monday' => ['start' => 8.0, 'end' => 17.5],
            'Tuesday' => ['start' => 8.0, 'end' => 17.5],
            'Wednesday' => ['start' => 8.0, 'end' => 16.5], // ۱۶:۳۰
        ];

        $totalWorkingHours = 0;

        while ($start < $end) {
            $currentDay = $start->format('l'); // نام روز جاری
            $currentHour = (float) $start->format('H.i'); // ساعت جاری به صورت اعشاری

            // اگر روز جاری در روزهای کاری باشد
            if (isset($workingHours[$currentDay])) {
                $workStartHour = $workingHours[$currentDay]['start'];
                $workEndHour = $workingHours[$currentDay]['end'];

                if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
                    // اگر بازه زمانی در همان روز باشد
                    $startHour = max($workStartHour, $currentHour);
                    $endHour = min($workEndHour, (float) $end->format('H.i'));
                    $dailyWorkingHours = max(0, $endHour - $startHour);
                } else {
                    // محاسبه ساعت کاری برای روز جاری
                    $startHour = max($workStartHour, $currentHour);
                    $dailyWorkingHours = max(0, $workEndHour - $startHour);
                }

                $totalWorkingHours += $dailyWorkingHours;
            }

            // به روز بعد برو
            $start->modify('+1 day');
            $start->setTime(0, 0); // ساعت را به ابتدای روز تنظیم کن
        }

        return $totalWorkingHours;
    }

    public function getTicketIdsWithDetailsInMonth($receiver_person_id, $isEntry)
    {
        if ($isEntry) {
            $query = "
                SELECT f.id, f.section_element_id, t.priority, f.creation_date, s.creation_date AS first_forward_creation_date
                FROM " . $this->forwardsTable . " f
                JOIN tickets t ON f.section_element_id = t.id
                LEFT JOIN " . $this->forwardsTable . " s ON f.section_element_id = s.section_element_id 
                    AND s.sender_person_id = ? 
                WHERE f.creation_date BETWEEN ? AND ?";

            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                // Handle error
            }
            $stmt->bind_param("iss", $receiver_person_id, $this->start_date, $this->end_date);
        } else {
            $query = "
                SELECT f.id, f.section_element_id, t.priority, f.creation_date, s.creation_date AS first_forward_creation_date
                FROM " . $this->forwardsTable . " f
                JOIN tickets t ON f.section_element_id = t.id
                LEFT JOIN " . $this->forwardsTable . " s ON f.section_element_id = s.section_element_id 
                    AND s.sender_person_id = ? 
                WHERE f.receiver_person_id = ? 
                  AND f.creation_date BETWEEN ? AND ?";

            $stmt = $this->conn->prepare($query);
            if ($stmt === false) {
                // Handle error
            }
            $stmt->bind_param("iiss", $receiver_person_id, $receiver_person_id, $this->start_date, $this->end_date);
        }


        $stmt->execute();
        $result = $stmt->get_result();

        $totalTickets = $onTimeTickets = $delayedTickets = $manHourWorkingTime = 0;
        $timeDifferences = [];

        while ($row = $result->fetch_assoc()) {

            // محاسبه مجموع ساعت‌های کاری از جدول man_hour
            $manHourQuery = "SELECT SUM(man_hour_number) AS total FROM man_hour WHERE part_id = ? AND admin_id = ? AND creation_date BETWEEN " . $this->start_date . " AND " . $this->end_date;
            $manHourStmt = $this->conn->prepare($manHourQuery);
            $manHourStmt->bind_param("ii", $row['section_element_id'], $receiver_person_id);
            $manHourStmt->execute();
            $manHourResult = $manHourStmt->get_result();
            $manHourWorkingTime += $manHourResult->fetch_assoc()['total'] ?? 0;

            $totalTickets++;
            $first_forward_creation_date = $row['first_forward_creation_date'];

            if ($first_forward_creation_date) {
                // استفاده از calculateWorkingHours برای محاسبه اختلاف ساعت‌ها
                $workingHours = $this->calculateWorkingDays($row['creation_date'], $first_forward_creation_date);
                $timeDifferences[] = $workingHours;  // ذخیره اختلاف ساعت‌ها

                // آستانه‌ها به ساعت تبدیل شده‌اند
                $priorityThresholds = [
                    'high' => 0.08 * 24,   // 0.08 روز معادل با 1.92 ساعت
                    'medium' => 10 * 24,   // 10 روز معادل با 240 ساعت
                    'low' => 22 * 24       // 22 روز معادل با 528 ساعت
                ];
                $threshold = $priorityThresholds[$row['priority']] ?? 0;

                if ($workingHours <= $threshold) {
                    $onTimeTickets++;
                } else {
                    $delayedTickets++;
                }
            }
        }

        // محاسبه درصد انطباق زمان پاسخ‌دهی
        $responseTimeCompliance = $totalTickets ? round(($onTimeTickets / $totalTickets) * 100, 2) : 0;
        // محاسبه درصد تأخیر
        $delayedTimeCompliance = $totalTickets ? round(($delayedTickets / $totalTickets) * 100, 2) : 0;
        // محاسبه میانگین اختلاف زمانی
        $averageTimeDiff = count($timeDifferences) ? array_sum($timeDifferences) / count($timeDifferences) : 0;

        return [
            'response_time_compliance' => $responseTimeCompliance,
            'average_time_difference' => $averageTimeDiff,
            'delayed_time_compliance' => $delayedTimeCompliance,
            'manhour_working_time' => $manHourWorkingTime,
        ];
    }



    public function countAllTicketAdminAssign($admin_id)
    {
        $start_date = $this->start_date;
        $end_date = $this->end_date;

        // ساخت پرس‌وجو با شرط تاریخ‌ها
        $query = "
            SELECT id 
            FROM " . $this->forwardsTable . "
            WHERE receiver_person_id = ?";

        // اضافه کردن شرط تاریخ به پرس‌وجو
        $query .= " AND creation_date BETWEEN ? AND ?";

        // آماده‌سازی پرس‌وجو
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            return ['total' => 0, 'ids' => []];
        }

        // متغیرها را به پارامترهای پرس‌وجو متصل می‌کنیم
        $stmt->bind_param("iss", $admin_id, $start_date, $end_date);

        // اجرای پرس‌وجو
        $stmt->execute();
        $result = $stmt->get_result();

        // دریافت شناسه‌ها
        $ids = [];
        while ($row = $result->fetch_assoc()) {
            $ids[] = $row['id'];
        }

        // بستن statement
        $stmt->close();

        // محاسبه تعداد کل
        $total = count($ids);

        return ['total' => $total, 'ids' => $ids];
    }




    public function countAllTicketAdminConfirmationTest($admin_id)
    {
        $start_date = $this->start_date;
        $end_date = $this->end_date;

        $resultMonth = $this->countAllTicketAdminAssign($admin_id);
        $listIds = $resultMonth['ids'];


        if (empty($listIds)) {
            return 0;
        }

        $placeholders = implode(',', array_fill(0, count($listIds), '?'));

        $query = "
    SELECT COUNT(f.id) as total
    FROM " . $this->forwardsTable . " AS f
    JOIN " . $this->ticketsTable . " AS t
    ON t.id = f.section_element_id
    WHERE f.receiver_person_id = ?
    AND t.status = 'condition_acepted_test'
    AND f.id IN ($placeholders)";

        if ($start_date !== null && $end_date !== null) {
            $query .= " AND f.creation_date BETWEEN ? AND ?";
        }

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return '0';
        }

        $types = str_repeat('i', count($listIds) + ($start_date !== null && $end_date !== null ? 3 : 1));
        $params = array_merge([$admin_id], $listIds);

        if ($start_date !== null && $end_date !== null) {
            $params[] = $start_date;
            $params[] = $end_date;
        }

        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();
        $stmt->close();

        return $reply ? $reply['total'] : 0;

    }

    public function countAllTicketAdminConfirmationTestAuto($admin_id)
    {
        $start_date = $this->start_date;
        $end_date = $this->end_date;


        $resultMonth = $this->countAllTicketAdminAssign($admin_id);
        $listIds = $resultMonth['ids'];

        if (empty($listIds)) {
            return 0; // اگر لیست خالی باشد، 0 بازگردانده می‌شود
        }

        // ساخت مکان‌نماها برای لیست آی‌دی‌ها
        $placeholders = implode(',', array_fill(0, count($listIds), '?'));

        // ساخت کوئری SQL
        $query = "
            SELECT COUNT(f.id) as total
            FROM " . $this->forwardsTable . " AS f
            JOIN " . $this->ticketsTable . " AS t
            ON t.id = f.section_element_id
            WHERE f.receiver_person_id = ?
            AND t.status = 'condition_acepted_test_auto'
            AND f.id IN ($placeholders)";

        if ($start_date !== null && $end_date !== null) {
            $query .= " AND f.creation_date BETWEEN ? AND ?";
        }

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return '0'; // اگر کوئری درست آماده نشود
        }

        // مقداردهی پارامترهای دینامیک
        $params = [$admin_id];
        $types = 'i'; // نوع مقادیر (اعداد صحیح)

        // اضافه کردن آی‌دی‌ها به پارامترها
        foreach ($listIds as $id) {
            $params[] = $id;
            $types .= 'i'; // هر آی‌دی یک عدد صحیح است
        }

        // اضافه کردن تاریخ‌ها در صورت وجود
        if ($start_date !== null && $end_date !== null) {
            $params[] = $start_date;
            $params[] = $end_date;
            $types .= 'ss'; // تاریخ‌ها به صورت رشته
        }

        // اتصال پارامترها به کوئری
        $stmt->bind_param($types, ...$params);

        // اجرای کوئری
        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();
        $stmt->close();

        return $reply ? $reply['total'] : 0;

    }

    public function countAllTicketAdminConfirmationFinalDone($admin_id)
    {
        $start_date = $this->start_date;
        $end_date = $this->end_date;


        $resultMonth = $this->countAllTicketAdminAssign($admin_id);
        $listIds = $resultMonth['ids'];

        if (empty($listIds)) {
            return 0; // اگر لیست خالی باشد، 0 بازگردانده می‌شود
        }

        // ساخت مکان‌نماها برای لیست آی‌دی‌ها
        $placeholders = implode(',', array_fill(0, count($listIds), '?'));

        // ساخت کوئری SQL
        $query = "
            SELECT COUNT(f.id) as total
            FROM " . $this->forwardsTable . " AS f
            JOIN " . $this->ticketsTable . " AS t
            ON t.id = f.section_element_id
            WHERE f.receiver_person_id = ?
            AND t.status = 'condition_final_done'
            AND f.id IN ($placeholders)";

        if ($start_date !== null && $end_date !== null) {
            $query .= " AND f.creation_date BETWEEN ? AND ?";
        }

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return '0'; // اگر کوئری درست آماده نشود
        }

        // مقداردهی پارامترها
        $params = [$admin_id];
        $types = 'i'; // نوع مقادیر (اعداد صحیح)

        // اضافه کردن آی‌دی‌ها به پارامترها
        foreach ($listIds as $id) {
            $params[] = $id;
            $types .= 'i'; // هر آی‌دی یک عدد صحیح است
        }

        // اضافه کردن تاریخ‌ها در صورت وجود
        if ($start_date !== null && $end_date !== null) {
            $params[] = $start_date;
            $params[] = $end_date;
            $types .= 'ss'; // تاریخ‌ها به صورت رشته
        }

        // اتصال پارامترها به کوئری
        $stmt->bind_param($types, ...$params);

        // اجرای کوئری
        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();
        $stmt->close();

        return $reply ? $reply['total'] : 0;

    }


    public function countAllTicketAdminConfirmationDone($admin_id)
    {
        $start_date = $this->start_date;
        $end_date = $this->end_date;


        $resultMonth = $this->countAllTicketAdminAssign($admin_id);
        $listIds = $resultMonth['ids'];

        if (empty($listIds)) {
            return 0; // اگر لیست خالی باشد، 0 بازگردانده می‌شود
        }

        // ساخت مکان‌نماها برای لیست آی‌دی‌ها
        $placeholders = implode(',', array_fill(0, count($listIds), '?'));

        // ساخت کوئری SQL
        $query = "
            SELECT COUNT(f.id) as total
            FROM " . $this->forwardsTable . " AS f
            JOIN " . $this->ticketsTable . " AS t
            ON t.id = f.section_element_id
            WHERE f.receiver_person_id = ?
            AND t.status = 'condition_done'
            AND f.id IN ($placeholders)";

        if ($start_date !== null && $end_date !== null) {
            $query .= " AND f.creation_date BETWEEN ? AND ?";
        }

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return '0'; // اگر کوئری درست آماده نشود
        }

        // مقداردهی پارامترها
        $params = [$admin_id];
        $types = 'i'; // نوع مقادیر (اعداد صحیح)

        // اضافه کردن آی‌دی‌ها به پارامترها
        foreach ($listIds as $id) {
            $params[] = $id;
            $types .= 'i'; // هر آی‌دی یک عدد صحیح است
        }

        // اضافه کردن تاریخ‌ها در صورت وجود
        if ($start_date !== null && $end_date !== null) {
            $params[] = $start_date;
            $params[] = $end_date;
            $types .= 'ss'; // تاریخ‌ها به صورت رشته
        }

        // اتصال پارامترها به کوئری
        $stmt->bind_param($types, ...$params);

        // اجرای کوئری
        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();
        $stmt->close();

        return $reply ? $reply['total'] : 0;

    }


    public function countAllTicketAdminRejectTest($admin_id)
    {
        $start_date = $this->start_date;
        $end_date = $this->end_date;


        $resultMonth = $this->countAllTicketAdminAssign($admin_id);
        $listIds = $resultMonth['ids'];

        if (empty($listIds)) {
            return 0; // اگر لیست خالی باشد، 0 بازگردانده می‌شود
        }

        // ساخت مکان‌نماها برای لیست آی‌دی‌ها
        $placeholders = implode(',', array_fill(0, count($listIds), '?'));

        $query = "
    SELECT COUNT(f.id) as total
    FROM " . $this->forwardsTable . " AS f
    JOIN " . $this->ticketsTable . " AS t
    ON t.id = f.section_element_id
    WHERE f.receiver_person_id = ?
    AND t.status = 'condition_reject_test'
    AND f.id IN ($placeholders)";

        if ($start_date !== null && $end_date !== null) {
            $query .= " AND f.creation_date BETWEEN ? AND ?";
        }

        $stmt = $this->conn->prepare($query);

        if ($stmt === false) {
            return '0'; // اگر کوئری آماده نشد
        }

        // مقداردهی پارامترها
        $params = [$admin_id];
        $types = 'i'; // نوع پارامتر اول (عدد صحیح)

        // اضافه کردن آی‌دی‌ها به پارامترها
        foreach ($listIds as $id) {
            $params[] = $id;
            $types .= 'i'; // آی‌دی‌ها همگی عدد صحیح هستند
        }

        // اضافه کردن تاریخ‌ها در صورت وجود
        if ($start_date !== null && $end_date !== null) {
            $params[] = $start_date;
            $params[] = $end_date;
            $types .= 'ss'; // تاریخ‌ها به صورت رشته
        }

        // اتصال پارامترها به کوئری
        $stmt->bind_param($types, ...$params);

        // اجرای کوئری
        $stmt->execute();
        $result = $stmt->get_result();
        $reply = $result->fetch_assoc();
        $stmt->close();

        return $reply ? $reply['total'] : 0;

    }

    public function calAdminTicketProductivity($isEntry, $resultAssign, $resultDone, $response_time_compliance, $delayed_time_compliance, $resultConfirmationTest, $resultConfirmationTestAuto, $resultFinalDone, $resultRejectTest)
    {
        $totalAssigned = $resultAssign['total'];
        if ($totalAssigned == 0) {
            return [
                'productivity' => 0,
                'kpi' => 0
            ];
        }

        if ($isEntry == 1) {
            $productivity = $response_time_compliance;
            $kpi = ($response_time_compliance / 100)
                - ($delayed_time_compliance / 100);

            return [
                'productivity' => round($productivity, 2),
                'kpi' => round($kpi, 2)
            ];
        }

        // محاسبه تعداد تایید شده‌ها با توجه به اولویت‌ها
        $totalConfirmed = $resultConfirmationTest + $resultFinalDone + $resultConfirmationTestAuto + $resultDone;

        // محاسبه Productivity
        $productivity = ($totalConfirmed / $totalAssigned) * 100;

        $penaltyCoefficient = 0.2;

        // محاسبه KPI با اولویت‌دهی به resultConfirmationTest و resultFinalDone
        $kpi = ($resultConfirmationTest * 2 + $resultFinalDone * 1) / ($totalAssigned * 2)  // resultConfirmationTest بیشتر از باقی
            - ($penaltyCoefficient * ($resultRejectTest / $totalAssigned))
            + ($response_time_compliance / 100)
            - ($delayed_time_compliance / 100);

        // اطمینان از اینکه kpi بین 0 و 1 باشد
        $kpi = max(0, min($kpi, 1));

        return [
            'productivity' => round($productivity, 2),
            'kpi' => round($kpi, 2)
        ];
    }



    ///////////////////////////////////////////////// new kpi



    function getCombinedTicketReport($startDate = null, $endDate = null)
    {
        $startDate = $startDate ?: date('Y-m-d') . ' 00:00:00';
        $endDate = $endDate ?: date('Y-m-d') . ' 23:59:59';

        $sql = "
        SELECT
            r.admin_id,
            r.name,
            r.unit_name,
            r.active_tickets_count,

            /* assigned در بازه */
            (
                SELECT COUNT(DISTINCT f.forward_ticket_id)
                FROM admin_forward_history_view f
                WHERE f.admin_id = r.admin_id
                  AND f.forward_creation_date BETWEEN ? AND ?
            ) AS assigned_tickets_count,

            /* status counts */
            (
                SELECT COUNT(DISTINCT s.section_element_id)
                FROM admin_status_history_view s
                WHERE s.admin_id = r.admin_id
                  AND s.status_name = 'condition_under_review'
                  AND s.creation_date BETWEEN ? AND ?
            ) AS condition_under_review,

            (
                SELECT COUNT(DISTINCT s.section_element_id)
                FROM admin_status_history_view s
                WHERE s.admin_id = r.admin_id
                  AND s.status_name = 'condition_in_progress'
                  AND s.creation_date BETWEEN ? AND ?
            ) AS condition_in_progress,

            (
                SELECT COUNT(DISTINCT s.section_element_id)
                FROM admin_status_history_view s
                WHERE s.admin_id = r.admin_id
                  AND s.status_name = 'condition_need_action'
                  AND s.creation_date BETWEEN ? AND ?
            ) AS condition_need_action,

            (
                SELECT COUNT(DISTINCT s.section_element_id)
                FROM admin_status_history_view s
                WHERE s.admin_id = r.admin_id
                  AND s.status_name = 'condition_done'
                  AND s.creation_date BETWEEN ? AND ?
            ) AS condition_done

        FROM admin_ticket_report_view r
        ORDER BY r.name
    ";

        $stmt = $this->conn->prepare($sql);
        $types = str_repeat('s', 10);

        $params = [
            $startDate,
            $endDate,
            $startDate,
            $endDate,
            $startDate,
            $endDate,
            $startDate,
            $endDate,
            $startDate,
            $endDate
        ];

        $stmt->bind_param($types, ...$params);

        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    function getPriorityCountByDateRange($startDate = null, $endDate = null)
    {

        $startDate = $startDate ?: date('Y-m-d') . ' 00:00:00';
        $endDate = $endDate ?: date('Y-m-d') . ' 23:59:59';
        $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate));

        $query = "SELECT 
                COALESCE(SUM(CASE WHEN ticket_priority = 'high' THEN 1 ELSE 0 END), 0) as high_count,
                COALESCE(SUM(CASE WHEN ticket_priority = 'medium' THEN 1 ELSE 0 END), 0) as medium_count,
                COALESCE(SUM(CASE WHEN ticket_priority = 'low' THEN 1 ELSE 0 END), 0) as low_count,
                COUNT(*) as total_count
              FROM general_ticket_view 
              WHERE ticket_creation_date BETWEEN ? AND ?
                AND ticket_priority IN ('low', 'medium', 'high')";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return [
            'high' => (int) $row['high_count'],
            'medium' => (int) $row['medium_count'],
            'low' => (int) $row['low_count'],
            'total' => (int) $row['total_count']
        ];
    }


    function getTotalTicketsCount($startDate = null, $endDate = null)
    {
        // تنظیم تاریخ‌های پیش‌فرض
        $startDate = $startDate ?: date('Y-m-d') . ' 00:00:00';
        $endDate = $endDate ?: date('Y-m-d') . ' 23:59:59';

        // اطمینان از فرمت صحیح تاریخ
        $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate));

        $query = "SELECT COUNT(*) as total_tickets 
              FROM general_ticket_view 
              WHERE ticket_creation_date >= ? 
                AND ticket_creation_date <= ?";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();

        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        $stmt->close();

        return (int) $row['total_tickets'];
    }


    function getTicketStatusCount($startDate = null, $endDate = null)
    {
        // تنظیم تاریخ‌های پیش‌فرض
        $startDate = $startDate ?: date('Y-m-d') . ' 00:00:00';
        $endDate = $endDate ?: date('Y-m-d') . ' 23:59:59';

        // فرمت صحیح تاریخ
        $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate));

        $query = "SELECT 
                ticket_status as condition_name,
                COUNT(*) as num_tickets
              FROM general_ticket_view 
              WHERE ticket_creation_date >= ?
                AND ticket_creation_date <= ?
                AND ticket_status IS NOT NULL
                AND ticket_status != ''
              GROUP BY ticket_status
              ORDER BY num_tickets DESC, ticket_status ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();

        $result = $stmt->get_result();

        $statusResult = [];

        while ($row = $result->fetch_assoc()) {
            $statusResult[] = [
                'condition_name' => $row['condition_name'],
                'num_tickets' => (int) $row['num_tickets']
            ];
        }

        $stmt->close();

        return $statusResult;
    }


    function getActiveAdminsViewCount($startDate = null, $endDate = null)
    {
        // تنظیم تاریخ‌های پیش‌فرض
        $startDate = $startDate ?: date('Y-m-d') . ' 00:00:00';
        $endDate = $endDate ?: date('Y-m-d') . ' 23:59:59';

        // فرمت صحیح تاریخ
        $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate));

        $query = "SELECT 
                ad.admin_id,
                ad.name as person_name,
                ad.unit_name,
                COUNT(v.person_id) as total_views
              FROM admins_details_view ad
              LEFT JOIN views v 
                ON ad.admin_id = v.person_id
                AND v.creation_date >= ?
                AND v.creation_date <= ?
              WHERE ad.status = 'Active'
              GROUP BY ad.admin_id, ad.name, ad.unit_name
              ORDER BY total_views DESC, ad.name ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bind_param('ss', $startDate, $endDate);
        $stmt->execute();

        $result = $stmt->get_result();
        $data = $result->fetch_all(MYSQLI_ASSOC);

        $stmt->close();

        return $data;
    }


    function getHighPriorityViolatedTickets($startDate = null, $endDate = null)
    {
        // تنظیم تاریخ‌های پیش‌فرض
        $startDate = $startDate ?: date('Y-m-d') . ' 00:00:00';
        $endDate = $endDate ?: date('Y-m-d') . ' 23:59:59';

        // فرمت صحیح تاریخ
        $startDate = date('Y-m-d H:i:s', strtotime($startDate));
        $endDate = date('Y-m-d H:i:s', strtotime($endDate));

        try {
            $status_conditions = [
                'condition_pending',
                'condition_need_action',
                'condition_do_action',
                'condition_under_review',
                'condition_in_progress'
            ];

            $status_list = array_map(function ($status) {
                return strtolower($status);
            }, $status_conditions);

            // تعداد statusها
            $status_count = count($status_list);

            // ساخت placeholders برای statusها
            $placeholders = implode(',', array_fill(0, $status_count, '?'));

            // کوئری با UNION
            $sql = "
(
    -- برای priority = 'high'
    SELECT 
        v.*,
        g.ticket_title,
        g.type_name,
        g.ticket_number,
        g.type_group,
        g.user_name,
        g.company_name,
        g.last_receiver_name,
        1 as priority_order
    FROM tickets_sla_view v
    INNER JOIN grid_ticket_data g ON v.id = g.ticket_id
    WHERE v.priority = 'high'
        AND (
            LOWER(v.status) IN ($placeholders)
            OR v.creation_date BETWEEN ? AND ?
        )
)
UNION ALL
(
    -- برای priority = 'medium'
    SELECT 
        v.*,
        g.ticket_title,
        g.type_name,
        g.ticket_number,
        g.type_group,
        g.user_name,
        g.company_name,
        g.last_receiver_name,
        2 as priority_order
    FROM tickets_sla_view v
    INNER JOIN grid_ticket_data g ON v.id = g.ticket_id
    WHERE v.priority = 'medium'
        AND (
            LOWER(v.status) IN ($placeholders)
            OR (v.sla = 0 AND v.creation_date BETWEEN ? AND ?)
        )
)
UNION ALL
(
    -- برای priority = 'low'
    SELECT 
        v.*,
        g.ticket_title,
        g.type_name,
        g.ticket_number,
        g.type_group,
        g.user_name,
        g.company_name,
        g.last_receiver_name,
        3 as priority_order
    FROM tickets_sla_view v
    INNER JOIN grid_ticket_data g ON v.id = g.ticket_id
    WHERE v.priority = 'low'
        AND (
            LOWER(v.status) IN ($placeholders)
            OR (v.sla = 0 AND v.creation_date BETWEEN ? AND ?)
        )
)
ORDER BY priority_order, creation_date ASC
        ";

            // آماده کردن statement
            $stmt = $this->conn->prepare($sql);

            if (!$stmt) {
                throw new Exception("خطا در آماده‌سازی query: " . $this->conn->error);
            }

            // ساخت آرایه پارامترها:
            // 1. statusها برای بخش اول (3 بار تکرار می‌شود)
            // 2. تاریخ‌ها برای بخش high (1 بار)
            // 3. تاریخ‌ها برای بخش medium (1 بار)
            // 4. تاریخ‌ها برای بخش low (1 بار)

            $params = [];

            // اضافه کردن statusها برای بخش اول (high)
            foreach ($status_list as $status) {
                $params[] = $status;
            }

            // اضافه کردن تاریخ‌ها برای بخش OR در high
            $params[] = $startDate;
            $params[] = $endDate;

            // اضافه کردن statusها برای بخش دوم (medium)
            foreach ($status_list as $status) {
                $params[] = $status;
            }

            // اضافه کردن تاریخ‌ها برای بخش OR در medium
            $params[] = $startDate;
            $params[] = $endDate;

            // اضافه کردن statusها برای بخش سوم (low)
            foreach ($status_list as $status) {
                $params[] = $status;
            }

            // اضافه کردن تاریخ‌ها برای بخش OR در low
            $params[] = $startDate;
            $params[] = $endDate;

            // ساخت رشته types برای bind_param
            // هر status یک 's' نیاز دارد و هر تاریخ یک 's' نیاز دارد
            // تعداد کل: (status_count * 3) + (تاریخ‌ها: 2 * 3) = (status_count * 3) + 6

            $types = str_repeat('s', ($status_count * 3) + 6);

            // استفاده از call_user_func_array برای bind_param با تعداد متغیر پارامترها
            $bind_params = [$types];
            foreach ($params as &$param) {
                $bind_params[] = &$param;
            }

            call_user_func_array([$stmt, 'bind_param'], $bind_params);

            // اجرای کوئری
            $stmt->execute();

            // دریافت نتایج
            $result = $stmt->get_result();
            $results = [];

            while ($row = $result->fetch_assoc()) {
                $results[] = $row;
            }

            $stmt->close();

            $results = array_reverse($results);

            return [
                'success' => true,
                'count' => count($results),
                'data' => $results,
                'start_date' => $startDate,
                'end_date' => $endDate
            ];

        } catch (Exception $e) {
            // مدیریت خطا
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'data' => []
            ];
        }
    }
}

