<?php

namespace StudentPayout;

/**
 * Class Student
 *
 * @package StudentPayout
 */
class Student {

  const ATTENDANCE_DATA = './src/data/attendance.csv';

  const WORKPLACES_DATA = './src/data/workplaces.csv';

  const MEAL            = 5.5;

  const TRAVEL          = 1.09;

  const AGE_UNDER_18    = 72.50;

  const AGE_18_TO_24    = 81.00;

  const AGE_25          = 85.90;

  const AGE_OVER_26     = 90.50;

  /**
   * Read the CSV file given.
   *
   * @param $file
   *   Path to the file.
   *
   * @return array
   *   Return an array containing all the data from the CSV file.
   */
  public function read($file) {
    $filename = $file;
    $results  = [];

    if (($h = fopen("{$filename}", 'rb')) !== FALSE) {
      // The items of the array are comma separated
      while (($data = fgetcsv($h, 500, ",")) !== FALSE) {
        $results[] = $data;
      }
      fclose($h);
    }

    return $results;
  }

  /**
   * Workout the basic rate based on student's age.
   *
   * @param $age
   *
   * @return float|int
   */
  public function basicRate($age) {
    $rate = 0;
    switch ($age) {
      case $age >= 26:
        $rate = self::AGE_OVER_26;
        break;
      case 25:
        $rate = self::AGE_25;
        break;
      case ($age >= 18 && $age < 25):
        $rate = self::AGE_18_TO_24;
        break;
      case $age < 18:
        $rate = self::AGE_UNDER_18;
        break;
      default:
    }

    return $rate;
  }

  /**
   * Given the date of birth in the form of YYYY,MM,DD return the age of a
   * student.
   *
   * @param $dobString
   *
   * @return int
   * @throws \Exception
   */
  public function calculateAge($dobString) {
    $dob       = new \DateTime($dobString);
    $todayDate = new \DateTime('today');

    return $dob->diff($todayDate)->y;
  }

  /**
   * Given the coordinates calculate the distance between two points.
   *
   * @param $x1
   * @param $y1
   * @param $x2
   * @param $y2
   *
   * @return float
   *   return the distance between two points.
   */
  public function distance($x1, $y1, $x2, $y2) {
    return sqrt((($x2 - $x1) ** 2) + (($y2 - $y1) ** 2));
  }

  /**
   * Sum the payout for each student so we have one entry which represent one
   * student record.
   *
   * @param $studentsAllowances
   *
   * @return array
   */
  public function sumPayout($studentsAllowances) {
    $studentTotal = [];
    foreach ($studentsAllowances as $key => $value) {
      $studentTotal[$key] = $key . ', ' . array_sum($value) . '</br>';
    }
    ksort($studentTotal);

    return $studentTotal;
  }

  /**
   * Extract numbers from brackets e.g. (x,y).
   *
   * @param $string
   *   String e.g. (x,y).
   *
   * @return false|string[]
   *   Return and array that has the numbers.
   */
  public function extractNumbers($string) {
    preg_match('#\((.*?)\)#', $string, $match);

    return explode(',', $match[1]);
  }

  /**
   * Given the workplaceID find the workplace location.
   *
   * @param $workplaceID
   *   The workplaceID links records of the student attendance and workplaces.
   *
   * @return false|string[]
   *   Return an array that has workplace location coordinates.
   */
  public function workplaceLocation($workplaceID) {
    $workplaces = $this->read(self::WORKPLACES_DATA);
    foreach ($workplaces as $key => $column) {
      if (($key !== 0) && $workplaceID === $column[0]) {
        return $this->extractNumbers($column[2]);
      }
    }

    return FALSE;
  }

  /**
   * Get the data from the attendance csv and store them in an array then go
   * through each record and calculate the student allowance based on
   * attendance, age, distance from student location to the workplace.
   *
   * @return string
   * @throws \Exception
   */
  public function payout() {
    $studentAttendance  = $this->read(self::ATTENDANCE_DATA);
    $studentsAllowances = [];

    foreach ($studentAttendance as $column) {
      switch ($column[5]) {
        case 'AT':
          $age                  = $this->calculateAge($column[3]);
          $rate                 = $this->basicRate($age);
          $studentCoordinates   = $this->extractNumbers($column[2]);
          $workplaceCoordinates = $this->workplaceLocation($column[4]);
          $travelAllowance      = 0;

          // If distance is 5KM or over then add travel allowance to the student's total allowance
          if ($this->distance($studentCoordinates[0], $studentCoordinates[1], $workplaceCoordinates[0], $workplaceCoordinates[1]) >= 5) {
            $travelAllowance = ($this->distance($studentCoordinates[0], $studentCoordinates[1], $workplaceCoordinates[0], $workplaceCoordinates[1]) * self::TRAVEL) * 2;
          }

          $allowance                        = $rate + self::MEAL + $travelAllowance;
          $studentsAllowances[$column[0]][] = round($allowance, 2);
          break;
        case 'AL':
        case 'CSL':
          $age                              = $this->calculateAge($column[3]);
          $rate                             = $this->basicRate($age);
          $studentsAllowances[$column[0]][] = $rate;
          break;
        case 'USL':
          $studentsAllowances[$column[0]][] = 0;
          break;
      }
    }

    $studentsAllowances = $this->sumPayout($studentsAllowances);

    return implode($studentsAllowances);
  }

  /**
   * Display students payout.
   *
   * @throws \Exception
   */
  public function displayPayout() {
    echo '<div>id, payout</div>';
    echo $this->payout();
  }
}
