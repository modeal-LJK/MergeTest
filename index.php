<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
require_once $_SERVER["DOCUMENT_ROOT"] ."/_sys_/inc/header_v2.php";

?>
<script type="text/javascript">
//<!--
var tmp_list = "#list_box"; //리스트 영역
var page_count = "#page_count"; // 조회 페이징수
var page_num = "#page_num"; // 현재 페이지
var load_proc = false;
var tmp_option_list = "#option_add"; //옵션 영역
var pr_op_idx = "#PR_OP_IDX";

$(document).ready(function() {

    refreshList();
    
	c_code_list();
	
	$("#wForm select").each(function(){
		setaddCode("#wForm #" + $(this).attr("id"));
	})
	//선택
	$("#submenucd").live("change",function(e){
		//e.preventDefault();
		$("#SUB_MENU_CD").val($("#submenucd").val());
		refreshList();
	})

	// 전체 선택
	$("#ckbox_all").live("change",function(e){
		//e.preventDefault();
		if($(this).is(":checked"))
			$(tmp_list).find("input[type=checkbox]").prop("checked",true);
		else
			$(tmp_list).find("input[type=checkbox]").prop("checked",false);
	})
	// list row선택
	$(".check_box").live('click', function(){
		checkRows();
	})
	// 현황 -> 변경: 팝업 active
	$(".btn-pop").live('click', function(){
		pop_view("#modal_html_column");
	})
	
	//조회수 변경
	$("#limit").live("change",function(e){
		//e.preventDefault();
		$(page_count).val($(this).val());
		
		refreshList();
	})
	//검색
	$("#sForm").live("submit",function(e){
		e.preventDefault();
		refreshList();
	})
	//등록
	$(".btn_insert_auth").live("click",function(e){
		e.preventDefault();
		$("#wForm #u_pwd").attr("require","on");
		$("#wForm #u_name").prop("disabled",false);
		$("#wForm  #proc").val("write");
		resultTemplateInit("#wForm");
		$("#wForm  #PR_SOLDOUTN1").prop("checked",true);
		$("#wForm  #PR_ENDN").prop("checked",true);
		pop_view("#modal_ajax");
		idx = 1;
		$( "#tmpl__option_add" ).tmpl(idx).appendTo("#option_add");

	})
	//조회
	$(".btn_view").live("click",function(e){
		e.preventDefault();	

		var frm = "#wForm";

		$(frm + " #proc").val("modify");
//		$(frm + " #NC_CODE").prop("readonly",true);
		resultTemplate(frm,resultTemplatejsonunescape($(this).data("json")));
		//getView($(this).data("idx"));
		$.each($(this).data("json"),function(key,value) {
			if(key =='SUB_MENU_CD'){
				$('#submenu option:eq('+value+')').attr('selected','selected');
			}
		});
		//옵션 정보 가저와서 셋팅 
		var pr_code = $(frm + " #PR_CODE").val();
		getOption(pr_code);
		pop_view("#modal_ajax");
		
	})
	//저장
	$(".btn_save").live("click",function(e){
		e.preventDefault();
		setView();
	})
	//선택삭제
	$(".btn_delete_all").live("click",function(e){
		e.preventDefault();
		delallView();

	})
	//삭제
	$(".btn_delete").live("click",function(e){
		e.preventDefault();
		delView($(this).data("idx"));

	})
	//옵션 추가
	$(".btn-option-add").live("click",function(e){
		e.preventDefault();
		idx = parseInt($(pr_op_idx).val())+1;
		$( "#tmpl__option_add" ).tmpl(idx).appendTo("#option_add");
		$(pr_op_idx).val(parseInt($(pr_op_idx).val())+1);
    })
    
	//파일등록
	$(".btn_file_up").live("click",function(e){
		e.preventDefault();


		var frm = "#sForm3";

		var validate = $(frm + ' [validate]');
		if(fnc_validate(validate) == false){
			return  ;
		}
 
		if(load_proc)
			return;

		load_proc = true;
 
		//결과처리
		var successfuc = function(data)
		{
			load_proc = false;
 
			if(data.code == "0" )
			{
				resultTemplateInit(frm);
				setList();
				getList();

			}
			else
			{
				alert(data.msg)	;
			}



		};
		var ERRfuc = function(data)
		{
			load_proc = false;
			alert("파일을 확인바랍니다.\n .xls파일로 값만 복사하기 하셨나요?\n에러내용:"+data.responseText)	;
		};
		var str_data =  $(frm).serialize();
		var link = $(frm).attr("saction");

		loadfilejson(link ,frm , successfuc,ERRfuc);
		return  ;


	})
 

})

function setaddCode(id)
{
	if($( id).data('code') != ""  && $(id).data('code') != undefined)
		loadSelectCode(  id,$(  id).data('code'),"");

}

//리스트 초기화
function setList(){
	//$(page_count).val($("#limit").val());
	$(page_num).val("1");
	$(tmp_list).html("");
}
//옵션정보조회
function getOption(pr_code){
	var frm = "#oForm";
	$(frm + " #PR_CODE_OP").val(pr_code);
	//결과처리
	var successfuc = function(data)
	{	
			if(data != "" && data.code == "0" && data.list != null)
			{
				$( "#tmpl__option_add_get" ).tmpl(data.list).appendTo("#option_add");
				$(pr_op_idx).val(parseInt(data.total));
			} else {
				idx = $(pr_op_idx).val();
				$( "#tmpl__option_add" ).tmpl(idx).appendTo("#option_add");
			}
		load_proc = false;

	};
	var str_data =  $(frm).serialize();
	var link = $(frm).attr("action");

	loadjson(link ,str_data , successfuc);
	return  ;
}
//리스트조회
function getList(){
	var frm = "#sForm"; // 동작시킬 form 
	var validate = $(frm +' [validate]'); // 유효성 체크 함수 필요없으면 생략해도 됨
	if(fnc_validate(validate) == false){
		return  ;
	}
	if(load_proc)
		return;

	load_proc = true; // 동작 에러 체크를 위한 변수
	//결과처리

	var successfuc = function(data)
	{
		if(data != "")
		{
			//$("#totcount").html(set_comma(data.total));
			//$(tmp_list).html("");
			if (data.list != null){
				
				curcount = Number(document.getElementById('page_num').value) * Number(document.getElementById('page_count').value);
				$( "#tmpl__list" ).tmpl( data.list).appendTo(tmp_list);
				if (curcount >= data.total) {
					document.getElementById('totcount').innerHTML= data.total + "/" + data.total;
					document.getElementById('see_more').style.display = "none";
					
					if (Number(document.getElementById('page_count').value) <= data.total) {
						alert("데이터를 모두 불러왔습니다.")
					}
				}else{
					document.getElementById('totcount').innerHTML= curcount + "/" + data.total;
					document.getElementById('see_more').style = "";
				}
				
			}else{
				$(tmp_list).html($("#tmpl__list_nodata" ).html());
			}
		}

		load_proc = false;
	};
	var str_data =  $(frm).serialize();
	var link = $(frm).attr("action");

	asyncjson(link ,str_data , successfuc); // ajax 동작 - 지정된 url(link)로 데이터(str_data)를 가지고 동작 후 성공여부(successfuc) 리턴
	return  ;
}

// list row 선택
function checkRows(){
	let del_idx = "";
	$(".check_box").each(function(n){
		if($(this).is(':checked')){
			index = n + 1;
			if(del_idx != "") del_idx += ", ";
			del_idx += index;
		}
	});
	console.log(del_idx);
}

//상세정보조회
function getView(idx){
	var frm = "#wForm";

	$(frm + " #u_idx").val(idx);
	$(frm + " #proc").val("modify");
	$(frm + " #u_pwd").attr("require","off");
	$(frm + " #u_name").prop("disabled",true);

	if(load_proc)
		return;

	load_proc = true;

	//결과처리
	var successfuc = function(data)
	{
			if(data != "" && data.code == "0" && data.list != null)
			{
				console.log(frm);
				console.log(data.list);
				resultTemplate(frm,data.list[0]);

			}
		load_proc = false;

	};
	var str_data =  $(frm).serialize();
	var link = $(frm).attr("action");

	loadjson(link ,str_data , successfuc);
	return  ;
}

function refreshList()
{
	setList();
	getList();
}

function seeMore(e){
	//e.preventDefault()

	// id 값으로 컨트롤
	var cur_page = document.getElementById('page_num').value;
	cur_page++;
	document.getElementById('page_num').value = cur_page;

	// name 값으로 컨트롤
	// document.getElementsByName('page_num').value = cur_page;

	/*
	var cur_page2 = $("#page_num").attr("value");
	cur_page2++;
	$("#page_num").attr("value", cur_page2);
	*/
	
	//$("#page_num").val(); // form 안에 있는 객체의 값을 가져올때 
	//console.log(document.getElementById("page_count").value);
	getList();
}


//저장
function setView(){
	var frm = "#wForm";

	var validate = $(frm +' [validate]');
	if(fnc_validate(validate) == false){
		return  ;
	}

	if(load_proc)
		return;

	load_proc = true;

	//결과처리
	var successfuc = function(data)
	{
		load_proc = false;

		if(data.code == "0" )
		{
			pop_close("#modal_ajax");
			refreshList();
		}
		else
		{
			alert(data.msg)	;
		}



	};
	var link = $(frm).attr("saction");
	loadfilejson(link,frm, successfuc);
	return  ;
}
//삭제
function delView(idx){
	var frm = "#wForm";
	$(frm + " #proc").val("delete");
	$(frm + " #u_idx").val(idx);

	if(!confirm("삭제하시겠습니까?"))
		return;

	if(load_proc)
		return;

	load_proc = true;

	//결과처리
	var successfuc = function(data)
	{
		load_proc = false;
		if(data.code == "0" )
		{
			refreshList();

		}
		else
		{
			alert(data.msg)	;
		}


	};
	var str_data =  $(frm).serialize();
	var link = $(frm).attr("saction");

	loadjson(link ,str_data , successfuc);
	return  ;
}

function c_code_list(){
	var frm = "#sForm"; // 동작시킬 form 
	var validate = $(frm +' [validate]'); // 유효성 체크 함수 필요없으면 생략해도 됨
	if(fnc_validate(validate) == false){
		return  ;
	}
	if(load_proc)
		return;

	load_proc = true; // 동작 에러 체크를 위한 변수
	//결과처리
	
	var successfuc = function(data){
		if(data != ""){
			
			if (data.c_list != null){
				var optionhtml = "<option value='선택'>선택</option>";
				for(var c_code_i = 0; c_code_i < data.c_list.length; c_code_i++){
					optionhtml += "<option>"+data.c_list[c_code_i]['maker']+"</option>";
				}
				
				document.getElementById("C_CODE").innerHTML =optionhtml;
			}else{
				document.getElementById("C_CODE").innerHTML ="<option value='선택'>선택</option>";
			}
		}

		load_proc = false;
	};
	var str_data =  $(frm).serialize();
	var link = "/_interface/stock/stock_select_list.php";

	loadjson(link ,str_data , successfuc); // ajax 동작 - 지정된 url(link)로 데이터(str_data)를 가지고 동작 후 성공여부(successfuc) 리턴

}

function m_code_list(){
	var frm = "#sForm"; // 동작시킬 form 
	var validate = $(frm +' [validate]'); // 유효성 체크 함수 필요없으면 생략해도 됨
	if(fnc_validate(validate) == false){
		return  ;
	}
	if(load_proc)
		return;

	load_proc = true; // 동작 에러 체크를 위한 변수
	//결과처리
	
	var successfuc = function(data){
		document.getElementById("M_CODE").innerHTML ="<option value='선택'>선택</option>";
		if(data != ""){
			
			if (data.m_list != null && document.getElementById("C_CODE").value != ""){
				var optionhtml = "<option value='선택'>선택</option>";
				for(var m_code_i = 0; m_code_i < data.m_list.length; m_code_i++){
					optionhtml += "<option>"+data.m_list[m_code_i]['model']+"</option>";
				}
				
				document.getElementById("M_CODE").innerHTML =optionhtml;
			}
		}

		load_proc = false;
	};
	var str_data =  $(frm).serialize();
	var link = "/_interface/stock/stock_select_list.php";

	loadjson(link ,str_data , successfuc); // ajax 동작 - 지정된 url(link)로 데이터(str_data)를 가지고 동작 후 성공여부(successfuc) 리턴

}


//-->
</script>

		<div id="page-wrapper">
            <div class="row">
                <div class="col-xs-12 " style="text-align:center;padding:10px;">
                    <h1>장기렌터카 실시간 재고현황</h1>
                </div>
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                        <div class="panel panel-default">
                            <div class="panel-heading searchbox report">
                            <form method="post" name="sForm" id="sForm" action="/_interface/stock/stock_list_new.php">
                                <input type="hidden" name="page_num" id="page_num" value="1">
                                <input type="hidden" name="page_count" id="page_count" value="50">
                                차량검색 | 
                                <select name="C_CODE" id="C_CODE" class="form-control" onchange="m_code_list()">
                                    <option value="선택">선택</option>
                                </select>
                                <select name="M_CODE" id="M_CODE" class="form-control">
                                    <option value="선택">선택</option>
                                </select>
                                <button type="button" class="btn btn-default" onclick="refreshList();">검색</button>
                                
                            </form>
                            <form method="post" name="sForm3" id="sForm3" action="" saction="/_sys_/_interface/data/data_action.php" enctype="multipart/form-data">
                                </div>
                                <div class="panel-body ">
                                    <div class="form-inline ">
                                        <div class="form-group">
                                            <div class="input-group ">
                                                <span class="input-group-addon">엑셀파일</span>
                                                <input type="file" class="form-control" name="M_EXFILE" id="M_EXFILE" value="" dt="" require="on" validate="etc" msg="엑셀파일">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-default btn_file_up">업로드</button>
                                        <button type="button" class="btn btn-default" onclick="refreshList();">다운로드</button>
                                    </div>
                                </div>
                            </form>
                            <div style="clear:both;"></div> <!-- float left right 등등으로 폼이 겹치게 될때 초기화 -->
                            

                            <!-- /.panel-heading -->
                            <div class="panel-body">
                                <div class="col-xs-9 ag_l " style="padding:10px;">
                                    * 발주대기 차량 한정 리스트 입니다. 발주 전 재고 현황이 달라질 수 있으니 담당자에게 문의하시기 바랍니다. (단위 : 원)
                                </div>
                                <div class="col-xs-3 ag_r" style="padding:10px;">
                                    Lasted Updated : 2020.11.10 14:21:00
                                </div>

                                <div class="table_agr" style="padding:10px;">
                                    <label>전체</label>
                                    <data id="totcount">0</data>건
                                </div>

                                <form method="post" name="lForm" id="lForm" action="" saction="/_interface/promotion/promotion_action_new.php">
								<input type="hidden" name="proc" id="proc" value="deleteall">
								<div class="btn-wrapper">
									<button class="btn btn-default">해제</button>
									<button class="btn btn-default">삭제</button>
								</div>
                                <div class="table-responsive ">
                                    <table class="table table-bordered va_m">
                                        <colgroup>
                                        <col width="100px">
                                        <col width="100px">
                                        <col width="100px">
                                        <col width="100px">
                                        <col width="100px">
                                        <col width="100px">
                                        <col width="100px">
                                        <col width="100px">
                                        <col width="100px">
                                        <col width="100px">
                                        <col width="100px">
                                        <col width="100px">
                                        </colgroup>

                                        <thead>
                                            <tr class="ag_c">
												<td><input id="ckbox_all" type="checkbox"></td>
                                                <td>순번</td>
                                                <td>견적기유형</td>
                                                <td>출고일</td>
                                                <td>개소세기준</td>
                                                <td>제조사</td>
                                                <td>차종</td>
                                                <td>모델</td>
                                                <td>외장색</td>
                                                <td>내장색</td>
                                                <td>옵션</td>
                                                <td>할인</td>
                                                <td>총차량가</td>
                                                <td>차량가</td>
                                                <td>옵션가</td>
                                                <td>제조사탁송료</td>
                                                <td>블박/전면썬팅지원</td>
                                                <td>추가수수료</td>
                                                <td>연식</td>
                                                <td>현황</td>
                                                <td>재고</td>
                                            </tr>
                                        </thead>
                                        <tbody id="list_box">     </tbody>
                                    </table>
                                </div>
                                </form>
                                
                                <div class="dataTables_paginate ag_c" id="see_more">
                                    <div style="">
                                        <button value="100" type="button" class="btn btn-default ag_c" onclick="seeMore();">더보기</button>
                                    </div>
                                </div>
                                <!-- /.table-responsive -->

								<!-- Modal -->
								<div class="modal" id="modal_html_column" tabindex="-1">
									<div class="modal-dialog modal-sm">
										<div class="modal-content">
											<div class="modal-header">
												<button type="button" class="close bt_popClose">×</button>
												<h4 class="modal-title">배정하기</h4>
											</div>
											<div class="modal-body search_modal">
												<table class="table table-striped table-bordered">
													<colgroup>
														<col width="100px">
														<col width="100px">
													</colgroup>
													<tbody>
														<tr>
															<th class="col-xs-3 ag_c">담당자</th>
															<td class="col-xs-9 ag_c">dummy text</td>
														</tr>
														<tr>
															<th class="col-xs-3 ag_c">AG</th>
															<td class="col-xs-9 ag_c">
																<select class="form-control" name="" id="">
																	<option value="">선택하세요</option>
																	<option value=""></option>
																	<option value=""></option>
																</select>
															</td>
														</tr>
														<tr>
															<th class="col-xs-3 ag_c">상태</th>
															<td class="col-xs-9 ag_c">
																<select class="form-control" name="" id="">
																	<option value="">선택하세요</option>
																	<option value=""></option>
																	<option value=""></option>
																</select>
															</td>
														</tr>
														<tr>
															<th class="ag_c">대수</th>
															<td class="ag_c">
																<div class="btn inc">+</div>
																<input type="number" id="count" min="" max="" value="0">
																<div class="btn dec">-</div>
															</td>
														</tr>
													</tbody>
												</table>
												<div class="ag_c">
													<button type="button" class="btn btn-default">취소</button>
													<button type="button" class="btn btn-default">저장</button>
												</div>
											</div>
										</div>
									</div>
								</div>
								<!-- Modal End-->
							</div>
							
                            <!-- Modal -->
                            <div class="modal fade modal-wide" id="modal-wide_ajax" tabindex="-1">
                                <div class="modal-dialog">
                                    <div id="modal_wide_content" class="modal-content full-width" style="position: relative; overflow: hidden">
                                    </div><!-- /.modal-content -->
                                </div>
                                <a href="#" class="bt_popClose"></a>
                            </div>
                            <!-- /.modal -->
                            <!-- Modal -->
                            <div class="modal modal-700" id="modal_ajax" tabindex="-1">
                                <div class="modal-dialog">
                                    <form name="wForm" id="wForm" saction="/_sys_/_interface/promotion/promotion_action_new.php" method="post" enctype="multipart/form-data">
                                    <input type="hidden" name="proc" id="proc" value="write">
                                    <input type="hidden" name="PR_OP_IDX" id="PR_OP_IDX" dt="PR_OP_IDX" value="1">
                                    <div id="modal_content" class="modal-content full-width">
                                        <div class="modal-header">
                                            <button type="button" class="close bt_popClose">×</button>
                                            <h4 class="modal-title" id="myModalLabel">프로모션 </h4>
                                        </div>
                                        <div class="modal-body report_modal">
                                            <div class="panel-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered">
                                                        <colgroup><col width="18%"><col width="*"></colgroup>
                                                        <tbody><tr class="ag_c">
                                                            <td>일련번호</td>
                                                            <td><input type="text" class="form-control" placeholder="" name="PR_CODE" id="PR_CODE" dt="PR_CODE" readonly="" require="off" validate="num" msg="일련번호"></td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>구분</td>
                                                            <td>
                                                                <select name="submenu" id="submenu" style="width:200px;" class="form-control" data-cd="SUB_MENU_CD" data-nm="SUB_MENU_NAME" data-url="/_sys_/_interface/promotion/create_select.php" require="on" validate="etc" msg="프로모션메뉴"><option value="">선택</option><option value="1">핫이슈</option><option value="2">즉시출고</option><option value="3">혜택</option></select>
                                                            </td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>제목</td>
                                                            <td>   <textarea type="text" class="form-control" placeholder="" name="PR_TITLE" id="PR_TITLE" dt="PR_TITLE" require="off" validate="etc" msg="제목" row="3" style="height:100px"></textarea></td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>메인배너<br>312 x 220<br>(핫이슈는 627 x 274)</td>
                                                            <td>
                                                                <div class="input-group ">
                                                                    <df><input type="file" class="form-control" placeholder="" name="MAIN_LIST_IMG" id="MAIN_LIST_IMG" dt="MAIN_LIST_IMG" require="off" validate="etc" msg="메인배너"></df>
                                                                    <imgviewer class="input-group-addon glyphicon glyphicon-file imgfile" dt="MAIN_LIST_IMG"></imgviewer>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>리스트배너<br>627 x 274</td>
                                                            <td>
                                                                <div class="input-group ">
                                                                    <df><input type="file" class="form-control" placeholder="" name="LIST_IMG" id="LIST_IMG" dt="LIST_IMG" require="off" validate="etc" msg="리스트베너"></df>
                                                                    <imgviewer class="input-group-addon glyphicon glyphicon-file imgfile" dt="LIST_IMG"></imgviewer>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>상단이미지</td>
                                                            <td>
                                                                <div class="input-group ">
                                                                    <df><input type="file" class="form-control" placeholder="" name="PR_VIEW_IMG" id="PR_VIEW_IMG" dt="PR_VIEW_IMG" require="off" validate="etc" msg="상세이미지"></df>
                                                                    <imgviewer class="input-group-addon glyphicon glyphicon-file imgfile" dt="PR_VIEW_IMG"></imgviewer>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>상단이미지 링크맵</td>
                                                            <td><textarea class="form-control" placeholder="" name="PR_MAP" id="PR_MAP" dt="PR_MAP" require="off" validate="etc" msg="상단링크맵"></textarea></td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>금융(ex.할부)</td>
                                                            <td><input tyle="text" id="PR_TASK" name="PR_TASK" dt="PR_TASK" value="" class="form-control" msg="금융"></td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>옵션 백그라운드 컬러</td>
                                                            <td><input tyle="text" id="OP_BACK_COLOR" name="OP_BACK_COLOR" dt="OP_BACK_COLOR" value="" class="form-control" msg="옵션 백그라운드 컬러"></td>
                                                        </tr>
                                                        
                                                        <tr>
                                                            <td colspan="2" id="option_add"></td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td colspan="2"><button type="button" class="btn btn-primary btn-option-add" style="width:100%">+ 프로모션 선택 추가</button></td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>하단이미지</td>
                                                            <td>
                                                                <div class="input-group ">
                                                                    <df><input type="file" class="form-control" placeholder="" name="PR_VIEW_IMG2" id="PR_VIEW_IMG2" dt="PR_VIEW_IMG2" require="off" validate="etc" msg="하단이미지"></df>
                                                                    <imgviewer class="input-group-addon glyphicon glyphicon-file imgfile" dt="PR_VIEW_IMG2"></imgviewer>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>하단이미지 링크맵</td>
                                                            <td><textarea class="form-control" placeholder="" name="PR_MAP2" id="PR_MAP2" dt="PR_MAP2" require="off" validate="etc" msg="하단링크맵"></textarea></td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>사용유무</td>
                                                            <td>   <select name="PR_USEYN" id="PR_USEYN" data-code="USEYN" dt="PR_USEYN" class="form-control" require="on" validate="etc" msg="사용유무"><option value="">선택</option><option value="Y" data-cd_etc1="null" data-cd_etc2="null" data-cd_etc3="null" data-cd_etc4="null" data-cd_order="1">사용</option><option value="N" data-cd_etc1="null" data-cd_etc2="null" data-cd_etc3="null" data-cd_etc4="null" data-cd_order="2">미사용</option></select>  </td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>종료여부</td>
                                                            <td class="pr_radio_yn"> 
                                                                <input type="radio" id="PR_ENDY" name="PR_ENDYN" dt="PR_ENDYN" value="Y" class="pr_radio_y">Y
                                                                <input type="radio" id="PR_ENDN" name="PR_ENDYN" dt="PR_ENDYN" value="N" checked="true" class="pr_radio_n">N
                                                            </td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>상담버튼사용유무</td>
                                                            <td>   <select name="SP_USEYN" id="SP_USEYN" data-code="USEYN" dt="SP_USEYN" class="form-control" require="on" validate="etc" msg="상담버튼사용유무"><option value="">선택</option><option value="Y" data-cd_etc1="null" data-cd_etc2="null" data-cd_etc3="null" data-cd_etc4="null" data-cd_order="1">사용</option><option value="N" data-cd_etc1="null" data-cd_etc2="null" data-cd_etc3="null" data-cd_etc4="null" data-cd_order="2">미사용</option></select>  </td>
                                                        </tr>

                                                        <tr class="ag_c">
                                                            <td>메인표시</td>
                                                            <td>   <select name="PR_MAIN" id="PR_MAIN" data-code="USEYN" dt="PR_MAIN" class="form-control" require="on" validate="etc" msg="메인표시"><option value="">선택</option><option value="Y" data-cd_etc1="null" data-cd_etc2="null" data-cd_etc3="null" data-cd_etc4="null" data-cd_order="1">사용</option><option value="N" data-cd_etc1="null" data-cd_etc2="null" data-cd_etc3="null" data-cd_etc4="null" data-cd_order="2">미사용</option></select>  </td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>정렬</td>
                                                            <td>   <input type="text" class="form-control" placeholder="" name="PR_ORDER" id="PR_ORDER" dt="PR_ORDER" require="on" validate="num" msg="정렬"></td>
                                                        </tr>
                                                        <tr class="ag_c">
                                                            <td>메인 정렬</td>
                                                            <td>   <input type="text" class="form-control" placeholder="" name="PR_MAIN_ORDER" id="PR_MAIN_ORDER" dt="PR_MAIN_ORDER" msg="메인정렬"></td>
                                                        </tr>

                                                    </tbody></table>
                                                    <input type="hidden" name="RE_CODE" id="RE_CODE" dt="RE_CODE">
                                                </div>
                                            </div>

                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-default bt_popClose ">Close</button>
                                            <button type="button" class="btn btn-primary btn_save_auth btn_save">저장</button>

                                        </div>
                                    </div><!-- /.modal-content -->
                                    </form>
                                </div>
                                <a href="#" class="bt_popClose"></a>
                            </div>
                            <!-- /.modal -->
                            <!-- /.panel-body -->
                        </div>
                        <!-- /.panel -->


                    </div>
                </div>
                
            </div>
        </div>
        <!-- /#page-wrapper -->
  
<?php require_once $_SERVER["DOCUMENT_ROOT"] ."/_sys_/inc/footer_v2.php";?>


<script id="tmpl__list" type="text/x-jquery-tmpl">
		<tr  class="ag_c">
			<td ><input class="check_box" type="checkbox"></td>
			<td >${seq_no}</td>
			<td >${estm_cd}</td>
			<td >${release_date}</td>
			<td>${tax_cd}</td>
			<td>${maker}</td>
			<td>${model}</td>
			<td>${lineup}</td>
			<td>${ext_color}</td>
			<td>${int_color}</td>
			<td>${options}</td>
			<td>${discount}</td>
			<td>${total_price}</td>
			<td>${car_price}</td>
			<td>${option_price}</td>
			<td>${consignment}</td>
			<td>${goods}</td>
			<td>${add_fee}</td>
			<td>${model_year}</td>
			<td ><button type="button" class="btn btn-default btn-pop">변경</button></td>
			<td>${stock}</td>
		</tr>
</script>
<script id="tmpl__list_nodata" type="text/x-jquery-tmpl">
		<tr>
			<td colspan="100" class="ag_c"> 리스트가 존재하지 않습니다.</td>
		</tr>
</script>
