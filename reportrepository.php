<?php
class ReportRepository
{
	private $dc;
	
	function __construct(DataCenter $dataCenter)
	{
		$this->dc = $dataCenter;
	}
	
	function __destruct()
	{
		
    }
	
	
	function GetClassesBySchool( $school )
	{
		$qry = "SELECT SUBSTRING(studing, 1,2) id, REPLACE(SUBSTRING(studing, 1,2), '-', '') classes FROM students where school = '". $school. "' GROUP BY classes";
		
		return $this->dc->ExecuteQuery($qry, true);
	}
	
	
	function GetSectionsByClass( $class )
	{
		$qry = "SELECT SUBSTRING(studing, 3) id, REPLACE(SUBSTRING(studing, 3), '-', '') sections FROM students where studing like '". $class. "%' GROUP BY sections";
		
		return $this->dc->ExecuteQuery($qry, true);
	}
	
	
	function GetStudents( $school, $class, $section, $date )
	{
		$studing = $class . $section;
		
		$qry = "SELECT 
				schoolNo, studentName, school, studing, house, birthDate
				, TIMESTAMPDIFF( YEAR, birthDate, '" . $date . "' ) as years
				, TIMESTAMPDIFF( MONTH, birthDate, '" . $date . "' ) % 12 as months
			FROM
				students 
			WHERE school = '" . $school . "' AND studing LIKE '" . $studing . "%'";
		//echo $qry;
		
		$dTable1 = $this->dc->ExecuteQuery($qry, true);
		
		
		$qry = "SELECT 
				COUNT(*) length
				, house
				, CONCAT(CONCAT(TIMESTAMPDIFF( YEAR, birthDate, '" . $date . "' ) , ' & '), (TIMESTAMPDIFF( YEAR, birthDate, '" . $date . "' )+1)) as years
			FROM
				students 
			WHERE school = '" . $school . "' AND studing LIKE '" . $studing . "%'
			GROUP BY years, house";
		//echo $qry;
		
		$dTable2 = $this->dc->ExecuteQuery($qry, true);
		
		$tSummary = $dTable2->CrossTab( "house", array("years"), "length", array("Total"=>"length") );
		
		//$tSummary->PrintTable();
		
		return array( $dTable1, $tSummary );
	}
	
}
?>