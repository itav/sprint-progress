<?php
declare(strict_types = 1);

namespace Perform\PrivateProjects\SprintProgress\Domain\Model;

class ProgressCalculator
{
    private $progressFactory;

    public function __construct(ProgressFactory $progressFactory)
    {
        $this->progressFactory = $progressFactory;
    }

    public function calculateProgress(Sprint $sprint, UserStories $userStories): Progress
    {
        $totalStoryPoints = 0;
        $teamPoints = 0;
        foreach ($userStories->all() as $userStory) {
            $totalStoryPoints += $userStory->storyPoints();
        }

        $daysLeft = (int)($sprint->startDate()->diff(new \DateTime('today'))->format('%a'));
        $totalWorkingDays = $this->calculateWorkingDays($sprint->startDate(), $sprint->endDate());
        $rabbitPoints = $daysLeft * $totalStoryPoints / $totalWorkingDays;
        foreach ($userStories->all() as $userStory) {
            $teamPoints += $userStory->burnedStoryPoints();
        }

        $rabbitPercent = $totalStoryPoints ? (int)(round(100 * $rabbitPoints / $totalStoryPoints)) : 100;
        $teamPercent = $totalStoryPoints ? (int)(round(100 * $teamPoints / $totalStoryPoints)) : 100;
        return $this->progressFactory->create($rabbitPercent, $teamPercent);
    }

    public function calculateWorkingDays(\DateTime $startDate, \DateTime $endDate, array $holidays = []): int
    {
        $endDate = strtotime($endDate->format(DATE_ATOM));
        $startDate = strtotime($startDate->format(DATE_ATOM));

        //The total number of days between the two dates. We compute the no. of seconds and divide it to 60*60*24
        //We add one to inlude both dates in the interval.
        $days = ($endDate - $startDate) / 86400 + 1;

        $no_full_weeks = floor($days / 7);
        $no_remaining_days = fmod($days, 7);

        //It will return 1 if it's Monday,.. ,7 for Sunday
        $the_first_day_of_week = date("N", $startDate);
        $the_last_day_of_week = date("N", $endDate);

        //---->The two can be equal in leap years when february has 29 days, the equal sign is added here
        //In the first case the whole interval is within a week, in the second case the interval falls in two weeks.
        if ($the_first_day_of_week <= $the_last_day_of_week) {
            if ($the_first_day_of_week <= 6 && 6 <= $the_last_day_of_week) {
                $no_remaining_days--;
            }
            if ($the_first_day_of_week <= 7 && 7 <= $the_last_day_of_week) {
                $no_remaining_days--;
            }
        } else {
            // (edit by Tokes to fix an edge case where the start day was a Sunday
            // and the end day was NOT a Saturday)

            // the day of the week for start is later than the day of the week for end
            if ($the_first_day_of_week == 7) {
                // if the start date is a Sunday, then we definitely subtract 1 day
                $no_remaining_days--;

                if ($the_last_day_of_week == 6) {
                    // if the end date is a Saturday, then we subtract another day
                    $no_remaining_days--;
                }
            } else {
                // the start date was a Saturday (or earlier), and the end date was (Mon..Fri)
                // so we skip an entire weekend and subtract 2 days
                $no_remaining_days -= 2;
            }
        }

        /** The no. of business days is: (number of weeks between the two dates) * (5 working days) + the remainder
         * february in none leap years gave a remainder of 0 but still calculated weekends between first and last day,
         * this is one way to fix it
         */
        $workingDays = $no_full_weeks * 5;
        if ($no_remaining_days > 0) {
            $workingDays += $no_remaining_days;
        }

        //We subtract the holidays
        foreach ($holidays as $holiday) {
            $time_stamp = strtotime($holiday);
            //If the holiday doesn't fall in weekend
            if ($startDate <= $time_stamp
                && $time_stamp <= $endDate
                && date("N", $time_stamp) != 6
                && date("N", $time_stamp) != 7
            ) {
                $workingDays--;
            }
        }
        return (int)(round($workingDays));
    }
}
