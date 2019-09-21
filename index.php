<?php
include("classes/usersession.php");
include("classes/settings.php");
include("reportrepository.php");

$response = -1;


$reportRepo = new ReportRepository ( $dc );

$tSchools = new DataTable();
$tSchools->SetColumns( array("Id", "Value") );
$tSchools->addRow( array("JSD", "Junior School") );
$tSchools->addRow( array("PSD", "Prep School") );
$tSchools->addRow( array("SSD", "Senior School") );

$OptSchools = new ComboBox("OptSchools", "class='form-control'", $tSchools);
$OptSchools->valueField = "Id";
$OptSchools->textField = "Value";

//$qry = "Select * from users";
//$dTable = $dc->ExecuteQuery($qry, true);

//$dTable->PrintTable();

$birthDate = "";

if($_SERVER['REQUEST_METHOD'] == 'POST') {
	$school = $_REQUEST['OptSchools'];
	$class = $_REQUEST['optClasses'];
	$section = $_REQUEST['optSections'];
	$birthDate = $_REQUEST['birthDateHidden'];
	$studing = $class . $section;
	
	//echo "school: " . $school . "<br/>class: " . $class . "<br/>section: " . $section . "<br/>";
	
	
	$dTable = $reportRepo->GetStudents( $school, $class, $section, $birthDate );
	
	$students = $dTable[0];
	$summary = $dTable[1];
}

?>
<!doctype html>

<html>

<?php $title = "Students Reporting"; include("includes.php"); ?>

<style>
.ln_solid {
    border-top: 1px solid #e5e5e5;
    color: #ffffff;
    background-color: #ffffff;
    height: 1px;
    margin: 20px 0;
}
</style>

<body class="nav-md footer_fixed">
    <div class="container body">
      <div class="main_container">
        	
            <div class="row">
              <div class="col-md-12 col-sm-12 col-xs-12">
              	
                <div class="x_panel">
                  <div class="x_content">
                    <br />
                    <div align="center">
                        <h1 style="margin-top:0; color:#0000ff;">Reporting Module</h1>
                        <h3 style="margin-top:0; color:#a52a2a;">Students age difference</h3>
                    </div>
                    <form id="newsForm" method="post" action="index.php" class="form-horizontal form-label-left" >
                        
                        <div class="form-group">
                    	<?php if($response == 0) { ?>
                          <div class="alert alert-danger">
                          	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
						  	<?php echo $message; ?>
                          </div>
                          <?php } else if($response == 1) { ?>
                            <div class="alert alert-success">
                            	<a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
								<?php echo $message; ?>
                            </div>
                      	  <?php } ?>
                         </div>
                         
                         <div align="center">
                            <div class="form-horizontal form-label-left col-md-6 col-sm-6 col-xs-12">
                            
                            
                              <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="OptSchools">Schools</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                  <?php $OptSchools->Bind(); ?>
                                </div>
                              </div>
                              
                              <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="optClasses">Classes</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                	<!--<select id="optClasses" name="optClasses" class="form-control" style='width:100%;'></select>-->
                                 <?php
                            
									//if($OptSchools->SelectedValue() > 0)
									{
										//echo (int)$OptSchools->SelectedValue();
										$school = $OptSchools->SelectedValue();
										if($school == "") $school = "JSD";
										
										$classes = $reportRepo->GetClassesBySchool ( $school );
										
										$row = $classes->NewRow();
										$row["id"] = "";
										$row["classes"] = "All";
										
										$classes->insertRowAt($row, 0);
										
										$optClasses = new ComboBox("optClasses", " class='form-control' style='width:100%;'", $classes);
										$optClasses->valueField = "id";
										$optClasses->textField = "classes";
										$optClasses->Bind();
									}
								?>
							   
                                </div>
                              </div>
                              
                              <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="optSections">Sections</label>
                                <div class="col-md-9 col-sm-9 col-xs-12">
                                	<!--<select id="optSections" name="optSections" class="form-control" style='width:100%;'></select>-->
                                  <?php
                            
									//if($OptSchools->SelectedValue() > 0)
									{
										//echo (int)$OptSchools->SelectedValue();
										$class = $optClasses->SelectedValue();
										if($class == "") $class = "no";
										
										$sections = $reportRepo->GetSectionsByClass ( $class );
										
										$row = $sections->NewRow();
										$row["id"] = "";
										$row["sections"] = "All";
										
										$sections->insertRowAt($row, 0);
										
										$optSections = new ComboBox("optSections", " class='form-control' style='width:100%;'", $sections);
										$optSections->valueField = "id";
										$optSections->textField = "sections";
										$optSections->Bind();
									}
								?>
                                </div>
                              </div>
                              
                              <!--<div class="ln_solid"></div>-->
                              <div class="form-group">
                                <div class="col-md-12 col-sm-12 col-xs-12" align="right">
                                  <button type="submit" id="CmdSubmit" name="CmdSubmit" class="btn btn-primary"> &nbsp;Find&nbsp; </button>
                                  <!--<button type="button" id="CmdCancel" name="CmdCancel" class="btn btn-default">Cancel</button>-->
                                </div>
                              </div>
                            </div>
                            <div class="form-horizontal form-label-right col-md-6 col-sm-6 col-xs-12">
                            	
                              <div class="form-group">
                                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="birthDate">Age as on</label>
                                <div class="col-md-9 col-sm-9 col-xs-12" align="left">
                                  <div id="birthDate" data-date="<?php  if($birthDate=="") echo "09/01/2019"; else echo $func->formatDate($birthDate, "m/d/Y"); ?>"></div>
                                  <input id="birthDateHidden" name="birthDateHidden" type="hidden" value="<?php  if($birthDate=="") echo "2019-09-01"; else echo $birthDate; ?>" />
                                </div>
                              </div>
                            </div>
                          </div>
                  		
                    <!--</form>-->
                    </form>
                	<div class="row">
                    
                      <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_panel">
                        <div class="ln_solid"></div>
                          <div class="x_content">
                            
                            <form id="form1" data-parsley-validate class="form-horizontal form-label-left">
                            	<h3>Quick Summary</h3>
                                <table class="table table-bordered table-hover">
                                  <thead>
                                    <tr>
                                      <th style="width:100px;">Age between</th>
                                      <th style="width:70px;">Students</th>
                                      <?php
									  
									  	if ($summary)
                                        foreach($summary->Columns as $column)
										{
											if($column != "years" && $column != "Total") {
										?>
                                        	<th style="width:60px;"><?php echo $column; ?></th>
                                        <?php
											}
										}
									  ?>
                                      
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?php
                                  if ($summary)
                                  {//$summary->PrintTable();
                                      $index = 1;
                                      foreach($summary->Rows as $row)
                                      {
                                  ?>
                                    <tr>
                                      
                                      <td><strong><?php echo $row->years; ?></strong></td>
                                      <td><?php echo $row->Total; ?></td>
                                      
                                      <?php
                                      foreach ($summary->Columns as $column) {
										  if($column != "years" && $column != "Total") {
											$val = $row[$column];
									  ?>
                                        	<td><?php echo $val; ?></td>
                                      <?php
										  }
									  }
									  ?>
                                      
                                    </tr>
                                    <?php
                                        }
									?>
                                    <tr>
                                    	<td><strong>Total</strong></td>
	                                    <td><strong><?php echo $summary->Compute(array("sum"=>"Total"), ""); ?></strong></td>
	                                    <?php
										  foreach ($summary->Columns as $column) {
											  if($column != "years" && $column != "Total") {
												//$val = $row[$column];
										  ?>
												<td><strong><?php echo $summary->Compute(array("sum"=>$column), ""); ?></strong></td>
										  <?php
											  }
										  }
										  ?>
                                    </tr>
                                    
                                    <?php
									
                                   }
                                    ?>
                                    
                                    
                                  </tbody>
                                </table>
                                <br />
                                <h3>Students List</h3>
                                <table class="table table-bordered table-hover">
                                  <thead>
                                    <tr>
                                      <th style="width:40px; align:center;">#</th>
                                      <th style="width:60px;">School No</th>
                                      <th style="width:130px;">Student Name</th>
                                      <th style="width:60px;">School</th>
                                      <th style="width:60px;">Class</th>
                                      <th style="width:100px;">House</th>
                                      <th style="width:70px;">Date of Birth</th>
                                      <th style="width:60px;">Years</th>
                                      <th style="width:60px;">Months</th>
                                    </tr>
                                  </thead>
                                  <tbody>
                                  <?php
                                  if ($students)
                                  {//$students->PrintTable();
                                      $index = 1;
                                      foreach($students->Rows as $row)
                                      {
                                  ?>
                                    <tr>
                                      <th scope="row"><?php echo $index++; ?></th>
                                      <td><?php echo $row->schoolNo; ?></td>
                                      <td><?php echo $row->studentName; ?></td>
                                      <td><?php echo $row->school; ?></td>
                                      <td><?php echo $row->studing; ?></td>
                                      <td><?php echo $row->house; ?></td>
                                      <td><?php echo $functions->formatDate($row->birthDate, "m/d/Y"); ?></td>
                                      <td><?php echo $row->years; ?></td>
                                      <td><?php echo $row->months; ?></td>
                                      
                                      <!--<td><a href="#" onclick="javascript:onEdit(<?php //echo $row->Id.",".$page;?>)">Edit</a></td>
                                      <td><a href="#" onclick="javascript:onAddDetails(<?php //echo $row->Id;?>)">Add Contents</a></td>
                                      <td><a href="#" onclick="javascript:onViewNews(<?php //echo $row->Id;?>)">View</a></td>-->
                                    </tr>
                                    <?php
                                        }
                                   }
                                    ?>
                                    
                                    <tr style='background-color:#F3F3F8;border-bottom:1px solid lightgray;border-left:1px solid gray;'>
                                        <td colspan="9">
                                        <?php
											//echo "total: ".$totalRecords."<br />";
											//echo "Perpage: ".$recordsPerPage."<br />";
                                            /*include("../paging.php");
                                            $url = $domainName . "news/createnews.php";
											$newsId = $id > 0 ? (int)$id : (int)$TxtEditId;
                                            Paging::Draw($page, $totalRecords, $recordsPerPage, $url, (int)$newsId);*/
                                        ?>
                                        </td>
                                    </tr>
                                  </tbody>
                                </table>
        						
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            

          	<!-- footer content -->
        
        	<?php //include("footer.php"); ?>
        	<!-- /footer content -->
          
        </div>
        <!-- /page content -->


      </div>
    </div>
	<?php include("footerincludes.php"); ?>
    <!-- Flot -->

 <script language="javascript" type="text/javascript">
	
	
$( document ).ready(function() {
	
	$('#birthDate').datepicker({
		todayBtn: "linked",
	    todayHighlight: true,
		defaultViewDate: { year: 2019, month: 08, day: 01 }
	});
	
	$('#birthDate').on('changeDate', function() {
		var d = new Date( $('#birthDate').datepicker('getFormattedDate') );
		
		//$('#birthDateHidden').val( ("0" + d.getDate()).slice(-2) + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + d.getFullYear() );
		
		$('#birthDateHidden').val( d.getFullYear() + "-" + ("0"+(d.getMonth()+1)).slice(-2) + "-" + ("0" + d.getDate()).slice(-2) );
	});
	
	$(function(){

        $("#OptSchools").change(function(){
			//alert($("#optClass").val());
            
			$.post("services/schoolService.php?select=1&school="+$("#OptSchools").val(),function(data){
                $("#optClasses").html(data);
				$("#optSections").html("<option value=''>All</option>");
				//alert(data);
            });
            
        });
		
		
		$("#optClasses").change(function(){
			//alert($("#optClass").val());
            
			$.post("services/schoolService.php?select=2&class="+$("#optClasses").val(),function(data){
                $("#optSections").html(data);
				//alert(data);
            });
            
        });
		
        
		
    });
});

</script>	
	
  </body>
</html>