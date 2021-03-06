﻿	<script>
		$(document).ready(function(){
			$("#search_btn").click(function(){
				if($("#q").val() == ''){
					alert('검색어를 입력해주세요.');
					return false;
				} else {
					var act = '/bbs/board/lists/ci_board/q/'+$("#q").val()+'/page/1';
					$("#bd_search").attr('action', act).submit();
				}
			});
		});

		function board_search_enter(form) {
			var keycode = window.event.keyCode;
			if(keycode == 13) $("#search_btn").click();
		}
	</script>
	<article id="board_area">
		<header>
			<h1></h1>
		</header>
		<table cellspacing="0" cellpadding="0" class="table table-striped">
			<thead>
				<tr>
					<th scope="col">번호</th>
					<th scope="col">썸네일</th>
					<th scope="col">제목</th>
					<th scope="col">작성자</th>
					<th scope="col">조회수</th>
					<th scope="col">등록일</th>
				</tr>
			</thead>
			<tbody>
<?php
foreach ($list as $lt)
{
?>
				<tr>
					<th scope="row">
						<?php echo $lt->seq_id;?>
					<td><img src="<?php echo $lt->file_path.$lt->file_name;?>" width="100px" height="100px"></td>
					<td><a rel="external" href="/bbs/<?php echo $this->uri->segment(1);?>/view/<?php echo $this->uri->segment(3);?>/board_id/<?php echo $lt->seq_id;?>/page/<?php echo $page;?>"><?php echo $lt->subject;?></a></td>
					<td><?php echo $lt->user_id;?></td>
					<td><?php echo $lt->hit;?></td>
					<td><time datetime="<?php echo mdate("%Y-%M-%j", human_to_unix($lt->reg_date));?>"><?php echo mdate("%M. %j, %Y", human_to_unix($lt->reg_date));?></time></td>
				</tr>
<?php
}
?>

			</tbody>
			<tfoot>
				<tr>
					<th colspan="5"><?php echo $pagination;?></th>
				</tr>
			</tfoot>
		</table>
		<div>
		<p><a href="/bbs/board/write/<?php echo $this->uri->segment(3);?>/page/<?php echo $this->uri->segment(5);?>" class="btn btn-success">쓰기</a></p>
		<p><a href="/bbs/controlls/upload_file" class="btn btn-success">이미지 업로드</a></p>
		</div>
		<div>
			<form method="post" class="well form-search" id="bd_search">
			<?php echo form_hidden($this->security->get_csrf_token_name(), $this->security->get_csrf_hash()); ?>
			<!--form id="bd_search" method="post" class="well form-search" -->
				<i class="icon-search"></i> <input type="text" name="search_word" id="q" onkeypress="board_search_enter(document.q);" class="input-medium search-query" /> <input type="button" value="검색" id="search_btn" class="btn btn-primary" />
			</form>
		</div>
	</article>
